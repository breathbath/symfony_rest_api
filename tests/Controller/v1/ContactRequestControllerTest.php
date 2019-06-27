<?php

namespace App\Tests\Controller\v1;

use App\Entity\ContactRequest;
use App\Tests\Helpers\DbTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package App\Tests\Controller\v1
 * @group integration
 */
class ContactRequestControllerTest extends WebTestCase
{
    use DbTestCase;

    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient([], ['HTTP_HOST' => 'apache']);
    }

    public function testAddAndList()
    {
        $input = [
            'email' => 'no@mail.me',
            'message' => 'Some text 213',
        ];

        $this->client->request('POST', '/v1/contact-requests', [], [], [], json_encode($input));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        /** @var ObjectManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $contactRequestRepo = $em->getRepository(ContactRequest::class);
        $contactRequests = $contactRequestRepo->findBy(['email' => 'no@mail.me']);
        $this->assertCount(1, $contactRequests);
        $this->assertEquals('Some text 213', $contactRequests[0]->getMessage());
        $now = new \DateTime();
        $this->assertTrue($now >=$contactRequests[0]->getCreatedAt());

        $anotherContactRequest = new ContactRequest();
        $anotherContactRequest->setEmail('my@mail.me');
        $anotherContactRequest->setMessage('Some other text');
        $anotherContactRequest->setCreatedAt($now);
        $em->persist($anotherContactRequest);
        $em->flush();

        $this->client->request('GET', '/v1/contact-requests');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $contactRequests = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $contactRequests);

        $actualContactRequestsArray = [];
        foreach ($contactRequests as $contactRequest) {
            $actualContactRequestsArray[] = [
                'email' => $contactRequest['email'],
                'message' => $contactRequest['message'],
            ];
        }

        $this->assertEquals(
            [
                [
                    'email' => 'no@mail.me',
                    'message' => 'Some text 213',
                ],
                [
                    'email' => 'my@mail.me',
                    'message' => 'Some other text',
                ]
            ],
            $actualContactRequestsArray
        );
    }
}