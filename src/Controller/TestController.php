<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base-front.html.twig');
    }
    #[Route('/aboutus', name: 'about_us')]
    public function aboutus(): Response
    {
        return $this->render('about.html.twig');
    }
}
