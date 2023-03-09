<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface ;
use App\Repository\ArticleRepository;

class ListFavorisController extends AbstractController
{
    #[Route('/list_favoris', name: 'app_list_favoris')]
    public function index(SessionInterface $session , ArticleRepository $ArticleRepository)
    {
        $list = $session->get('list', []); 

        $listWithData = []; 

        foreach ($list as $id => $quantity) {

         $listWithData[] = ['Article' => $ArticleRepository->find($id) , 'quantity' => $quantity ];
    }
        return $this->render('list_favoris/index.html.twig', [
            'controller_name' => 'ListFavorisController',
            'items' => $listWithData

        ]);
    }
    #[Route('/list/add/{id} ', name: 'list_add')]

    public function add($id, SessionInterface $session) {

        $list = $session->get('list', []);
        if(empty($list[$id]))
        {
            $list[$id] = 1; 
        }

        $session->set('list', $list);

        return $this->redirectToRoute("app_list_favoris");
    }
    #[Route('/list/remove/{id} ', name: 'list_remove')]

    public function remove($id, SessionInterface $session) {
     $list = $session->get('list', []); 
        if (!empty($list[$id]))
        {
            unset($list[$id]); 
        } 
         
        $session->set('list', $list);

         return $this->redirectToRoute("app_list_favoris");
    }  
}
