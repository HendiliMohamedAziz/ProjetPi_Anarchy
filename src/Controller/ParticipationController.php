<?php

namespace App\Controller;

use App\Entity\Club;
use App\Repository\ClubRepository;
use App\Entity\Participation;
use App\Form\ParticipationType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    #[Route('/participation', name: 'app_participation')]
    public function index(): Response
    {
        return $this->render('participation/index.html.twig', [
            'controller_name' => 'ParticipationController',
        ]);
    }
    //read liste participation selon id du client
    #[Route('/client/listParticipation', name: 'list_participation')]
    public function listParticipation(ParticipationRepository $repository)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $userId = $user->getId();
        $participations = $repository->findByIdClient($userId);
        // $clubs= $this->getDoctrine()->getRepository(ClubRepository::class)->findAll();
        return $this->render("participation/listParticipation.html.twig", array("tabParticipation" => $participations));
    }


    #[Route('/client/addParticipation/{id}', name: 'add_participation')]
    public function addParticipation($id, ParticipationRepository $repository, Request $request, ClubRepository $clubRepository)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $club = $clubRepository->find($id);
        $participation = new Participation();
        $participation->setIdClub($club);
        $participation->setParticipated(false);
        $prixClub = $clubRepository->findPrixClubById($club);
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $participation->setIdUser($user);
            $repository->add($participation, true);
            if ($participation->isParticipated() == true) {
                return $this->redirectToRoute("list_participation");
            } else {
                return $this->render("participation/waitPart.html.twig");
            }
        }
        return $this->renderForm("participation/addParticipation.html.twig", array("formParticipation" => $form, "prix" => $prixClub));
    }

    #[Route('/client/updateParticipation/{id}', name: 'update_participation')]
    public function  updateParticipation($id, ParticipationRepository $repository, ManagerRegistry $doctrine, Request $request)
    {
        $participation = $repository->find($id);
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_participation");
        }
        return $this->renderForm("participation/updateParticipation.html.twig", array("formParticipation" => $form));
    }
    #[Route('/client/removeParticipation/{id}', name: 'remove_participation')]

    public function removeParticipation(ManagerRegistry $doctrine, $id, ParticipationRepository $repository)
    {
        $participation = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($participation);
        $em->flush();
        return  $this->redirectToRoute("list_participation");
    }
    /*
    #[Route('/listeAdherants', name: 'list_adherants')]
    public function listClientsByClubOwner(ParticipationRepository $participationRepository)
    {
        $clients = $participationRepository->findByIdClient(1);
        return $this->render('participation/listAdherantClub.html.twig', [
            'clients' => $clients,
        ]);
    }*/



    //////////////afficher la liste des clients qui ont participe aux clubs selon l id du club owner
    #[Route('/club/owner/ParticipationsAdherants', name: 'particip_adherants')]
    public function listParticipationsByClubOwner(ParticipationRepository $participationRepository)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $userId = $user->getId();
        $participations = $participationRepository->findParticipationsByClubOwner($userId);
        return $this->render('participation/listAdherantClub.html.twig', [
            'participations' => $participations,
        ]);
    }
    ////////////////////////////////////////////remove participation client par clubowner

    #[Route('/club/owner/removeParticipations/{id}', name: 'remove_participationClient')]

    public function removeParticipation1(ManagerRegistry $doctrine, $id, ParticipationRepository $repository)
    {
        $participation = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($participation);
        $em->flush();
        return  $this->redirectToRoute("particip_adherants");
    }

    /////////////////////////////////////////////client is participated approve
    #[Route('/club/owner/isParticipared/{id}', name: 'isParticipated_participationClient')]
    public function participated(Request $request, ParticipationRepository $participationRepository, int $id)
    {
        $participation = $participationRepository->find($id);
        $participation->setParticipated(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($participation);
        $entityManager->flush();

        $this->addFlash('success', 'client is now participated!');

        return $this->redirectToRoute('particip_adherants');
    }
    /////////////////////////////

}
