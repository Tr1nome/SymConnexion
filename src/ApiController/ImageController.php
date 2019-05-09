<?php

namespace App\ApiController;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/image", host="api.connexion.fr")
 */
class ImageController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="image_api",
     * )
     * @Rest\View()
     */
    public function index(ImageRepository $imageRepository): View
    {
        $image = $imageRepository->findAll();
        return View::create($image, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="imageshow_api",
     * )
     * @Rest\View()
     */
    public function show(Image $image): View
    {
        return View::create($image, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="imagenew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $image = new Image();
        $image->setName($request->get('name'));
        $image->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();
        return View::create($image, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="imageedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Image $image): View
    {
        if($image) {
            $image->setName($request->get('name'));
            $image->setDescription($request->get('description'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($formation, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="imagepatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Image $image): View
    {
        if($image) {
            $form = $this->createForm(ImageType::class, $image);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($image, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="imagedelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Image $image): View
    {
        if ($image) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}