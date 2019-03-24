<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="default")
     */
    public function defaultAction()
    {
        return $this->handleView($this->view(['error' => 'Not found'], Response::HTTP_NOT_FOUND));
    }
}