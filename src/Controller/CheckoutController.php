<?php

namespace App\Controller;
use App\Entity\BillingAddress;
use App\Form\BillingAddressType;
use App\Repository\BillingAddressRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface ;
use App\Repository\ArticleRepository;
use App\Entity\Panier;
use App\Entity\PanierArticle;
use App\Entity\Article;
use App\Entity\Commande;
use DateTime;


class CheckoutController extends AbstractController
{
    /**
     * @Route("/client/checkout", name="checkout")
     */
    public function index(): Response
    {

        return $this->render('checkout/index.html.twig', [
            'controller_name' => 'CheckoutController',
        ]);
    }
        /**
     * @Route("/client/checkout2", name="checkout2")
     */
    public function index2(SessionInterface $session , ArticleRepository $ArticleRepository): Response
    {
            $panier = $session->get('panier', []); 
    
            $panierWithData = []; 
    
            foreach ($panier as $id => $quantity) {
    
             $panierWithData[] = ['Article' => $ArticleRepository->find($id) , 'quantity' => $quantity ];
        }
            $total=0 ;
            foreach ($panierWithData as $item )
                {
                $totalItem= $item['Article']->getPrix()*$item['quantity'];
                 $total += $totalItem;
                }  
                
    
                return $this->render('checkout/index2.html.twig', [
                    'items' => $panierWithData,
                    'total'=>$total
                ]);
        }    
            /**
     * @Route("/client/checkout3", name="checkout3")
     */
    public function index3(): Response
    {
        return $this->render('checkout/placeOrder.html.twig');
    }
                /**
     * @Route("/client/checkout4", name="checkout4")
     */
    public function index4(): Response
    {
        return $this->render('checkout/fail.html.twig');
    }
    #[Route('/client/addForm', name: 'add2')]
    public function addForm(ManagerRegistry $doctrine,Request $request)
    {
        $BillingAddress= new BillingAddress();
        $form= $this->createForm(BillingAddressType::class,$BillingAddress);
        $form->handleRequest($request) ;
        if ($form->isSubmitted()&& $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($BillingAddress);
            $em->flush();
            return $this->redirectToRoute("checkout2");
         }
        return $this->renderForm("checkout/add.html.twig",array("formBillingAddress"=>$form));
    }
    #[Route('/client/PlaceOrder', name: 'placeOrder')]
    public function PlaceOrder(SessionInterface $session , ArticleRepository $ArticleRepository,ManagerRegistry $doctrine,Request $request):response
    {
                
        $pan = $session->get('panier', []); 

        $panierWithData = []; 
        $panier=new Panier();
        $panier->setPrix(100);
        $panier->setQuantity(2);
        $panier->setConfirme(true);
        $a= $doctrine->getManager();
        $a->persist($panier);
        $a->flush();
        $em= $doctrine->getManager();
        foreach ($pan as $id => $quantity) {
         
         $panierWithData[] = ['Article' => $ArticleRepository->find($id) , 'quantity' => $quantity ];
         $panierArticle= new PanierArticle();
         $panierArticle->setIdArticle($ArticleRepository->find($id));
         $panierArticle->setQuantity($quantity);
         $panierArticle->setIdPanier($panier);
         $em->persist($panierArticle);
            }
            $em->flush();
        $quantity=0;
        $total=0 ;
        foreach ($panierWithData as $item )
            {
            $totalItem= $item['Article']->getPrix()*$item['quantity'];
             $total += $totalItem;
             $quantity=$quantity+1;

            }  
            $panier->setPrix($total);
            $panier->setQuantity($quantity);
            $panier->setConfirme(true);
            $a= $doctrine->getManager();
            $a->persist($panier);
            $a->flush();
            $date = new DateTime();
            $repository = $this->getDoctrine()->getRepository(BillingAddress::class);

            $derniereLigne = $repository->findOneBy([], ['id' => 'desc']);
            $commande=new Commande ();
            $commande->setMontant($total);
            $commande->setIdPanier($panier);
            $commande->setIdAddress($derniereLigne);
            $commande->setDate($date );
            $commande->setConfirmeAdmin(false);
            $c= $doctrine->getManager();
            $c->persist($commande);
            $c->flush();
            $session->remove('panier');
            return $this->render('checkout/final.html.twig');
        }

    }

 
