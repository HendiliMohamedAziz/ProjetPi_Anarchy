<?php

namespace App\Controller;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductController extends AbstractController
{
    
      #[Route('/products', name: 'product_list')]
    public function list()
    {
        // Obtenez les produits de la base de données
        $products = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/APIproducts', name: 'product_list1')]
    public function listp( NormalizerInterface $normalizable)
    {
        $products = $this->getDoctrine()->getRepository(Article::class)->findAll();

        $jsonContent = $normalizable->normalize($products, 'json', ['groups' => 'panier_list']);

        return new \Symfony\Component\HttpFoundation\JsonResponse($jsonContent);
    }

}