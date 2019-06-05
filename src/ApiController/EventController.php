<?php

namespace App\ApiController;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/event", host="api.connexion.fr")
 */
class EventController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="event_api",
     * )
     * @Rest\View()
     */
    public function index(EventRepository $eventRepository): View
    {
        $event = $eventRepository->findAll();
        $mevent = $this->normalize($event);
        return View::create($mevent, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="eventshow_api",
     * )
     * @Rest\View()
     */
    public function show(Event $event): View
    {
        $mevent = $this->normalize($event);
        return View::create($mevent, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="eventnew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $event = new Event();
        $event->setName($request->get('name'));
        $event->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();
        return View::create($event, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="eventedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Event $event): View
    {
        if($event) {
            $event->setName($request->get('name'));
            $event->setDescription($request->get('description'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
        }
        return View::create($event, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="eventpatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Event $event): View
    {
        if($event) {
            $form = $this->createForm(EventType::class, $event);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
        }
        return View::create($event, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="eventdelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Event $event): View
    {
        if ($event) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'name',
                'description',
                'user' => ['id','username','image'=>['id','path','imgPath','alternative']],
                'image'=> ['id','file','path','imgPath'],
                
            ]]);
        return $object;
    }
}