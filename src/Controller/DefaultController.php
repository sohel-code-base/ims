<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(): Response
    {
        return $this->render('pages/login.html.twig');
    }

    /**
     * @return Response
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index()
    {
        return $this->render('pages/dashboard.html.twig');
    }

}
