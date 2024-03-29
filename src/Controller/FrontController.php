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
     * @Route("/{id}/check", name="reservation_hotel_check", methods={"GET","POST"})
     */
    public function checkout(Request $request, ReservationHotel $reservationHotel): Response
    {
        $form = $this->createForm(ReservationHotelType::class, $reservationHotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hotel_shows', array('id' => $reservationHotel->getRoomId()->getIdHotel()->getId()));
        }

        return $this->render('reservation_hotel/checkout.html.twig', [
            'reservation_hotel' => $reservationHotel,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/test", name="test",methods={"GET","POST"})
     */
    public function testing(Request $request, ReservationHotel $reservationHotel): Response
    {



        \Stripe\Stripe::setApiKey('sk_test_51IZIMOALxTyarhINuN861AZlmvVUdPgnPDVKTmOrohYm22DWk87No2mjp41qgYz3nulYwnDQTIQUHo9u9XGAAF5b00exSWSpoJ');

// Token is created using Stripe Checkout or Elements!
// Get the payment token ID submitted by the form:

        $amount = $request->request->get('_amount');
        $desc = $request->request->get('message');

        if ($amount!=null)
        {
            \Stripe\Charge::create([
                'amount' => $amount*100,
                'currency' => 'usd',
                'description' => $desc,
                'source' => 'tok_us',
            ]);

            return $this->redirectToRoute('reservation_hotel_check', array('id' => $reservationHotel->getId()));

        }

       // return $this->redirectToRoute('reservation_hotel_check', array('id' => $reservationHotel->getId()));

        return $this->render('front/test.html.twig', [
            'id' => $reservationHotel->getId(),
            'reservation_hotel'=>$reservationHotel,
        ]);


            /*

        return $this->render('reservation_hotel/test.html.twig', [
            'reservation_hotel' => $reservationHotel,

        ]);*/
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

            return $this->redirectToRoute('test', array('id' => $reservationHotel->getId()));
        }

        return $this->render('front/hotelsReservation.html.twig', [
            'reservation_hotel' => $reservationHotel,
            'form' => $form->createView(),
            'hotel' => $hotel,

        ]);
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
     * @Route("/listeHotels ", name="listeHotels")
     */
    public function searchHotelx(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Hotel::class);
        $requestString=$request->get('searchValue');
        $hotels= $repository->findHotelByName($requestString);
        $jsonContent = $Normalizer->normalize($hotels, 'json',['groups'=>'hotels']);
        $retour=json_encode($jsonContent);
        return new Response($retour);

    }




/*
    public function searchHotelx(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Hotel::class);
        $requestString=$request->get('searchValue');
        $hotels= $repository->findHotelByName($requestString);
        $jsonContent = $Normalizer->normalize($hotels, 'json',['groups'=>'hotelzzs']);
        $retour=json_encode($jsonContent);
        return new Response($retour);

    }*/
}
