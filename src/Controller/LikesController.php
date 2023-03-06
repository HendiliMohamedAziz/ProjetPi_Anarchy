<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
class LikesController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator){
        $this->entityManager = $entityManager;
    }
    #[Route('/likes/{id}', name: 'app_likes')]
    public function index($id, ManagerRegistry $doctrine): Response
    {
        //Je vais chercher le User qui m'interesse
        $user = $this->entityManager->getRepository(User::class)->findOneById($id);
        $entityManager = $doctrine->getManager();
        //Je récupère le champ "Likes" pour le modifier
        $likes = $user->getLikes();

        //Pour lui incrémenter un like à chaque clique
        if($likes ==0){
            $plusDeLikes = $likes + 1;
        }
        else {
            $plusDeLikes =0;
        }
        

        //Je mets à jour mon champ la table User
        $user->setLikes($plusDeLikes);
        $entityManager->flush();

        //Je redirige vers la page
        return $this->redirectToRoute('list_coach');
    }
}
