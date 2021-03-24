<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/hotel")
 */
class HotelController extends AbstractController
{
    /**
     * @Route("/", name="hotel_index", methods={"GET"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,HotelRepository $hotelRepository): Response
    {


        $table = $dataTableFactory->create()

            ->add('name', TextColumn::class, ['label' => 'name', 'orderable'=> true,'searchable'=>true,])
            ->add('stars', TextColumn::class, ['label' => 'stars', 'orderable'=> true,'searchable'=>false,])
            ->add('photo', TextColumn::class, ['label' => 'photo', 'orderable'=> true,'searchable'=>false,])
            ->add('description', TextColumn::class, ['label' => 'description', 'orderable'=> true,'searchable'=>false,])
            ->add('adress', TextColumn::class, ['label' => 'adress', 'orderable'=> true,'searchable'=>false,])

            ->add('id', TextColumn::class, ['orderable'=> false,'label' => 'ACTION','searchable'=>false,'render' => function($value, $context) {
                return sprintf('<a href="%u">SHOW</a> <a href="%d/edit">EDIT</a> ', $value,$value);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Hotel::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('hotel/index.html.twig', [
            'datatable4' => $table,
            'Hotel' => $hotelRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="hotel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('photo')->getData();


            if ($image)
            {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $originalFilename;
                $fileName = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('imagehotel_directory'),$fileName);
                } catch (FileException $e)
                {

                }

                $hotel->setPhoto($fileName);
            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hotel);
            $entityManager->flush();


            return $this->redirectToRoute('hotel_index');
        }

        return $this->render('hotel/new.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hotel_show", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(Hotel $hotel): Response
    {
        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="hotel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hotel $hotel): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);




        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('photo')->getData();


            if ($image)
            {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $originalFilename;
                $fileName = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('imagehotel_directory'),$fileName);
                } catch (FileException $e)
                {

                }

                $hotel->setPhoto($fileName);
            }



            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hotel_index');
        }

        return $this->render('hotel/edit.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hotel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Hotel $hotel): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hotel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hotel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('hotel_index');
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
