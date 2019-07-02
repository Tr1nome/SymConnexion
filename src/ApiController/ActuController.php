<?php

namespace App\ApiController;

use App\Entity\Actu;
use App\Form\ActuType;
use App\Repository\ActuRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/actu", host="api.fenrir-studio.fr")
 */
class ActuController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="actu_api",
     * )
     * @Rest\View()
     */
    public function index(ActuRepository $actuRepository): View
    {
        $actus = $actuRepository->findAll();
        return View::create($actus, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="actushow_api",
     * )
     * @Rest\View()
     */
    public function show(Actu $actu): View
    {
        return View::create($actu, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="actunew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $actu = new Actu();
        $actu->setTitle($request->get('title'));
        $actu->setContent($request->get('content'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($actu);
        $em->flush();
        return View::create($actu, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="actuedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Actu $actu): View
    {
        if($actu) {
            $actu->setTitle($request->get('title'));
            $actu->setContent($request->get('content'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="actupatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request, Actu $actu): View
    {
        if($actu) {
            $form = $this->createForm(ActuType::class, $actu);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="actudelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Actu $actu): View
    {
        if ($actu) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actu);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}