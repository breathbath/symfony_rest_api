<?php

namespace App\Tests\Helpers;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;

trait DbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::bootKernel([]);

        /** @var EntityManager $em */
        $em = static::$container->get('doctrine')->getManager();
        $purger = new ORMPurger();
        $purger->setEntityManager($em);
        $purger->purge();
    }

    protected function tearDown()
    {

    }

    public static function tearDownAfterClass()
    {
        static::ensureKernelShutdown();
    }
}