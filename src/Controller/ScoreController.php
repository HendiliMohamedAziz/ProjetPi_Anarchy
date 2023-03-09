<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Score;
use App\Form\ScoreType;
use App\Repository\UserRepository;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ScoreController extends AbstractController
{
    #[Route('/score', name: 'app_score')]
    public function index(): Response
    {
        return $this->render('test', [
        ]);
    }

    #[Route('/client/noter/coach/{id}', name: 'noter_coach')]
    public function ajout_score_coach($id,Request $request,ScoreRepository $rp,UserRepository $urp): Response
    {
        $score=new score();
        $user=$this->getUser();
        $coach=$urp->find($id);
        $form=$this->createForm(ScoreType::class,$score);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $score->setCoach($coach);
            $score->setUser($user);

            $em->persist($score);
            $em->flush();
            return $this->redirectToRoute("app_test");

            
           
        }
        return $this->render('score/ajout_note.html.twig', [
            'f' => $form->createView(),
        
        ]);
        }

        
       
        #[Route('/admin/reclamation/afficher_note', name: 'afficher_note')]
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
         
            return $this->render('score/afficher_note.html.twig',[
                'sco' => $score,
                'topcoach'=> $top_coach

                
             
            ] );
        }



        
       /* #[Route('/afficher_moyenne', name: 'afficher_moyenne')]
        public function calculer_moyenne(ScoreRepository $repository,UserRepository $urp): Response
        {
            $p=0;
           $m=0;
           
            $score=$repository->findAll();
            $coach=$urp->findByroles('["coach"]');
            dd($coach);
           
            dd($coach[0]->getMoyenne());
            
            return $this->render('score/afficher_note.html.twig',[
                'sco' => $score,
                'topcoach'=> $coach
                
             
            ] );
        }
        /*public function topCoaches(EntityManagerInterface $entityManager)
        {
            $coaches = $entityManager->createQueryBuilder()
                ->select('u.nom, AVG(r.score) AS score_moyen')
                ->from(User::class, 'u')
                ->join(Score::class, 'r', 'WITH', 'u.id = r.coach')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%"' . User::ROLE_COACH . '"%')
                ->groupBy('u.id')
                ->orderBy('score_moyen', 'DESC')
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();
        
            return $this->render('top_coaches.html.twig', [
                'coaches' => $coaches,
            ])}*/
        
     
        
        
        
        

}
