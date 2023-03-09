<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Reclamation;
use App\Form\UpdaterecType;
use App\Form\ReclamationType;
use App\Form\ReclamationbackType;
use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use App\Repository\ScoreRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('test', [
         
        ]);
    }

    function removeBadWords($comment) {
        //hedha tableau taa lklem li thebou yestnahha 
        $badWords = array("bad", "words");
        $words = explode(" ", $comment->getMessage());
        foreach ($words as &$word) { 
            if (in_array(strtolower($word), $badWords)) { 
                $word = str_repeat("*", strlen($word)); 
            }
        }
        $newComment = implode(" ", $words); 
        echo $newComment;
        $comment->setMessage(  $newComment);
        return $comment;
    }
    

    #[Route('/reclamer/coach/{id}', name: 'reclamer_coach')]
    public function ajout_reclamation_coach($id,Request $request,ReclamationRepository $rp,UserRepository $urp): Response
    {
        $reclamation=new reclamation();
        $user=$this->getUser();
        $coach=$urp->find($id);

        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted() )
        {
            $this->removeBadWords($reclamation);
            $em=$this->getDoctrine()->getManager();
            $reclamation->setCoach($coach);
            $reclamation->setDatereclamation(new \DateTime("now"));
            $reclamation->setType("reclamation_coach");
            $reclamation->setEtat("non traité");
            //$reclamation->setColor("#FF0000");          
            $reclamation->setUser($user);
            
            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Reclamation Added Successfully !');
            return $this->redirectToRoute("reclamer_coach", ['id'=>$id], Response::HTTP_SEE_OTHER);
         

            
           
        }
        return $this->render('reclamation/ajout_reclamation.html.twig', [
            'f' => $form->createView(),
        
        ]);
        }

        #[Route('/reclamer/club/{id}', name: 'reclamer_club')]
    public function ajout_reclamation_club($id,Request $request,ReclamationRepository $rp,UserRepository $urp,ClubRepository $crp): Response
    {
        $reclamation=new reclamation();
        $user=$this->getUser();
        $club=$crp->find($id);
        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $this->removeBadWords($reclamation);
            $em=$this->getDoctrine()->getManager();
            $reclamation->setClub($club);
            $reclamation->setDatereclamation(new \DateTime("now"));
            $reclamation->setType("reclamation_club");
            $reclamation->setEtat("non traité");
            //$reclamation->setColor("#FF0000");          
            $reclamation->setUser($user);

            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Reclamation Added Successfully !');
            return $this->redirectToRoute("app_test", [], Response::HTTP_SEE_OTHER);

            
           
        }
        return $this->render('reclamation/ajout_reclamation.html.twig', [
            'f' => $form->createView(),
        
        ]);
        }

        #[Route('/reclamer/produit/{id}', name: 'reclamer_produit')]
    public function ajout_reclamation_produit($id,Request $request,ReclamationRepository $rp,UserRepository $urp,ArticleRepository $arp): Response
    {
        $reclamation=new reclamation();
        $user=$this->getUser();
        $art=$arp->find($id);
        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {              $this->removeBadWords($reclamation);
             $em=$this->getDoctrine()->getManager();
            $reclamation->setArticle($art);
            $reclamation->setDatereclamation(new \DateTime("now"));
            $reclamation->setType("reclamation_produit");
            $reclamation->setEtat("non traité");
            //$reclamation->setColor("#FF0000");          
            $reclamation->setUser($user);

            $em->persist($reclamation);
            $em->flush();
            $this->addFlash('success','Reclamation Added Successfully !');
            return $this->redirectToRoute("app_test", [], Response::HTTP_SEE_OTHER);

            
           
        }
        return $this->render('reclamation/ajout_reclamation.html.twig', [
            'f' => $form->createView(),
        
        ]);
        }



        #[Route('/afficher_coach', name: 'afficher_coach')]
        public function afficher_coach(UserRepository $repository,ScoreRepository $sr): Response
        {
    
           
            $coach=$repository->findByroles('["ROLE_COACH"]');
            $score=$sr->findAll(); 
         
            return $this->render('reclamation/afficher_coach.html.twig',[
                'coach' => $coach,
                'score'=>$score
                
             
            ] );
        }
        #[Route('/afficher_club', name: 'afficher_club')]
        public function afficher_club(ClubRepository $repository): Response
        {
    
           
            $club=$repository->findAll();
         
            return $this->render('reclamation/afficher_club.html.twig',[
                'club' => $club,
                
             
            ] );
        }
        #[Route('/afficher_produit', name: 'afficher_produit')]
        public function afficher_produit(ArticleRepository $repository): Response
        {
    
           
            $produit=$repository->findAll();
         
            return $this->render('reclamation/afficher_produit.html.twig',[
                'produit' => $produit,
                
             
            ] );
        }
        #[Route('/afficher_reclamation', name: 'afficher_reclamation')]
        public function afficher_reclamation(ReclamationRepository $repository): Response
        {
    
           
            $reclamation=$repository->findAll();
         
            return $this->render('reclamation/afficher_reclamation.html.twig',[
                'rec' => $reclamation,
                
             
            ] );
        }

        #[Route('/afficher_reclamation_nontraite', name: 'afficher_reclamationnt')]
        public function afficher_reclamationnt(ReclamationRepository $repository): Response
        {
    
           
            $reclamation=$repository->findAll();
            $recs=$repository->findbynontraite("non traité");
         
            return $this->render('reclamation/afficher_reclamation.html.twig',[
                'rec'=>$recs

                
             
            ] );
        }


        #[Route('/afficher_reclamation_traite', name: 'afficher_reclamationt')]
        public function afficher_reclamationt(ReclamationRepository $repository): Response
        {
    
           
            $reclamation=$repository->findAll();
            $recs=$repository->findbynontraite(" traité");
      
         
            return $this->render('reclamation/afficher_reclamation.html.twig',[
                'rec'=>$recs
             
            ] );
        }
                        
        #[Route('/afficher_mes_reclamations', name: 'afficher_mes_reclamations')]
        public function afficher_mes_reclamations(ReclamationRepository $repository,UserRepository $urp): Response
        {
            $user=$this->getUser();
            $reclamation=$repository->findByuser($user);
         
            return $this->render('reclamation/afficher_mes_reclamations.html.twig',[
                'rec' => $reclamation,
                
             
            ] );
        }
       
/**
     * @param $id
     * @param ReclamationRepository $rep
     * @route ("/delete_reclamation/{id}", name="delete_reclamation")
     */
    function Delete($id,ReclamationRepository $rep){
        $reclamation=$rep->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($reclamation);
        $em->flush();
        return $this->redirectToRoute('afficher_mes_reclamations');
    }

    #[Route('/update_reclamation/{{id}}', name: 'update_reclamation')]
 
    function Update(ReclamationRepository $repository,$id,Request $request)
    {
        $reclamation = $repository->find($id);
        $form = $this->createForm(UpdaterecType::class, $reclamation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("afficher_mes_reclamations");
        }
        return $this->render('reclamation/update_reclamation.html.twig',
            [
                'f' => $form->createView(),
                "form_title" => "Modifier la reclamation"
            ]);
    }
    /**
     * @param $id
     * @param ReclamationRepository $rep
     * @route ("/delete_reclamation_back/{id}", name="delete_reclamation_back")
     */
    function Delete1($id,ReclamationRepository $rep){
        $reclamation=$rep->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($reclamation);
        $em->flush();
        return $this->redirectToRoute('afficher_reclamation');
    }

    #[Route('traiter_reclamation/{id}', name: 'traiter_reclamation')]
    public function traiter_reclamation($id,Request $request,ReclamationRepository $rp, TransportInterface $mailer): Response
    {        
        $reclamation=$rp->find($id);
        $form=$this->createForm(ReclamationbackType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid() )
        {
            $reclamation->setEtat(" traité");
            $user=$reclamation->getUser() ;
            $em=$this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
            $user=$this->getDoctrine()->getRepository(User::class)->find(2);
            $email= (new TemplatedEmail())
                ->from("ahmed.sta@esprit.tn")
                ->to("ahmed.sta@esprit.tn")
                ->subject('Contact au sujet de la reclamation')
                ->text("reclamation traitée");
          
        
            $mailer->Send($email);
            $this->addFlash('message', 'Votre e-mail a bien été envoyé');
            return $this->redirectToRoute('afficher_reclamation');

        }

        return $this->render('reclamation/ajoutR.html.twig', [
            'f' => $form->createView(),
        
        ]);

    }

  /*  #[Route('/searchuserajax', name:'ajaxuser')]
     
    public function searchajax(Request $request ,ReclamationRepository $repository)
    {
        $requestString=$request->get('searchValue');
        $Users=$repository->findByreclamation($requestString);
        return $this->render('reclamation/ajax.html.twig', [
            'rec'=>$Users,
        ]);
    }


    public function app(): Response
    {
        $reclamationRepository = $this->getDoctrine()->getRepository(Reclamation::class);
        $reclamations = $reclamationRepository->getReclamationsStatus();

        return $this->render('reclamation/afficher_reclamation.html.twig',[
            'reclamations' => $reclamations,
        ]);
    }
*/

       

  /*  public function topCoaches(EntityManagerInterface $entityManager)
{
    $coaches = $entityManager->createQueryBuilder()
        ->select('u.nom, AVG(r.score) AS score_moyen')
        ->from(User::class, 'u')
        ->join(Rating::class, 'r', 'WITH', 'u.id = r.coach')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%"' . User::ROLE_COACH . '"%')
        ->groupBy('u.id')
        ->orderBy('score_moyen', 'DESC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();

    return $this->render('top_coaches.html.twig', [
        'coaches' => $coaches,
    ]);
}*/


  /**
     * @Route("/reclamation/search", name="reclamations_search")
     */
 

public function search(Request $request,EntityRepository $reclamationRepository)
{
    $form = $this->createFormBuilder()
        ->add('date', DateType::class)
        ->add('status', ChoiceType::class, [
            'choices' => ['New' => 'new', 'In Progress' => 'in_progress', 'Resolved' => 'resolved'],
            'placeholder' => 'Choose a status',
        ])
        ->add('type', ChoiceType::class, [
            'choices' => ['Complaint' => 'complaint', 'Refund' => 'refund'],
            'placeholder' => 'Choose a type',
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $qb = $reclamationRepository->createQueryBuilder('r');
        $qb->where('r.date = :date')->setParameter('date', $data['date']);
        $qb->andWhere('r.status = :status')->setParameter('status', $data['status']);
        $qb->andWhere('r.type = :type')->setParameter('type', $data['type']);

        $results = $qb->getQuery()->getResult();

        return $this->render('search/results.html.twig', [
            'results' => $results,
        ]);
    }

    return $this->render('search/form.html.twig', [
        'form' => $form->createView(),
    ]);
}


 
#[Route('/afficher_note2', name: 'afficher_note2')]
public function afficher_score(ScoreRepository $repository,UserRepository $urp): Response
{
    $m=0;
    $p=0;

   $top_coach=$urp->findByroles('["ROLE_COACH"]');
    $score=$repository->findAll();
    foreach($top_coach as $c)
    {
      foreach ($score as $s)
      {
            if ($s->getCoach() == $c){
                $m=$m+$s->getNote();
                  $p=$p+1;
              }
              
          }
          if($m==0&&$p==0)
          {$c->setMoyenne(null);}
          else
          {
          $em=$this->getDoctrine()->getManager();
          $c->setMoyenne($m/$p);
          $em->flush();
      }
          $m=0;
          $p=0;

      }
      $top_coach=$urp->findTopCoaches('["ROLE_COACH"]');
 
    return $this->render('reclamation/afficher_top3coach.html.twig',[
        'sco' => $score,
        'topcoach'=> $top_coach

        
     
    ] );
}

}

