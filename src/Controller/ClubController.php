<?php

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Geocoder\Provider\Mapbox\Mapbox;
use Http\Adapter\Guzzle6\Client;


class ClubController extends AbstractController
{
    #[Route('/club', name: 'app_club')]
    public function index(): Response
    {
        return $this->render('club/index.html.twig', [
            'controller_name' => 'ClubController',
        ]);
    }
    /////////afficher la liste des clubs ajoutes selon l id du club owner qui il a ajoute
    #[Route('/club/owner/listClub', name: 'list_club')]
    public function listClub(ClubRepository $repository)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $userId = $user->getId();
        $clubs = $repository->findByIdClubowner($userId);
        // $clubs= $this->getDoctrine()->getRepository(ClubRepository::class)->findAll();
        return $this->render("club/listClub.html.twig", array("tabClub" => $clubs));
    }
    /*#[Route('/addClub', name: 'add_club')]
    public function addStudent(ManagerRegistry $doctrine)
    {
        $club= new Club();
        //$club->setId("258");
        $club->setNom("california");
        $club->setLocalisation("Ariana");
       // $em=$this->getDoctrine()->getManager();
        $em= $doctrine->getManager();
        $em->persist($club);
        $em->flush();
        return $this->redirectToRoute("list_club");
    }*/
    #[Route('/club/owner/addClub', name: 'add_club')]
    public function addClub(ClubRepository $repository, Request $request, SluggerInterface $slugger)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $club = new Club;
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('club_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $club->setImage($newFilename);
            }
            $club->setIdClubOwner($user);
            $club->setLatitude($form->get('latitude')->getData());
            $club->setLongitude($form->get('longitude')->getData());
            $repository->add($club, true);
            return $this->redirectToRoute("list_club");
            //return $this->render("club/listClub.html.twig");

        }
        return $this->renderForm("club/add.html.twig", array("formClub" => $form));
    }
    
    /*#[Route('/show', name: 'club_show')]
    public function show(){
        return $this->render("club/show.html.twig");
    }*/
    #[Route('/club/owner/updateClub/{id}', name: 'update_club')]
    public function  updateClub($id, ClubRepository $repository, ManagerRegistry $doctrine, Request $request)
    {
        $club = $repository->find($id);
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_club");
        }
        return $this->renderForm("club/updateClub.html.twig", array("formClub" => $form));
    }

    #[Route('/club/owner/removeClub/{id}', name: 'remove_club')]

    public function removeClub(ManagerRegistry $doctrine, $id, ClubRepository $repository)
    {
        $club = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($club);
        $em->flush();
        return  $this->redirectToRoute("list_club");
    }

    #[Route('/client/list', name: 'club_index')]
    public function list(): Response
    {
        $clubs = $this->getDoctrine()
            ->getRepository(Club::class)
            ->findAll();

        return $this->render('club/index.html.twig', [
            'clubs' => $clubs,
        ]);
    }
    #[Route('/client/showClub/{id}', name: 'club_show')]
    public function details($id): Response
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->find($id);

        return $this->render('club/detailsClub.html.twig', [
            'club' => $club,
            'latitude' => $club->getLatitude(),
            'longitude' => $club->getLongitude(),
        ]);
    }
    #[Route('/client/showMesClubs', name: 'mes_clubs')]
    public function showMesClubs(ParticipationRepository $participationRepository)
    {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $userId = $user->getUser();
        $mesClubs = $participationRepository->findMesClubs($userId);

        return $this->render('listParticipation.html.twig', [
            'mes_clubs' => $mesClubs,
        ]);
    }

    
    ////////////////////////mapbox
    /**
     * Get the geolocation of a club
     */
    public function getGeolocation($club)
    {
        // Create a new HTTP client
        $httpClient = new Client();

        // Set your Mapbox API token
        $token = 'pk.eyJ1Ijoib2JiYSIsImEiOiJjbGV6bnAxYm8wMHhqM3hwYTJjdDJzbDE3In0.Q4PmphqzTG9pJBXsNLz-Jw';

        // Create a new Mapbox provider
        $mapboxProvider = new Mapbox($httpClient, $token);

        // Retrieve the address of the club
        $address = $club->getLocalisation(); // Replace with the actual address field in your Club entity

        // Geocode the address to retrieve the coordinates
        $geocodeQuery = GeocodeQuery::create($address);
        $result = $mapboxProvider->geocodeQuery($geocodeQuery);

        if ($result->isEmpty()) {
            // Handle the case where the address could not be geocoded
        } else {
            $coordinates = $result->first()->getCoordinates();

            // Do something with the coordinates, such as storing them in the club entity
            $club->setLongitude($coordinates->getLongitude());
            $club->setLatitude($coordinates->getLatitude());

            // Save the changes to the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($club);
            $entityManager->flush();
        }
    }
}
