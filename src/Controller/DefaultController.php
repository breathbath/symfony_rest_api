<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;

class DefaultController extends AbstractFOSRestController
{
    /**
     * @Get("/", name="default")
     */
    public function defaultAction()
    {
        return $this->handleView($this->view(['error' => 'Not found'], Response::HTTP_NOT_FOUND));
    }
}