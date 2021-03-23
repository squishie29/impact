<?php

namespace App\Controller;

use App\Entity\Options;
use App\Form\OptionsType;
use App\Repository\OptionsRepository;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/options")
 */
class OptionsController extends AbstractController
{
    /**
     * @Route("/", name="options_index", methods={"GET"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory,OptionsRepository $optionsRepository)
    {
        $table = $dataTableFactory->create()

            ->add('description', TextColumn::class,['label' => 'Description','searchable'=>true])
            ->add('room_id', TextColumn::class,['field' => 'room_id.id','label' => 'Room Id','searchable'=>false])
            ->add('id', TextColumn::class, ['orderable'=> false,'label' => 'ACTION','searchable'=>false,'render' => function($value, $context) {
                return sprintf('<a href="%u">SHOW</a> <a href="%d/edit">EDIT</a>', $value,$value);
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Options::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('options/index.html.twig', [
            'datatable' => $table,
            'options' => $optionsRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="options_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $option = new Options();
        $form = $this->createForm(OptionsType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('options_index');
        }

        return $this->render('options/new.html.twig', [
            'option' => $option,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="options_show", methods={"GET"},requirements={"id"="\d+"})
     */
    public function show(Options $option): Response
    {
        return $this->render('options/show.html.twig', [
            'option' => $option,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="options_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Options $option): Response
    {
        $form = $this->createForm(OptionsType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('options_index');
        }

        return $this->render('options/edit.html.twig', [
            'option' => $option,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="options_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Options $option): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('options_index');
    }



}


