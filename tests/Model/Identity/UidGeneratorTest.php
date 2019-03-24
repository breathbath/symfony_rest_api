<?php

namespace App\Tests\Model\Identity;

use App\Model\Identity\UidGenerator;
use PHPUnit\Framework\TestCase;

class UidGeneratorTest extends TestCase
{
    /**
     * @var UidGenerator
     */
    private $uidGenerator;

    protected function setUp()
    {
        $this->uidGenerator = new UidGenerator();
    }

    /**
     * @return array
     */
    public function getUidGenerationDataSets(): array
    {
        return [
            [
                'no@mail.me',
                '2001-01-01 00:00:00',
                '2510329767:2753808910',
            ],
            [
                'some@mail.me',
                '2010-11-11 01:22:30',
                '3810408921:3574960848',
            ]
        ];
    }

    /**
     * @dataProvider getUidGenerationDataSets
     * @param string $email
     * @param string $dateStr
     * @param string $expectedUid
     */
    public function testUidGeneration(string $email, string $dateStr, string $expectedUid)
    {
        $actualResult = $this->uidGenerator->generateUid($email, new \DateTime($dateStr));

        $this->assertEquals($expectedUid, $actualResult);
    }

    public function testUidGenerationFailures()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Empty email value');

        $this->uidGenerator->generateUid('', new \DateTime());
    }
}