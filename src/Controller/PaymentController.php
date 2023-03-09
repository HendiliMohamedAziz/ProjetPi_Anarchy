<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe;
use Symfony\Component\HttpFoundation\Session\SessionInterface ;
use App\Repository\ArticleRepository;
 
class PaymentController extends AbstractController
{
    #[Route('/client/stripe', name: 'app_stripe')]
    public function index(): Response
    {
 
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }
 
    #[Route('/client/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request,SessionInterface $session , ArticleRepository $ArticleRepository)
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
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        try {
            Stripe\Charge::create ([
                "amount" => $total * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => " Payment Test"
            ]);

            // Paiement réussi, on redirige l'utilisateur vers la page de succès
            $this->addFlash(
                '',
                ''
            );
            
            return $this->redirectToRoute('checkout3', [], Response::HTTP_SEE_OTHER);
        } catch (Stripe\Exception\CardException $e) {
            // Paiement échoué, on redirige l'utilisateur vers la page d'échec
            $this->addFlash(
                '',
                ''
            );
            return $this->redirectToRoute('checkout4', [], Response::HTTP_SEE_OTHER);
        }

 
    }
}
