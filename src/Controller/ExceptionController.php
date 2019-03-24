<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends AbstractFOSRestController
{
    public function errorAction()
    {
        return $this->handleView($this->view(['error' => 'Not found'], Response::HTTP_NOT_FOUND));
    }
}