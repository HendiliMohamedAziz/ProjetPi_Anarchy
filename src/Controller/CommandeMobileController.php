<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CommandeMobileController extends AbstractController
{

    #[Route('/listcommandeAPI', name: 'list_commandeAPI', methods: ['GET'])]
    public function listCommande(CommandeRepository $repository,SerializerInterface $serializer)
    {
        $commande= $repository->findAll();
        $formatted = $serializer->serialize($commande, 'json', [
            'groups' => ['commande_list'],
        ]);

        return new JsonResponse($formatted, 200, [], true);    }



        #[Route('/AddCommandeApi', name: 'addCommande_api', methods: ['POST'])]
        public function addForm(Request $request, SerializerInterface $serializer, EntityManagerInterface $em) 
        {
            $content = $request->getContent();
            $data = $serializer->deserialize($content, Commande::class, 'json');
        
            $em->persist($data);
            $em->flush();
            
            return $this->json($data);
        }
        #[Route('/updateFormAPI/{id}', name: 'updateAPI', methods: ['POST'])]
        public function updateCommande(Request $request, SerializerInterface $serializer, $id)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $commande = $entityManager->getRepository(Commande::class)->find($id);

            if (!$commande) {
                throw $this->createNotFoundException(
                    'Aucune commande trouvée pour l\'identifiant '.$id
                );
            }
            $commande->setConfirmeAdmin($request->get("ConfirmeAdmin") == 'true');
            // On persiste et on flush les modifications apportées à l'objet Commande
            $entityManager->persist($commande);
            $entityManager->flush();
    
            // On retourne une réponse JSON pour indiquer que la mise à jour a été effectuée avec succès
            return new JsonResponse(['message' => 'La commande a ate mise a jour avec succes']);
        }

       #[Route('/deleteFormAPI/{id}', name: 'deleteAPI', methods: ['POST'])]
         public function deleteCommandeAction(Request $request,$id) {
         $em = $this->getDoctrine()->getManager();
         $Commande = $em->getRepository(Commande::class)->find($id);
         if($Commande!=null ) {
             $em->remove($Commande);
             $em->flush();

             $serialize = new Serializer([new ObjectNormalizer()]);
             $formatted = $serialize->normalize("Commande a ete supprimee avec success.");
             return new JsonResponse($formatted);

         }
         return new JsonResponse("id Commande invalide.");

     }

}








