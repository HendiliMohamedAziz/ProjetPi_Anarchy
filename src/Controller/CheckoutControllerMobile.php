<?php

namespace App\Controller;
use App\Entity\BillingAddress;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface ;
use App\Repository\ArticleRepository;
use App\Entity\Panier;
use App\Entity\PanierArticle;
use App\Entity\Commande;
use DateTime;


class CheckoutControllerMobile extends AbstractController
{
    #[Route('/APIBillingAddress', name: 'add2Mobile', methods: ['POST'])]
    public function addForm(ManagerRegistry $doctrine,Request $request)
    {
        $BillingAddress= new BillingAddress();
        $BillingAddress->setNom($request->get("nom"));
        $BillingAddress->setEmail($request->get("email"));
        $BillingAddress->setAddress($request->get("address"));
        $BillingAddress->setDescription($request->get("description"));
        $BillingAddress->setPhone($request->get("phone"));
            $em= $doctrine->getManager();
            $em->persist($BillingAddress);
            $em->flush();
        return new \Symfony\Component\HttpFoundation\JsonResponse(['message' => ' billing Address a ate mise a jour avec succes']);

    }

    #[Route('/ApIPlaceOrder', name: 'placeOrderMobile', methods: ['GET'])]
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
        foreach ($pan as $id => $quantity) 
            { 
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
            $commande->setIdAddress($derniereLigne);
            $commande->setMontant($total);
            $commande->setIdPanier($panier);
            $commande->setDate($date );
            $commande->setConfirmeAdmin(false);
            $c= $doctrine->getManager();
            $c->persist($commande);
            $c->flush();
            $session->remove('panier');
            return new \Symfony\Component\HttpFoundation\JsonResponse(['message' => 'La commande a ate mise a jour avec succes']);
    }

}

 
