<?php

namespace App\Controller;
use App\Entity\Reservation;
use App\Entity\Seance;
use App\Entity\User;
use App\Form\ReservationType;
use App\Repository\UserRepository;
use App\Repository\ReservationRepository;
use App\Repository\SeanceRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

   #[Route('/client/addreservation', name: 'add_reservation')]
    public function addReservation(ManagerRegistry $doctrine,Request $request): Response
    {
       $token = $this->get('security.token_storage')->getToken();
       $user = $token->getUser();
       $reservation = new Reservation();
      
       $form= $this->createForm(ReservationType::class,$reservation);
       $form->handleRequest($request) ;
       if ($form->isSubmitted() && $form->isValid() ){
        $reservation->setIdUser($user);
        $reservation= $form->getData();
        $seance= $reservation->getSeance();
        if($seance->getNbrGrp()==0){
            $this->addFlash('Erreur', 'Group Complet');
            return $this->redirectToRoute('add_reservation');
        }
        $em= $doctrine->getManager();
        $nbrgrp= $seance->getNbrGrp();
        $nbrgrp--;
        $seance->setNbrGrp($nbrgrp);
        $em->persist($reservation);
        $em->flush();
       
        return  $this->redirectToRoute("list_coach");
    }
       return $this->renderForm("reservation/addreservation.html.twig",array("formreservation"=>$form));
} 


}
