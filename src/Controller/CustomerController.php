<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Customer;
use App\Form\CustomerType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("/api", name="api_")
 */
class CustomerController extends AbstractFOSRestController
{
    /**
     * Create Customer.
     * @Post("/customers")
     * @param Request $request
     * @return Response
     */
    public function postCustomerAction(Request $request)
    {
        $customer = new Customer();

        return $this->handleCustomerSubmit($customer, $request, 'registration');
    }

    /**
     * Read customers
     * @Get("/customers")
     * @return Response
     */
    public function getCustomersAction()
    {
        $customerRepo = $this->getDoctrine()->getRepository(Customer::class);
        $customers = $customerRepo->findall();

        $view = $this->view($customers, 200);

        return $this->handleView($view);
    }

    /**
     * Change customer
     * @Put("/customers/{id}", requirements={"id" = "\d+"})
     * @param Customer $customer
     * @param Request $request
     * @return Response
     */
    public function changeCustomerAction(Customer $customer, Request $request)
    {
        return $this->handleCustomerSubmit($customer, $request, 'Default');
    }

    /**
     * Change customer
     * @Delete("/customers/{id}", requirements={"id" = "\d+"})
     * @param Customer $customer
     * @return Response
     */
    public function deleteCustomerAction(Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($customer);
        $em->flush();

        $view = $this->view(['status' => 'ok'], 200);
        return $this->handleView($view);
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @param string $validationGroup
     * @return Response
     */
    private function handleCustomerSubmit(Customer $customer, Request $request, string $validationGroup): Response
    {
        $form = $this->createForm(CustomerType::class, $customer, ['validation_groups' => [$validationGroup]]);
        $data = json_decode($request->getContent(), true);

        $form->submit($data, $customer->getId() === null);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }
}