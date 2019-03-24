<?php

namespace App\Controller\v1;

use App\Model\Identity\UidGenerator;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Form\CustomerType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("/customers", name="customers_")
 */
class CustomerController extends AbstractFOSRestController
{
    /**
     * @var UidGenerator
     */
    private $uidGenerator;

    /**
     * CustomerController constructor.
     * @param UidGenerator $uidGenerator
     */
    public function __construct(UidGenerator $uidGenerator)
    {
        $this->uidGenerator = $uidGenerator;
    }

    /**
     * Create Customer.
     * @Post("", name="add")
     * @param Request $request
     * @return Response
     */
    public function postCustomerAction(Request $request)
    {
        $customer = new Customer();

        return $this->handleCustomerSubmit($customer, $request, ['Default', 'registration']);
    }

    /**
     * Read customers
     * @Get("", name="list")
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
     * Read a customer info
     * @Get("/{id}", name="one", requirements={"id" = "\d+"})
     * @param Customer $customer
     * @return Response
     */
    public function getCustomerAction(Customer $customer)
    {
        $view = $this->view($customer, 200);

        return $this->handleView($view);
    }

    /**
     * Change customer
     * @Put("/{id}", requirements={"id" = "\d+"}, name="change")
     * @param Customer $customer
     * @param Request $request
     * @return Response
     */
    public function changeCustomerAction(Customer $customer, Request $request)
    {
        return $this->handleCustomerSubmit($customer, $request, ['Default']);
    }

    /**
     * Change customer
     * @Delete("/{id}", requirements={"id" = "\d+"}, name="delete")
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
     * @param array $validationGroups
     * @return Response
     */
    private function handleCustomerSubmit(Customer $customer, Request $request, array $validationGroups): Response
    {
        $form = $this->createForm(CustomerType::class, $customer, ['validation_groups' => $validationGroups]);
        $data = json_decode($request->getContent(), true);

        $form->submit($data, $customer->getId() === null);

        if ($customer->getUid() === null) {
            $customer->setUid($this->uidGenerator->generateUid($customer->getEmail(), new \DateTime()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }
}