<?php

namespace App\Tests\Command;

use App\Command\ContactRequestCommand;
use App\Entity\ContactRequest;
use App\Errors\ValidationError;
use App\Tests\Helpers\DbTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group integration
 */
class ContactRequestCommandTest extends KernelTestCase
{
    use DbTestCase;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var vfsStreamDirectory
     */
    private $vsDirectoryRoot;

    protected function setUp()
    {
        $application = new Application();
        $command = static::$container->get(ContactRequestCommand::class);
        $application->add($command);

        $command = $application->find(ContactRequestCommand::getDefaultName());
        $this->commandTester = new CommandTester($command);

        $this->vsDirectoryRoot = vfsStream::setup('root');
    }

    public function testImportFile()
    {
        $csvContent = <<<TXT
no@mail.me,some text
no@mail.me,some other text
TXT;

        $csvFile = vfsStream::newFile('test.csv')
            ->withContent($csvContent)
            ->at($this->vsDirectoryRoot);

        $this->commandTester->execute(['file' => $csvFile->url()]);

        /** @var ObjectManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $contactRequestRepo = $em->getRepository(ContactRequest::class);
        $contactRequests = $contactRequestRepo->findAll();
        $this->assertCount(2, $contactRequests);

        $actualContactRequestsArray = [];
        /** @var ContactRequest $contactRequest */
        foreach ($contactRequests as $contactRequest) {
            $actualContactRequestsArray[] = [
                'email' => $contactRequest->getEmail(),
                'message' => $contactRequest->getMessage(),
            ];
        }
        $this->assertEquals(
            [
                [
                    'email' => 'no@mail.me',
                    'message' => 'some text'
                ],
                [
                    'email' => 'no@mail.me',
                    'message' => 'some other text'
                ]
            ],
            $actualContactRequestsArray
        );
    }

    public function testImportNonExistingFile()
    {
        $this->expectExceptionMessage('File "some file" cannot be found');
        $this->expectException(ValidationError::class);
        $this->commandTester->execute(['file' => 'some file']);
    }
}