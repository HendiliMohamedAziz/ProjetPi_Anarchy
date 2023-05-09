<?php


namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;

 use App\Entity\Article;
 use App\Serializer\CircularReferenceHandler;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
 use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;
use App\Repository\ArticleRepository;


class Article_Controller extends  AbstractController
{


    
    /******************Ajouter Article*****************************************/
     /**
      * @Route("/addArticle", name="add_Article")
      * @Method("POST")
      */

     public function ajouterArticleAction(Request $request)
     {
         $Article = new Article();
         $description = $request->query->get("description");
         $nom = $request->query->get("nom");
         $prix = $request->query->get("prix");
         $image = $request->query->get("image");
         $em = $this->getDoctrine()->getManager();
         
         $Article->setNom($nom);
         $Article->setDescription($description);
         $Article->setPrix($prix);
         $Article->setImage($image);
         $Article->setEtat("desrachive");
         $em->persist($Article);
         $em->flush();
         $serializer = new Serializer([new ObjectNormalizer()]);
         $formatted = $serializer->normalize($Article);
         return new JsonResponse($formatted);

     }

     /******************Supprimer Article*****************************************/

     /**
      * @Route("/deleteArticle", name="delete_Article")
      * @Method("DELETE")
      */

     public function deleteArticleAction(Request $request) {
         $id = $request->get("id");

         $em = $this->getDoctrine()->getManager();
         $Article = $em->getRepository(Article::class)->find($id);
         if($Article!=null ) {
             $em->remove($Article);
             $em->flush();

             $serialize = new Serializer([new ObjectNormalizer()]);
             $formatted = $serialize->normalize("Article a ete supprimee avec success.");
             return new JsonResponse($formatted);

         }
         return new JsonResponse("id Article invalide.");


     }





      /**
     * @param Request $request
     * @Route("/article/json/upload",name="uploadJson",methods={"GET","POST"})
     * @return JsonResponse
     */
    public function uploadImage(Request $request)
    {

        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);

        if ((($_FILES["file"]["type"] == "image/*") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 5000000) && in_array($extension, $allowedExts)) {
            if ($_FILES["file"]["error"] > 0) {
                $named_array = array("Response" => array(array("Status" => "error")));
                return new JsonResponse($named_array);

            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], $this->getParameter('image').$_FILES["file"]["name"]);

                $Path = $_FILES["file"]["name"];
                $named_array = array("Response" => array(array("Status" => "ok")));
                return new JsonResponse($named_array);
            }
        } else {
            $named_array = array("Response" => array(array("Status" => "invalid")));
            return new JsonResponse($named_array);

        }
    }


    /******************Modifier Article*****************************************/
    /**
     * @Route("/updateArticle", name="update_Article")
     * @Method("POST")
     */
    
    public function modifierArticleAction(ArticleRepository $articleRepository,Request $request) {
       /* $em = $this->getDoctrine()->getManager();
        $Article = $this->getDoctrine()->getManager()
                        ->getRepository(Article::class)
                        ->find($request->get("id"));

         $Article->setImage($request->get('image'));
         $Article->setNom($request->get('nom'));
          $Article->setDescription($request->get('Description'));
          $Article->setEtat('desarchive');
           $Article->setPrix($request->get('prix'));

        $em->persist($Article);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Article);
        return new JsonResponse("Article a ete modifiee avec success.");*/

        $em = $this->getDoctrine()->getManager();

        $article = $articleRepository->find($request->get('id'));
       
        $article->setImage($request->get('image'));
        $article->setNom($request->get('nom'));
         $article->setDescription($request->get('Description'));
         $article->setEtat('desarchive');
          $article->setPrix($request->get('prix'));

        $em->persist($article);
        $em->flush();

        return new JsonResponse($article);

    }



    

     /******************Detail Article*****************************************/

     /**
      * @Route("/detailArticle", name="detail_Article")
      * @Method("GET")
      */

     //Detail Article
     public function detailArticleAction(Request $request)
     {
         $id = $request->get("id");

         $em = $this->getDoctrine()->getManager();
         $Article = $this->getDoctrine()->getManager()->getRepository(Article::class)->find($id);
         return $this->json([
            'data'=>$Article
      ],200,[],[ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function()
      {return 'symfony4';}]
      );
     }

     
     /**
      * @Route("/displayArticle", name="display_article")
      */
      public function allRecAction(ArticleRepository $repository):JsonResponse
      {
 
         /*$article = $repository->findAll();
          $serializer = new Serializer([new ObjectNormalizer()]);
          $formatted = $serializer->normalize($article);
 
          return new JsonResponse($formatted);*/
          $articles = $repository->findAll();
         /* $encoder = new JsonEncoder();
          $normalizer = new ObjectNormalizer();
           ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function()
           {return 'symfony4';};
          $serializer = new Serializer( $normalizer,[$encoder]);
          $formatted = $serializer->normalize($articles);*/

          return $this->json(
                $articles
          ,200,[],[ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function()
          {return ;}]
          );
          
          
           
      }
 


 }
