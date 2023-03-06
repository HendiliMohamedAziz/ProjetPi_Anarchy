<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Seance;
use App\Form\SeanceType;
use App\Entity\Reservation;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ReservationContactType;
use App\Repository\SeanceRepository;
use function PHPUnit\Framework\countOf;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport\SendmailTransport;

use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SeanceController extends AbstractController
{
    #[Route('/seance', name: 'app_seance')]
    public function index(): Response
    {
        return $this->render('seance/index.html.twig', [
            'controller_name' => 'SeanceController',
        ]);
    }
    #[Route('/backend', name: 'app_template')]
    public function template(): Response
    {
        return $this->render('template.html.twig');
    }
    #[Route('/addseance', name: 'add_seance')]
    public function addSeance(ManagerRegistry $doctrine,Request $request)
    {
        $seance= new Seance;
        $form= $this->createForm(SeanceType::class,$seance);
        $form->handleRequest($request) ;
        if ($form->isSubmitted() && $form->isValid()){
             $em= $doctrine->getManager();
             $em->persist($seance);
             $em->flush();
             return  $this->redirectToRoute("list_seance");
         }
        return $this->renderForm("seance/addseance.html.twig",array("formSeance"=>$form));
    
}

#[Route('/listcoach', name: 'list_coach')]
    public function listCoach(UserRepository $repository,Request $request,TransportInterface $mailer,ManagerRegistry $doctrine)
    {
        $coach = $repository->findAll();
        
        $form = $this->createForm(ReservationContactType::class);
        $contact = $form->handleRequest($request);
    
        
        if($form->isSubmitted() && $form->isValid()){
            $user=$this->getDoctrine()->getRepository(User::class)->find(2);
     
            $emailUser=$user->getEmail();
            
            $email= (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to($emailUser)
            ->subject('Contact au sujet du séance de coaching')
            ->htmlTemplate('emails/contact_coach.html.twig')
            ->context([
                'title'=>$contact->get('title')->getData(),
                'mail'=> $contact->get('email')->getData(),
                'message'=> $contact->get('message')->getData()
            ]);
           
            $mailer->Send($email);
            $this->addFlash('message', 'Votre e-mail a bien été envoyé');
            return $this->redirectToRoute('list_coach');

        }
        return $this->render('seance/listcoach.html.twig', array("tabcoach" => $coach,
    'form'=>$form->createView()));
    } 
//---------------ListSeance------------
    #[Route('/listseance', name: 'list_seance')]
    public function listSeance(SeanceRepository $repository,Request $request)
    {
        //on cherche le num de la page dans l'url
        $page = $request->query->getInt('page', 1);
        $seance = $repository->ListsancePaginated($page,2);
        $sortByGrp= $repository->sortByGrp();
        return $this->render('seance/listeseance.html.twig', array("tabseance" => $seance,
        "sortByGrp"=>$sortByGrp,));
    } 

    #[Route('/updateseance/{id}', name: 'update_seance')]
    public function  updateCoach($id,SeanceRepository $repository,ManagerRegistry $doctrine,Request $request)
    {
        $seance= $repository->find($id);
        $form= $this->createForm(SeanceType::class,$seance);
        $form->handleRequest($request) ;
        if ($form->isSubmitted() && $form->isValid()){
            $em= $doctrine->getManager();
            $em->flush();
            $this->addFlash('message', 'Séance modifiée avec succés');
            return  $this->redirectToRoute("list_seance");
        }
        return $this->renderForm("seance/updateseance.html.twig",array("formUpdate"=>$form));
    }

     //-----------------SupprimerSeance--------------
     #[Route('/removeseance/{id}', name: 'remove_seance')]

     public function removeCoach(ManagerRegistry $doctrine,$id,SeanceRepository $repository)
     {
         $seance= $repository->find($id);
         $em = $doctrine->getManager();
         $em->remove($seance);
         $em->flush();
         return  $this->redirectToRoute("list_seance");
     }

     //-----------------------AfficherDétailsSeance-----------------------
    #[Route('/detailseance/{id<\d+>}', name: 'detail_seance')]
    public function detailcoach($id,  Seance $seance = null): Response {
        if( !$seance) {
            $this->addFlash('error', "La seance d'id $id n'existe pas");
            return $this->redirectToRoute('list_seance');
        }
       else {
        return $this->render('seance/detailseance.html.twig', ['seance' => $seance]);
    }}

    #[Route('/statistiques', name: 'statistiques_seance')]
    public function statistiques(SeanceRepository $repo){
        
        $seances= $repo->findAll();
        $seanceNom= [];
        $seanceGrp= [];
        $seanceColor= [];
        foreach($seances as $seance){
            $seanceNom[] = $seance->getTitre();
            $seanceGrp[] = $seance->getNbrGrp();
            $seanceColor[] = $seance->getColor();
           
           
        }
       return $this->render('seance/stats.html.twig', [
        'SeanceNom' => json_encode($seanceNom),
        'SeanceGrp' => json_encode($seanceGrp),
        'SeanceColor' => json_encode($seanceColor)
    ]);
    }
}
