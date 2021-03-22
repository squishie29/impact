<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Options;
use App\Entity\ReservationHotel;
use App\Entity\Room;
use App\Entity\Utilisateur;
use App\Form\ReservationHotelType;
use App\Repository\GalleryRepository;
use App\Repository\HotelRepository;
use App\Repository\OptionsRepository;
use App\Repository\RoomRepository;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class FrontController extends AbstractController
{
    /**
     * @Route("/front", name="front")
     */
    public function index(): Response
    {

        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    /**
     * @Route("/hotels", name="hotels", methods={"GET"})
     */
    public function affichehotels(HotelRepository $hotelRepository): Response
    {
        return $this->render('front/hotels.html.twig', [
            'hotels' => $hotelRepository->findAll(),

        ]);
    }
    /**
     * @Route("/hotels/{id}", name="hotel_shows", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(Hotel $hotel, RoomRepository $roomRepository,OptionsRepository $optionsRepository,GalleryRepository $galleryRepository): Response
    {



        $geocoder = new \OpenCage\Geocoder\Geocoder('50ca3024fa7f45bca607020d281a8faa');
        $result = $geocoder->geocode('tunis');

        return $this->render('front/details.html.twig', [
            'hotel' => $hotel,
            'rooms' => $roomRepository->findAll(),
            'options' =>$optionsRepository->findAll(),
            'galleries' =>$galleryRepository->findAll(),
            'result' =>$result,

        ]);

    }


    /**
     * @Route("/reservationNew/{id}", name="reservationNew", methods={"GET","POST"},requirements={"id"="\d+"})
     */
    public function reservationNew(Hotel $hotel,Request $request): Response
    {
        $reservationHotel = new ReservationHotel();
        $form = $this->createForm( ReservationHotelType::class, $reservationHotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservationHotel);
            $entityManager->flush();

            return $this->redirectToRoute('hotel_shows');
        }

        return $this->render('front/hotelsReservation.html.twig', [
            'reservation_hotel' => $reservationHotel,
            'form' => $form->createView(),
            'hotel' => $hotel,

        ]);
    }
    /**
     * @Route("/reservationHotels/{id}", name="reservation_hotel_new2", methods={"GET","POST"},requirements={"id"="\d+"})
     * @param HotelRepository $hotelRepository
     * @param Hotel $hotel
     * @param $id
     * @return Response
     */
    public function test2(HotelRepository $hotelRepository, Hotel $hotel, $id)
    {
        $hotel=$hotelRepository->find(["id" => $id]);
        $rooms=$hotel->getRooms();
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(Utilisateur::class)->findOneByEmail("azeaze@email.com");
        dump($user);
        return $this->render('front/hotelsReservation.html.twig', [
            'rooms' => $rooms,
            'hotel' => $hotel,
        ]);
    }
    /**
     * @Route("/reservation_new", name="reservation_new", methods={"GET","POST"},requirements={"id"="\d+"})
     * @param HotelRepository $hotelRepository
     * @param Hotel $hotel
     * @param $id
     * @return Response
     */

    public function reservation_new(Request $request){
        $em = $this->getDoctrine()->getManager();

        $req  = $request->getContent();
        $req = json_decode($req,true);

        $debut = $req['debut'];
        $fin = $req['fin'];
        $usermail = $req['usermail'];
        $room = $req['room'];

//        $user=$em->getRepository("AppBundle:Utilisateur")->findOneByEmail($usermail);
//        $room=$em->getRepository("AppBundle:Room")->findOneByType($usermail);

        $user = $em->getRepository(Utilisateur::class)->findOneByEmail($usermail);
        dump($user);

        $reservationHotel = new ReservationHotel();
        $reservationHotel->setDebut($debut);
        $reservationHotel->setFin($fin);
        $reservationHotel->setUserId($usermail);
        $reservationHotel->setRoomId($room);
        $em->persist($reservationHotel);
        $em->flush();

        return false;
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository('AppBundle:Post')->findEntitiesByString($requestString);
        if(!$posts) {
            $result['hotels']['error'] = "hotel Not found :( ";
        } else {
            $result['hotels'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    /**
     * @Route("/search", name="search")
     */
    public function getRealEntities($hotels){
        foreach ($hotels as $hotels){
            $realEntities[$hotels->getId()] = [$hotels->getPhoto(),$hotels->getTitle()];

        }
        return $realEntities;
    }

    /**
     * @Route("/searchHotelx ", name="searchHotelx")
     */
    public function searchHotelx(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Hotel::class);
        $requestString=$request->get('searchValue');
        $hotels= $repository->findHotelByName($requestString);
        $jsonContent = $Normalizer->normalize($hotels, 'json',['groups'=>'post:read']);
        $retour=json_encode($jsonContent);
        return new Response($retour);

    }
}
