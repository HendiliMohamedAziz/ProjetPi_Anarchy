<?php


namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;

 use App\Entity\Seance;
 use App\Entity\User;
 use App\Serializer\CircularReferenceHandler;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
 use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;
use App\Repository\SeanceRepository;
use App\Repository\UserRepository;

class Seance_Controller extends  AbstractController
{


    /******************Ajouter Article*****************************************/
     /**
      * @Route("/addSeance1", name="add_Seance1")
      * @Method("POST")
      */

      public function ajouterSeanceAction(Request $request)
      {
          $Seance = new Seance();
          $nbr_seance = $request->query->get("nbr_seance");
          $nbr_grp = $request->query->get("nbr_grp");
          $description = $request->query->get("description");
          $color = $request->query->get("color");
          $titre = $request->query->get("titre");
          $em = $this->getDoctrine()->getManager();
          $Seance->setNbrSeance($nbr_seance);
          $Seance->setNbrGrp($nbr_grp);
          $Seance->setDescription($description);
         
          $Seance->setColor($color);
          $Seance->setTitre($titre);
          $em->persist($Seance);
          $em->flush();
          $serializer = new Serializer([new ObjectNormalizer()]);
          $formatted = $serializer->normalize($Seance);
          return new JsonResponse($formatted);
 
      }
     /******************Supprimer Article*****************************************/

     /**
      * @Route("/deleteSeance1", name="delete_Seance1")
      * @Method("DELETE")
      */

      public function deleteSeanceAction(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Seance = $em->getRepository(Seance::class)->find($id);
        if($Seance!=null ) {
            $em->remove($Seance);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Seance a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Seance invalide.");


    }

    

     /******************Modifier Article*****************************************/
    /**
     * @Route("/updateSeance1", name="update_Seance1")
     * @Method("POST")
     */
    
     public function modifierSeanceAction(SeanceRepository $reservationRepository,Request $request) {

        $em = $this->getDoctrine()->getManager();
 
         $seance = $reservationRepository->find($request->get('id'));
        
         $seance->setNbrSeance($request->get('nbr_seance'));
         $seance->setNbrGrp($request->get('nbr_grp'));
         $seance->setDescription($request->get('description'));
        
 
         $em->persist($seance);
         $em->flush();
 
         return new JsonResponse($seance);
 
     }
/**
      * @Route("/displaySeance1", name="display_seance1")
      */
      public function allRecAction(SeanceRepository $repository):JsonResponse
      {
          $seances = $repository->findAll();
          return $this->json([
                'data'=>$seances
          ],200,[],[ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function()
          {return 'symfony4';}]
          );
          
 
}
 
     

 }