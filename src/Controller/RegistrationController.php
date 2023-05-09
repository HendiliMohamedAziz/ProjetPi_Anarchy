<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, SluggerInterface $slugger,UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setApproved(false);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        

        if ($form->isSubmitted()) {
            if ($form->get('isCoach')->getData()) {
                if ($user->getIsCoach()) {
                    $CoachImage = $form->get('Image')->getData();
                    // this condition is needed because the 'brochure' field is not required
                    // so the PDF file must be processed only when a file is uploaded
                    if ($CoachImage) {
                        $originalFilename = pathinfo($CoachImage->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$CoachImage->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $CoachImage->move(
                                $this->getParameter('brochures_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $user->setImage($newFilename);
                    }
                }
                if ($user->getIsCoach()) {
                    $user->setSpecialite($form->get('specialite')->getData());
                }
                if ($user->getIsCoach()) {
                    $user->setExperiance($form->get('experiance')->getData());
                }
                if ($user->getIsCoach()) {
                    $user->setDescription($form->get('description')->getData());
                } 
                

                if (!$user->getImage()) {
                    $form->get('Image')->addError(new FormError('Please provide your Image.'));
                }
                if (!$user->getSpecialite()) {
                    $form->get('specialite')->addError(new FormError('Please provide your coaching specialite.'));
                }
                if (!$user->getExperiance()) {
                    $form->get('experiance')->addError(new FormError('Please provide your coaching experiance.'));
                }
                if (!$user->getDescription()) {
                    $form->get('description')->addError(new FormError('Please provide your coaching experiance with a description.'));
                }    
                
                

                          
                        
            } else {
                // Set coach-specific fields to null
                $user->setCertificates(null);
                $user->setSpecialite(null);
                $user->setExperiance(null);
                $user->setDescription(null);
            }

            if ($form->get('isCoach')->getData()) {
                $user->setRoles(['ROLE_COACH_UNAPPROVED']);
                $user->setApproved(false);
            } else {
                $user->setRoles(['ROLE_CLIENT']);
                $user->setApproved(true);
            }
        }


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

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
