<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gallery")
 */
class GalleryController extends AbstractController
{
    /**
     * @Route("/", name="gallery_index", methods={"GET"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,GalleryRepository $galleryRepository): Response
    {
        $table = $dataTableFactory->create()

            ->add('imgpath', TextColumn::class, ['label' => 'image path', 'orderable'=> true,])
            ->add('hotel_id', TextColumn::class, ['label' => 'HOTEL', 'orderable'=> true, 'field' => 'hotel_id.name','searchable'=>true,])
            ->add('id', TextColumn::class, ['orderable'=> false,'label' => 'ACTION','searchable'=>false,'render' => function($value, $context) {
                return sprintf('<a href="%u">SHOW</a> <a href="%d/edit">EDIT</a>', $value,$value);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Gallery::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('gallery/index.html.twig', [
            'datatable3' => $table,
            'gallery' => $galleryRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="gallery_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('imgpath')->getData();


            if ($image)
            {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $originalFilename;
                $fileName = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('imagegallery_directory'),$fileName);
                } catch (FileException $e)
                {

                }

                $gallery->setImgpath($fileName);
            }





            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gallery);
            $entityManager->flush();

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gallery_show", methods={"GET"})
     */
    public function show(Gallery $gallery): Response
    {
        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gallery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Gallery $gallery): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gallery_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Gallery $gallery): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gallery_index');
    }
}
