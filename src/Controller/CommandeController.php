<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Entity\PanierArticle;
use App\Repository\PanierArticleRepository;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use  Dompdf\Dompdf as Dompdf;
use Dompdf\Options;
class CommandeController extends AbstractController
{
    #[Route('/admin/produit/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/list.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
    #[Route('/admin/produit/listcommande', name: 'list_commande')]
    public function listCommande(CommandeRepository $repository)
    {
        $commande= $repository->findAll();
       return $this->render("commande/list.html.twig",array("tabcommande"=>$commande));
    }
    #[Route('/admin/produit/updateFormCommande/{id}', name: 'updateCommande')]
    public function  updateForm($id,CommandeRepository $repository,ManagerRegistry $doctrine,Request $request)
    {
        $commande= $repository->find($id);
        $form= $this->createForm(CommandeType::class,$commande);
        $form->handleRequest($request) ;
        if ($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_commande");
        }
        return $this->renderForm("commande/update.html.twig",array("formCommande"=>$form));
    }


    #[Route('/admin/produit/removeFormCommande/{id}', name: 'removeCommande')]
    public function removeCommande(ManagerRegistry $doctrine,$id,CommandeRepository $repository)
    {
        $commande= $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($commande);
        $em->flush();
        return  $this->redirectToRoute("list_commande");
    }

    #[Route('/admin/produit/commande/data/download1', name: 'users_data_download1')]

    public function usersDataDownload(CommandeRepository $commande)
    {
        // On d??finit les options du PDF
        $pdfOptions = new Options();
        // Police par d??faut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
    
        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $commande= $commande->findAll();
       
        // $classrooms= $this->getDoctrine()->getRepository(classroomRepository::class)->findAll();
    
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);
    
        // On g??n??re le html
        $html =$this->renderView('commande/pdf.html.twig',[
            'tabcommande'=>$commande
        ]);
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // On g??n??re un nom de fichier
        $fichier = 'Liste-commande' .'.pdf';
    
        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);
    
        return new Response() ;
    }

    #[Route('/admin/produit/FindPanier/{id}', name: 'FindPanier')]
    public function FindPanier(ManagerRegistry $doctrine,$id,CommandeRepository $repository,PanierArticleRepository $repository1,ArticleRepository $repository2)
    {
 // R??cup??rer la commande correspondante ?? l'ID donn??
$repositoryCommande = $this->getDoctrine()->getRepository(Commande::class);
$commande = $repositoryCommande->find($id);

// R??cup??rer le panier associ?? ?? cette commande
$panier = $commande->getIdPanier();

// R??cup??rer les ??l??ments de panier associ??s ?? ce panier
$repositoryPanierArticle = $this->getDoctrine()->getRepository(PanierArticle::class);
$panierArticles = $repositoryPanierArticle->createQueryBuilder('pa')
    ->select('pa, a.nom, a.image,a.prix')
    ->join('pa.idArticle', 'a')
    ->where('pa.idPanier = :panier')
    ->setParameter('panier', $panier)
    ->getQuery()
    ->getResult();
 
// R??cup??rer les articles associ??s ?? chaque ??l??ment de panier
$articles = array();
$i=0;
foreach ($panierArticles as $panierArticle) {
    $article = $panierArticles[$i];

    $i=$i+1;
    $articles[] = $article;
}
        return $this->render('commande/details.html.twig', [
            'items' => $articles ]);



    }


}