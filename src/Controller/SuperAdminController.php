<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateAdminsType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{   
    #[Route('/super/admin/ListAdmins', name: 'app_list_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findAdmins();

        return $this->render('super_admin/listAdmins.html.twig', [
            'admins' => $admins,
        ]);
    }


    #[Route('/super/admin/addAdmin', name: 'app_super_admin_addAdmin')]
    public function newAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setApproved(true);
        $user->setIsCoach(false);
        $form = $this->createForm(CreateAdminsType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($form->getData()->getRoles());
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

            return $this->redirectToRoute('app_list_admin');
        }
        
        return $this->render('super_admin/registerAdmin.html.twig', [
            'registrationAdminForm' => $form->createView(),
        ]);
    }

    #[Route('/super/admin/admins/{id}/removeAdmin', name: 'remove_admins')]

    public function removeAdmins(ManagerRegistry $doctrine, $id, UserRepository $repository)
    {
        $admin = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($admin);
        $em->flush();
        return  $this->redirectToRoute("app_list_admin");
    }

    #[Route('/super/admin/admins/{id}/editAdmin', name: 'edit_admins')]

    public function editUser(Request $request, User $user): Response
    {
        $form = $this->createForm(CreateAdminsType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_list_admin');
        }

        return $this->render('super_admin/registerAdmin.html.twig', [
            'registrationAdminForm' => $form->createView(),
        ]);
    }

    #[Route('/super/admin/admins/{id}/banAdmin', name: 'admin_ban')]
    public function banAdmin(Request $request, UserRepository $userRepository, int $id)
    {
        $admin = $userRepository->find($id);
        $admin->setApproved(false);
        if (in_array('ROLE_ADMIN_COACH', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_COACH_banned']);
        }
        if (in_array('ROLE_ADMIN_CLUBOWNER', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_CLUBOWNER_banned']);
        }
        if (in_array('ROLE_ADMIN_RECLAMATION', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_RECLAMATION_banned']);
        }
        if (in_array('ROLE_ADMIN_PRDOUIT', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_PRDOUIT_banned']);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        $this->addFlash('success', 'admin banned!');

        return $this->redirectToRoute('app_list_admin');
    }
    #[Route('/super/admin/admins/{id}/unbanAdmin', name: 'admin_unban')]
    public function unbanAdmin(Request $request, UserRepository $userRepository, int $id)
    {
        $admin = $userRepository->find($id);
        $admin->setApproved(true);
        if (in_array('ROLE_ADMIN_COACH_banned', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_COACH']);
        }
        if (in_array('ROLE_ADMIN_CLUBOWNER_banned', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_CLUBOWNER']);
        }
        if (in_array('ROLE_ADMIN_RECLAMATION_banned', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_RECLAMATION']);
        }
        if (in_array('ROLE_ADMIN_PRDOUIT_banned', $admin->getRoles())) {
            $admin->setRoles(['ROLE_ADMIN_PRDOUIT']);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        $this->addFlash('success', 'admin unbanned!');

        return $this->redirectToRoute('app_list_admin');
    }
}
