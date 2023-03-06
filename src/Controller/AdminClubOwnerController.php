<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\CreateClubOwnersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminClubOwnerController extends AbstractController
{
    #[Route('/admin/clubOwner/listClubOwner', name: 'app_admin_ListClubOwner')]
    public function index(UserRepository $userRepository): Response
    {
        $clubOwner = $userRepository->findClubOwner();

        return $this->render('admin_club_owner/listClubOwner.html.twig', [
            'clubOwner' => $clubOwner,
        ]);
    }


    #[Route('/admin/clubOwner/addClubOwner', name: 'app_admin_addClubOwner')]
    public function newAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setApproved(true);
        $user->setIsCoach(false);
        $user->setRoles(['ROLE_CLUBOWNER']);
        $form = $this->createForm(CreateClubOwnersType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin_ListClubOwner');
        }
        
        return $this->render('admin_club_owner/registerClubOwner.html.twig', [
            'registrationClubOwnerForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/clubOwner/{id}/removeCubOwner', name: 'remove_clubOwner')]

    public function removeClubOwner(ManagerRegistry $doctrine, $id, UserRepository $repository)
    {
        $admin = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($admin);
        $em->flush();
        return  $this->redirectToRoute("app_admin_ListClubOwner");
    }

    #[Route('/admin/clubOwner/{id}/editClubOwner', name: 'edit_clubOwner')]

    public function editClubOwner(Request $request, User $user): Response
    {
        $form = $this->createForm(CreateClubOwnersType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ListClubOwner');
        }

        return $this->render('admin_club_owner/registerClubOwner.html.twig', [
            'registrationClubOwnerForm' => $form->createView(),
        ]);
    }
}
