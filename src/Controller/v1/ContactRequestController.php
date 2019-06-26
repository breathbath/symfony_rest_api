<?php

namespace App\Controller\v1;

use App\Entity\ContactRequest;
use App\Errors\ValidationError;
use App\Service\ContactRequestFacade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("/contact-requests", name="contact_requests_")
 */
class ContactRequestController extends AbstractFOSRestController
{
    /**
     * @var ContactRequestFacade
     */
    private $contactRequestFacade;

    /**
     * @param ContactRequestFacade $contactRequestFacade
     */
    public function __construct(ContactRequestFacade $contactRequestFacade)
    {
        $this->contactRequestFacade = $contactRequestFacade;
    }

    /**
     * Create ContactRequest.
     * @Post("", name="add")
     * @param Request $request
     * @return Response
     */
    public function postCustomerAction(Request $request)
    {
        return $this->handleSubmit($request);
    }

    /**
     * Read contact requests
     * @Get("", name="list")
     * @return Response
     */
    public function getCustomersAction()
    {
        $contactRequestRepo = $this->getDoctrine()->getRepository(ContactRequest::class);
        $contactRequests = $contactRequestRepo->findall();

        $view = $this->view($contactRequests, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function handleSubmit(Request $request): Response
    {
        try {
            $this->contactRequestFacade->handleJsonRequest($request->getContent());
        } catch (ValidationError $validationError) {
            return $this->handleView($this->view(['error' => $validationError->getMessage()], Response::HTTP_BAD_REQUEST));
        } catch (\Exception $e) {
            return $this->handleView($this->view(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }
}