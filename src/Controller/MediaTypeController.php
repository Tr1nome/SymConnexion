<?php

namespace App\Controller;

use App\Entity\MediaType;
use App\Form\MediaTypeType;
use App\Repository\MediaTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/media/type")
 */
class MediaTypeController extends AbstractController
{
    /**
     * @Route("/", name="media_type_index", methods={"GET"})
     */
    public function index(MediaTypeRepository $mediaTypeRepository): Response
    {
        return $this->render('media_type/index.html.twig', [
            'media_types' => $mediaTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="media_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mediaType = new MediaType();
        $form = $this->createForm(MediaTypeType::class, $mediaType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mediaType);
            $entityManager->flush();

            return $this->redirectToRoute('media_type_index');
        }

        return $this->render('media_type/new.html.twig', [
            'media_type' => $mediaType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="media_type_show", methods={"GET"})
     */
    public function show(MediaType $mediaType): Response
    {
        return $this->render('media_type/show.html.twig', [
            'media_type' => $mediaType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="media_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MediaType $mediaType): Response
    {
        $form = $this->createForm(MediaTypeType::class, $mediaType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('media_type_index', [
                'id' => $mediaType->getId(),
            ]);
        }

        return $this->render('media_type/edit.html.twig', [
            'media_type' => $mediaType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="media_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MediaType $mediaType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mediaType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mediaType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('media_type_index');
    }
}
