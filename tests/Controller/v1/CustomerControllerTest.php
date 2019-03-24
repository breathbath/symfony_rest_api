<?php

namespace App\Tests\Controller\v1;

use App\Tests\Controller\DbTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CustomerControllerTest
 * @package App\Tests\Controller\v1
 * @group functional
 */
class CustomerControllerTest extends WebTestCase
{
    use DbTestCase;

    public function testCrud()
    {
        $this->evaluateAdd();

        $customer = $this->evaluateList();

        $this->evaluateOne($customer);

        $this->evaluateChange($customer);

        $this->evaluateDelete($customer);
    }

    private function evaluateAdd()
    {
        $customerInput = [
            'email' => 'no@mail.me',
            'surname' => 'Mc',
            'birthday' => '2009-01-03',
            'name' => 'Yan'
        ];

        self::$client->request('POST', '/v1/customers', [], [], [], json_encode($customerInput));
        $this->assertEquals(201, self::$client->getResponse()->getStatusCode());
    }

    /**
     * @param array $customer
     */
    private function evaluateChange(array $customer)
    {
        $customerInput = [
            'email' => 'newmail@mail.me',
            'surname' => 'NewSurname',
            'birthday' => '2010-01-03',
            'name' => 'NewName'
        ];

        self::$client->request('PUT', '/v1/customers/' . $customer['id'], [], [], [], json_encode($customerInput));
        $this->assertEquals(201, self::$client->getResponse()->getStatusCode());

        $customer = array_merge($customer, $customerInput);
        $this->evaluateOne($customer);
    }

    /**
     * @param array $customer
     */
    private function evaluateDelete(array $customer)
    {
        self::$client->request('DELETE', '/v1/customers/' . $customer['id']);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        $this->evaluateMissing($customer['id']);
    }

    /**
     * @param string $customerId
     */
    private function evaluateMissing(string $customerId)
    {
        self::$client->request('GET', '/v1/customers/' . $customerId);
        $this->assertEquals(404, self::$client->getResponse()->getStatusCode());
    }

    /**
     * @param array $customer
     */
    private function evaluateOne(array $customer)
    {
        self::$client->request('GET', '/v1/customers/' . $customer['id']);
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
        $actualCustomer = json_decode(self::$client->getResponse()->getContent(), true);
        $this->assertEquals($customer, $actualCustomer);
    }

    /**
     * @return array
     */
    private function evaluateList(): array
    {
        self::$client->request('GET', '/v1/customers');
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        $customers = json_decode(self::$client->getResponse()->getContent(), true);
        $this->assertCount(1, $customers);
        $customer = $customers[0];
        $this->assertEquals('no@mail.me', $customer['email']);
        $this->assertEquals('Mc', $customer['surname']);
        $this->assertEquals('2009-01-03', $customer['birthday']);
        $this->assertEquals('Yan', $customer['name']);
        $this->assertNotEmpty($customer['id']);
        $this->assertNotEmpty($customer['uid']);

        return $customer;
    }
}