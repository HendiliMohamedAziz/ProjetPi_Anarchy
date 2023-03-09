<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\SeanceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class Reservation_Controller extends AbstractController
{
    /**
     * @Route("/reservation/json", name="ReservationsJsonAction")
     * @throws ExceptionInterface
     */
    public function ReservationsJsonAction(ReservationRepository $ReservationRepository): JsonResponse
    {
        $reservations = new Reservation();
        $reservations = $ReservationRepository->allReservations();

        return $this->json([
            'data'=>$reservations
      ],200,[],[ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function()
      {return 'symfony4';}]
      );
    }

    /**
     * @Route("/reservations/json/new", name="ReservationsJsonNewAction")
     */
    public function newReservationJson(SeanceRepository $SeanceRepository, Request $request): JsonResponse
    {
        $reservation = new Reservation();
       // $user = $userRepository->find($request->get('user'));
        $seance = $SeanceRepository->find($request->get('seance'));

        $em = $this->getDoctrine()->getManager();
        $reservation->setDate(new \DateTime());
       // $reservation->setDate($request->get('date'));
        $reservation->setSeance($seance);
        
        $em->persist($reservation);
        $em->flush();

        return new JsonResponse($reservation);
    }


    /**
     * @Route("/reservations/json/update", name="ReservationsJsonUpdateAction")
     */
    public function updateReservationJson(ReservationRepository $reservationRepository, Request $request): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();

        $reservation = $reservationRepository->find($request->get('id'));
        $reservation->setDate(new \DateTime());
        $em->flush();

        return new JsonResponse($reservation);
    }


     /**
     * @Route("/reservations/json/delete", name="deleteReservationsJsonAction")
     * @throws ExceptionInterface
     */
    public function deleteReservationsJsonAction(ReservationRepository $reservationRepository, Request $request): JsonResponse
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository(Reservation::class)->find($id);
        if($reservation!=null ) {
            $em->remove($reservation);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Reservation a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Reservation invalide.");
}



}
?>