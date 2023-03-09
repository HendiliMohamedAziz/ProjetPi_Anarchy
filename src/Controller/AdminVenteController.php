<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminVenteController extends AbstractController
{
    #[Route('/adminvente', name: 'app_admin_vente')]
    public function index(): Response
    {
        return $this->render('AdminVente.html.twig', [
            'controller_name' => 'AdminVenteController',
        ]);
    }
}
