<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    #[Route('/unapproved', name: 'app_coach')]
    public function index(): Response
    {
        return $this->render('coach/coachUnapproved.htm.twig');
    }

    #[Route('/banned', name: 'app_adminBanned')]
    public function ban(): Response
    {
        return $this->render('coach/AdminBanned.htm.twig');
    }


}
