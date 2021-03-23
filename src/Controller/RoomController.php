<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Options;
use App\Form\RoomType;
use App\Repository\OptionsRepository;
use App\Repository\RoomRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/", name="room_index", methods={"GET"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,RoomRepository $roomRepository,OptionsRepository $optionsRepository): Response
    {
        $table = $dataTableFactory->create()

            ->add('nb_personnes', TextColumn::class, ['label' => 'nb_personnes', 'orderable'=> true,])
            ->add('description', TextColumn::class, ['label' => 'description', 'orderable'=> true,])
            ->add('type', TextColumn::class, ['label' => 'type', 'orderable'=> true,])
            ->add('prix', TextColumn::class, ['label' => 'prix', 'orderable'=> true,])
            ->add('idHotel', TextColumn::class, ['label' => 'HOTEL', 'orderable'=> true, 'field' => 'idHotel.name','searchable'=>true,])
            ->add('option', TextColumn::class, ['label' => 'options', 'orderable'=> false, 'render' => function($value ,$context) {
                $name = $context->getId();

                $user = $this->getDoctrine()
                    ->getRepository(Options::class)
                    ->findBy(['room_id' => $name]);

                $comma_separated = implode(",", $user);
                if ($user==null)
                return null;
                    else
                        return $comma_separated;
            }])
            ->add('id', TextColumn::class, ['orderable'=> false,'label' => 'ACTION','searchable'=>false,'render' => function($value, $context) {
                return sprintf('<a href="%u">SHOW</a> <a href="%d/edit">EDIT</a>', $value,$value);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Room::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('Room/index.html.twig', [
            'datatable5' => $table,
            'gallery' => $roomRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="room_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_show", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index');
    }

    /**
     * @Route("/searchRoomx",name="searchRoomx")
     */
    public function searchRoomx(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Room::class);
        $requestString=$request->get('searchValue');
        $Rooms= $repository->findRoomByDescription($requestString);



        $jsonContent = $Normalizer->normalize($Rooms, 'html',['groups'=>'post:rooms']);

        $retour=json_encode($jsonContent,JSON_FORCE_OBJECT);

       // dd($retour);
       // echo json_last_error_msg();
        return new Response($retour);

    }
}
