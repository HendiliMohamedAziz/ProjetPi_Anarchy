<?php

namespace App\Controller;

use App\Form\ForgetPassType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/email', name: 'app_email')]
    public function sendEmail(Request $request,MailerInterface $mailer,UserRepository $repo): Response
    {


        $form = $this->createForm(ForgetPassType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted()){
            $user = $form->getData();
            $users=$repo->findBy(['email' => $user->getEmail()]);
            if($user){
                $email = (new TemplatedEmail())
                ->from('mohamedaziz.hendili@esprit.tn')
                ->to($user->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->htmlTemplate('mailer/mailer.html.twig')
                ->context([
                    'pass' => $users[0]->getPassword(),
                ]);
                $mailer->send($email);
                return $this->redirectToRoute('app_login');
            }
        // ...
        }
        return $this->renderForm('security/forgotPassword.html.twig',[
            "form"=>$form
        ]);
    }
}
