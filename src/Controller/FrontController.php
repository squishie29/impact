<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Room;
use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    { dump("test");
        return $this->render('front/hotels.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }
    /**
     * @Route("/hotels/{id}", name="hotel_shows", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(Hotel $hotel): Response
    {
        return $this->render('front/details.html.twig', [
            'hotel' => $hotel,
        ]);
    }
}
