<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participation;
use App\Repository\ClubRepository;
use App\Entity\Club;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;





class ParticipationAPIController extends AbstractController
{


    /**
     * @Route("/participation/json", name="ParticipationsJsonAction")
     * @throws ExceptionInterface
     */
    public function ParticipationsJsonAction(ParticipationRepository $participationRepository): JsonResponse
    {
        $participations = $participationRepository->findAll();

        return $this->json(
            $participations,
            200,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function () {
                    return 'symfony4';
                }
            ]
        );
    }


    /**
     * @Route("/participations/json/new", name="ParticipationsJsonNewAction")
     */
    public function newParticipationJson(ClubRepository $clubRepository, Request $request): JsonResponse
    {
        $participation = new Participation();
        $club = $clubRepository->findOneBy(['id' => $request->query->get('id_club')]);

        $em = $this->getDoctrine()->getManager();
        $participation->setDateDebut(new \DateTime($request->get('dateDebut')));
        $participation->setDateFin(new \DateTime($request->get('dateFin')));
        $participation->setIdClub($club);

        $em->persist($participation);
        $em->flush();

        return new JsonResponse($participation);
    }

    /**
     * @Route("/participations/json/update", name="ParticipationsJsonUpdateAction")
     */
    public function updateParticipationJson(ParticipationRepository $participationRepository, ClubRepository $clubRepository, Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $participation = $participationRepository->find($request->get('id'));
        $club = $clubRepository->findOneBy(['id' => $request->get('id_club')]);



        $participation->setIdClub($club);
        $participation->setDateDebut(new \DateTime($request->get('DateDebut')));
        $participation->setDateFin(new \DateTime($request->get('DateFin')));

        $em->flush();

        return new JsonResponse($participation);
    }




    /**
     * @Route("/participations/json/delete", name="deleteParticipationsJsonAction")
     * @throws ExceptionInterface
     */
    public function deleteParticipationsJsonAction(ParticipationRepository $participationRepository, Request $request): JsonResponse
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $participation = $em->getRepository(Participation::class)->find($id);
        if ($participation != null) {
            $em->remove($participation);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("La participation a ete supprimee avec succes.");
            return new JsonResponse($formatted);
        }
        return new JsonResponse("Identifiant de participation invalide.");
    }
}
