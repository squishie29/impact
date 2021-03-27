<?php

namespace App\Controller;

use App\Entity\ReservationHotel;
use App\Form\ReservationHotelType;
use App\Repository\ReservationHotelRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation/hotel")
 */
class ReservationHotelController extends AbstractController
{
    /**
     * @Route("/", name="reservation_hotel_index", methods={"GET"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,ReservationHotelRepository $reservationHotelRepository): Response
    {
        $table = $dataTableFactory->create()

            ->add('userId', TextColumn::class, ['label' => 'userId', 'orderable'=> true, 'field' => 'userId.email'])
            ->add('roomId', TextColumn::class, ['label' => 'roomId', 'orderable'=> true, 'field' => 'roomId.id'])
            ->add('debut', DateTimeColumn::class, ['label' => 'date debut','format' => 'd-m-Y'])
            ->add('fin', DateTimeColumn::class, ['label' => 'date fin','format' => 'd-m-Y'])
            ->add('confirmation', TextColumn::class, ['label' => 'Confirmation', 'orderable'=> true])
            ->add('id', TextColumn::class, ['orderable'=> false,'label' => 'ACTION','searchable'=>false,'render' => function($value, $context) {
                return sprintf('<a href="%u">SHOW</a> <a href="%d/edit">EDIT</a>', $value,$value);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => reservationHotel::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('reservation_hotel/index.html.twig', [
            'datatable2' => $table,
            'reservation_hotels' => $reservationHotelRepository->findAll(),

        ]);

    }

    /**
     * @Route("/new", name="reservation_hotel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reservationHotel = new ReservationHotel();
        $form = $this->createForm(ReservationHotelType::class, $reservationHotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservationHotel);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_hotel_index');
        }

        return $this->render('reservation_hotel/new.html.twig', [
            'reservation_hotel' => $reservationHotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_hotel_show", methods={"GET"})
     */
    public function show(ReservationHotel $reservationHotel): Response
    {
        return $this->render('reservation_hotel/show.html.twig', [
            'reservation_hotel' => $reservationHotel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_hotel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ReservationHotel $reservationHotel): Response
    {
        $form = $this->createForm(ReservationHotelType::class, $reservationHotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_hotel_index');
        }

        return $this->render('reservation_hotel/edit.html.twig', [
            'reservation_hotel' => $reservationHotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_hotel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ReservationHotel $reservationHotel): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationHotel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservationHotel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_hotel_index');
    }
}
