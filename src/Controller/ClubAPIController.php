<?php

namespace App\Controller;

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;
use App\Entity\User;
use App\Repository\ClubRepository;
use App\Repository\ParticipationRepository;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Form\ClubType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ClubAPIController extends AbstractController
{
    #[Route('/club/api', name: 'app_club_api')]
    public function index(): Response
    {
        return $this->render('club_api/index.html.twig', [
            'controller_name' => 'ClubController',
        ]);
    }

    /**
     * @Route("/displayClub1", name="display_Club1")
     */
    public function allClubsAction(ClubRepository $repository): JsonResponse
    {
        $clubs = $repository->findAll();
        return $this->json(

            $clubs,
            200,
            [],
            [ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function () {
                return 'symfony4';
            }]
        );
    }

    /**
     * @Route("/addClub1", name="add_Club1")
     * @Method("POST")
     */

    public function ajouterClubAction(Request $request)
    {
        $Club = new Club();
        $nom = $request->query->get("nom");
        $localisation = $request->query->get("localisation");
        $description = $request->query->get("description");
        $telephone = $request->query->get("telephone");
        $type_activite = $request->query->get("type_activite");
        $image = $request->query->get("image");

        $em = $this->getDoctrine()->getManager();
        $Club->setNom($nom);
        $Club->setLocalisation($localisation);
        $Club->setDescription($description);
        $Club->setTelephone($telephone);
        $Club->setTypeActivite($type_activite);
        $Club->setImage($image);

        $em->persist($Club);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Club);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/updateClub1", name="update_Club1")
     * @Method("POST")
     */

    public function modifierClubAction(ClubRepository $clubRepository, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $club = $clubRepository->find($request->get('id'));

        $club->setNom($request->get('nom'));
        $club->setLocalisation($request->get('localisation'));
        $club->setDescription($request->get('description'));
        $club->setTelephone($request->get('telephone'));

        $club->setTypeActivite($request->get('type_activite'));

        $club->setImage($request->get('image'));



        $em->persist($club);
        $em->flush();

        return new JsonResponse($club);
    }

    /**
     * @Route("/deleteClub1", name="delete_Club1")
     * @Method("DELETE")
     */

    public function deleteClubAction(Request $request)
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Club = $em->getRepository(Club::class)->find($id);
        if ($Club != null) {
            $em->remove($Club);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("club a ete supprimee avec success.");
            return new JsonResponse($formatted);
        }
        return new JsonResponse("id Seance invalide.");
    }
    /*
    #[Route('/listClubClientAPI', name: 'club_index_api')]
    public function list(SerializerInterface $serializer): JsonResponse
    {
        $clubs = $this->getDoctrine()
            ->getRepository(Club::class)
            ->findAll();

        $json = $serializer->serialize($clubs, 'json', [
            'groups' => ['club_list']
        ]);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/showClubDetailsAPI/{id}', name: 'club_show_api')]
    public function details($id, SerializerInterface $serializer): JsonResponse
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->find($id);

        $formatted = $serializer->serialize($club, 'json', [
            'groups' => ['club_details'],
        ]);

        return new JsonResponse($formatted, 200, [], true);
    } */
}
