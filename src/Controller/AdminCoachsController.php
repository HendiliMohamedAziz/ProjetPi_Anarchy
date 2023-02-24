<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCoachsController extends AbstractController
{
    #[Route('/admin/coachs', name: 'app_admin_coachs')]
    public function index(UserRepository $userRepository): Response
    {
        $coaches = $userRepository->findCoaches();

        return $this->render('admin_coachs/coaches.html.twig', [
            'coaches' => $coaches,
        ]);
    }

    #[Route('/admin/coaches/{id}/approve', name: 'admin_coach_approve')]
    public function approveCoach(Request $request, UserRepository $userRepository, int $id)
{
    $coach = $userRepository->find($id);
    $coach->setApproved(true);
    $coach->setRoles(['ROLE_COACH']);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($coach);
    $entityManager->flush();

    $this->addFlash('success', 'Coach approved!');

    return $this->redirectToRoute('app_admin_coachs');
}

#[Route('/admin/coaches/{id}/removeCoach', name: 'remove_coach')]

public function removeClub(ManagerRegistry $doctrine, $id, UserRepository $repository)
{
    $coach = $repository->find($id);
    $em = $doctrine->getManager();
    $em->remove($coach);
    $em->flush();
    return  $this->redirectToRoute("app_admin_coachs");
}
}
