<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class PanierMobileController extends AbstractController
{
    #[Route('/panierAPI', name: 'app_panierAPI')]
    public function index(SessionInterface $session, ArticleRepository $articleRepository,NormalizerInterface $serializer): JsonResponse
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'Article' => $articleRepository->findOneById($id),
                'quantity' => $quantity,
            ];
        }

        $total = 0;

        foreach ($panierWithData as $item) {
            $totalItem = $item['Article']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }

        $response = [
            'items' => $panierWithData,
            'total' => $total,
        ];

        $formatted = $serializer->normalize($panierWithData, 'json', [
            'groups' => ['panier_list'],
        ]);
        return new \Symfony\Component\HttpFoundation\JsonResponse($formatted);
    }

    #[Route('/panierAPI/add/{id}', name: 'panier_addAPI', methods: ['POST'])]
    public function add($id, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        $response = [
            'success' => true,
            'message' => 'Product added to cart successfully',
        ];

        return new JsonResponse($response);
    }

    #[Route('/panierAPI/remove/{id}', name: 'panier_removeAPI', methods: ['POST'])]
    public function remove($id, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        $response = [
            'success' => true,
            'message' => 'Product removed from cart successfully',
        ];

        return new JsonResponse($response);
    }

    #[Route('/panierAPI/removeOne/{id}', name: 'remove1_panierAPI', methods: ['POST'])]
    public function removeOne($id, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        $response = [
            'success' => true,
            'message' => 'One product removed from cart successfully',
        ];

        return new JsonResponse($response);
    }
}