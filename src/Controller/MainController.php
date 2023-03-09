<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changelocale($locale,Request $request): Response
    {
        //On stock la langue demandé par la session
        $request->getSession()->set('_locale', $locale);
        
        //On revient sur la page précedente

       return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/calendar', name: 'app_calendar')]
    public function template(): Response
    {
        return $this->render('calendar.html.twig');
    }
}
