<?php

namespace App\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;

trait DbTestCase
{
    /**
     * @var Client
     */
    protected static $client;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::bootKernel([]);

        /** @var EntityManager $em */
        $em = self::$container->get('doctrine')->getManager();
        $purger = new ORMPurger();
        $purger->setEntityManager($em);
        $purger->purge();

        self::$client = static::createClient([], ['HTTP_HOST' => 'apache']);
    }
}