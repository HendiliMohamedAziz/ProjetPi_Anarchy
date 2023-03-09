<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\ArticleType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Dompdf\Dompdf as Dompdf;
use Dompdf\Options;



class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
 //liste des articles
 

    #[Route('/listarticle', name: 'list_article')]
    public function listarticle(articleRepository $repository,Request $request) : Response
    {
        $articles= $repository->findByExampleField("desarchive");
        return $this->render("article/list.html.twig",
            array('tabarticle'=>$articles));
      
    }



    #[Route('/listarchive', name: 'list_archive')]
    public function listarchive(articleRepository $repository,Request $request) : Response
    {
       $articles= $repository->findByExampleField("supprimer");
        return $this->render("article/archive.html.twig",
            array('tabarticle'=>$articles));
    }

   
 //ajouter acticle
    #[Route('/addForm', name: 'add2')]
    public function addForm(ManagerRegistry $doctrine , Request $request,SluggerInterface $slugger)
    {
        $article= new article;
        $form= $this->createForm(articleType::class,$article);
        $form->handleRequest($request) ;
        if ($form->isSubmitted() && $form->isValid()){

            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) { 
                }
                $article->setImage($newFilename);
            }
             $em= $doctrine->getManager();
             $article->setEtat("desarchive");
             $em->persist($article);
             $em->flush();
             return  $this->redirectToRoute("list_article");
         }
         return $this->renderForm("article/add.html.twig",array( "formarticle"=>$form));
    }
    //modifier article
    #[Route('/updateForm/{id}', name: 'update')]
    public function  updateForm($id,articleRepository $repository,ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger)
{
        $article= $repository->find($id);
        $form= $this->createForm(articleType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted()){

            
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) { 
                }
                $article->setImage($newFilename);
            }




            $em= $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_article");
        }
        return $this->renderForm("article/update.html.twig",array("formarticle"=>$form));
    }
 //supprimer article
    #[Route('/removeForm/{id}', name:'remove')]

    public function removearticle($id)
    {
       
        $em=$this->getDoctrine()->getManager();
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        $article->setEtat("supprimer");
        $em->flush();

        return $this->redirectToRoute("list_article");
    }


  
 //see more

    #[Route('/article/{id}', name:'articledetail')]

    public function Article(ArticleRepository $articleRepository,ManagerRegistry $doctrine,CommentaireRepository $commentaireRepository,$id,Request $request){
      
        $article= $articleRepository->find($id);
        $commentaires=$commentaireRepository->findByArticle($article);
        return $this->render("article/article.html.twig",
            array('tabarticle'=>$article,'tabcommentaires'=>$commentaires)); 
    }

 //supprimer un commentaire
    #[Route('/removecommentaire/{id}', name: 'removecommentaire')]

    public function removecommentaire(ManagerRegistry $doctrine,$id,CommentaireRepository $repository,ArticleRepository $articleRepository)
    {
        $commentaire= $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($commentaire);
        $em->flush();
        return  $this->redirectToRoute("list_article");
    }

  
/*  front  */
//ajouter commentaire a un article

function removeBadWords($comment) {
    //hedha tableau taa lklem li thebou yestnahha 
    $badWords = array("bad", "words");
    $words = explode(" ", $comment->getContenu());
    foreach ($words as &$word) { 
        if (in_array(strtolower($word), $badWords)) { 
            $word = str_repeat("*", strlen($word)); 
        }
    }
    $newComment = implode(" ", $words); 
    echo $newComment;
    $comment->setContenu(  $newComment);
    return $comment;
}

#[Route('/article2/{id}', name:'articledetail2')]
public function Article2(ArticleRepository $articleRepository,ManagerRegistry $doctrine,CommentaireRepository $commentaireRepository,$id,Request $request){
  
    $article= $articleRepository->find($id);

    $commentaires=$commentaireRepository->findByArticle($article);
    $commentaire=new Commentaire();
    $form= $this->createForm(CommentaireType::class,$commentaire);
    $form->handleRequest($request);
    $token = $this->get('security.token_storage')->getToken();
    $user = $token->getUser();
    $nom=$user->getNom();
    dd($user);
    if($form->isSubmitted() ){
        $commentaire->setDate(new \DateTime());
        $commentaire->setAuteur($nom);
        
        $commentaire->setArticle($article);
       
        $this->removeBadWords($commentaire);
       
        $em= $doctrine->getManager();
        $em->persist($commentaire);
        $em->flush();
        return $this->redirectToRoute("articledetail2",array('id'=>$id));
    }
    return $this->render("article/frontarticle.html.twig",
        array('tabarticle'=>$article,'tabcommentaires'=>$commentaires,'form'=>$form->createView())); 
}

#[Route('/desarchive/{id}', name:'desarchiver')]

public function desarchive($id)
{
   
    $em=$this->getDoctrine()->getManager();
    $article = $this->getDoctrine()
        ->getRepository(Article::class)
        ->find($id);
    $article->setEtat("desarchive");
    $em->flush();

    return $this->redirectToRoute("list_article");
}


 //liste des articles
#[Route('/listarticle2', name: 'list_article2')]
public function listarticle2(articleRepository $repository,Request $request)
{

    $filter = $request->get("filter");
    $page = $request->query->getInt('page', 1); 
    $article = $repository->findProductsPaginated($page, 3 , $filter,"desarchive"); 
    
    if ($filter != null) { 
        return  new JsonResponse([ 
            "content" => $this->renderView('article/filter_list.html.twig', [
                'tabarticle' => $article,
            ])
        ]);
    } else {
        return $this->render('article/frontlist.html.twig', [
            'tabarticle' => $article,
            
        ]);
    }

}


#[Route('/article/data/download', name: 'users_data_download')]

public function usersDataDownload(ArticleRepository $article)
{
    // On définit les options du PDF
    $pdfOptions = new Options();
    // Police par défaut
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->setIsRemoteEnabled(true);

    // On instancie Dompdf
    $dompdf = new Dompdf($pdfOptions);
    $article= $article->findAll();
   
    // $classrooms= $this->getDoctrine()->getRepository(classroomRepository::class)->findAll();

    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => FALSE,
            'verify_peer_name' => FALSE,
            'allow_self_signed' => TRUE
        ]
    ]);
    $dompdf->setHttpContext($context);

    // On génère le html
    $html =$this->renderView('article/pdf.html.twig',[
        'articles'=>$article
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // On génère un nom de fichier
    $fichier = 'Liste-produit' .'.pdf';

    // On envoie le PDF au navigateur
    $dompdf->stream($fichier, [
        'Attachment' => true
    ]);

    return new Response() ;
}
#[Route('/searcharticle', name: 'seracharticle')]
public function searchArticle(ArticleRepository $repository,Request $request){
    $articles= $repository->search($request->get('val'));
    if(!$articles) {
        $result['articles']['error'] = "Aucun Article trouvé";
    } else {
        $result['articles'] = $this->getRealEntities($articles);
    }

    return new Response(json_encode($result));
}

public function getRealEntities($articles)
 {
    foreach ($articles as $article) {
        $realEntities[$article->getId()] = [$article->getNom(),$article->getDescription(),$article->getPrix(),$article->getImage()];
    }
    return $realEntities;
 }

}