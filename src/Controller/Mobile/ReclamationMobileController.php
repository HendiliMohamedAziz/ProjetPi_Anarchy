<?php

namespace App\Controller\Mobile;

use App\Entity\Reclamation;
use App\Repository\ArticleRepository;
use App\Repository\ClubRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/mobile/reclamation', name: 'Reclamation_mobile')]
class ReclamationMobileController extends AbstractController
{
    
    #[Route('', methods: 'GET')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();

        if ($reclamations) {
            return new JsonResponse($reclamations, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    
    #[Route('/add', methods: 'POST')]
    public function add(Request           $request,
                        UserRepository    $userRepository,
                        ClubRepository    $clubRepository,
                        ArticleRepository $articleRepository): JsonResponse
    {
        $reclamation = new Reclamation();

        return $this->manage($reclamation, $request, false,
            $userRepository,
            $clubRepository,
            $articleRepository);
    }

    
    #[Route('/edit', methods: 'POST')]
    public function edit(Request           $request, ReclamationRepository $reclamationRepository,
                         UserRepository    $userRepository,
                         ClubRepository    $clubRepository,
                         ArticleRepository $articleRepository): Response
    {
        $reclamation = $reclamationRepository->find((int)$request->get("id"));

        if (!$reclamation) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($reclamation, $request, true,
            $userRepository,
            $clubRepository,
            $articleRepository);
    }

    public function manage($reclamation, $request, $isEdit,
                           UserRepository $userRepository,
                           ClubRepository $clubRepository,
                           ArticleRepository $articleRepository
    ): JsonResponse
    {

        $reclamation->constructor(
            $userRepository->find((int)$request->get("userId")),
            $userRepository->find((int)$request->get("coachId")),
            $clubRepository->find((int)$request->get("clubId")),
            $articleRepository->find((int)$request->get("articleId")),
            DateTime::createFromFormat("d-m-Y", $request->get("date")),
            $request->get("etat"),
            $request->get("reponse"),
            $request->get("type"),
            $request->get("message")
        );


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        return new JsonResponse($reclamation, 200);
    }

    
    #[Route('/delete', methods: 'POST')]
    public function delete(Request $request, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): JsonResponse
    {
        $reclamation = $reclamationRepository->find((int)$request->get("id"));

        if (!$reclamation) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    
    #[Route('/deleteAll', methods: 'POST')]
    public function deleteAll(EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();

        foreach ($reclamations as $reclamation) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }

}
