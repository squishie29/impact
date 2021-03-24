<?php

namespace App\Controller;

use App\Entity\Options;
use App\Form\OptionsType;
use App\Repository\GalleryRepository;
use App\Repository\HotelRepository;
use App\Repository\OptionsRepository;
use App\Repository\ReservationHotelRepository;
use App\Repository\RoomRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash")
 */
class hotelDepartementController extends AbstractController
{

    /**
     * @Route("/", name="options_test", methods={"GET"})
     */
    public function indexAction(OptionsRepository $optionsRepository,HotelRepository $hotelRepository,ReservationHotelRepository $reservationHotelRepository,GalleryRepository $galleryRepository,RoomRepository $roomRepository)
    {
        $options = $optionsRepository->findAll();
        $hotels = $hotelRepository->findAll();
        $rooms = $roomRepository->findAll();
        $galleris = $galleryRepository->findAll();
        $reservations = $reservationHotelRepository->findAll();
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Gestion Hotel', 'Number per section'],
                ['Number of options',     count($options)],
                ['Number of hotels',     count($hotels)],
                ['Number of rooms',     count($rooms)],
                ['Number of galleris',     count($galleris)],
                ['Number of reservations',     count($reservations)]

            ]
        );

        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(700);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);


        //char2

        $star1 = $hotelRepository->findBy(
            ['stars' => '1']);

        $star2 = $hotelRepository->findBy(
            ['stars' => '2']);

        $star3 = $hotelRepository->findBy(
            ['stars' => '3']);

        $star4 = $hotelRepository->findBy(
            ['stars' => '4']);

        $star5 = $hotelRepository->findBy(
            ['stars' => '5']);

        $star6 = $hotelRepository->findBy(
            ['stars' => '6']);

        $star7 = $hotelRepository->findBy(
            ['stars' => '7']);

        $bar = new BarChart();
        $bar->getData()->setArrayToDataTable(
            [['', ''],
                ['1 stars',     count($star1)],
                ['2 stars',     count($star2)],
                ['3 stars',     count($star3)],
                ['4 stars',     count($star4)],
                ['5 stars',     count($star5)],
                ['6 stars',     count($star6)],
                ['7 stars',     count($star7)]



            ]
        );

        $bar->getOptions()->getHAxis()->setMinValue(0);
        $bar->getOptions()->setWidth(400);
        $bar->getOptions()->setHeight(500);





        return $this->render('hoteldepartement.html.twig', [
            'pieChart' => $pieChart,
            'barchart' => $bar,

        ]);
    }


}


