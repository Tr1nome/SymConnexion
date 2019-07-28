<?php

namespace App\ApiController;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\Event\ContactSentEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/contact", host="api.fenrir-studio.fr")
 */
class ContactController extends AbstractFOSRestController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * @Rest\Get(
     * path = "/",
     * name="contact_api",
     * )
     * @Rest\View()
     */
    public function index(ContactRepository $contactRepository): View
    {
        $contacts = $contactRepository->findAll();
        return View::create($contacts, Response::HTTP_OK);
    }

    

    /**
     * @Rest\Post(
     * path = "/new",
     * name="contact_new_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $contact = new Contact();
        $contact->setMail($request->get('mail'));
        $contact->setMessage($request->get('message'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();
        $contactEvent = new ContactSentEvent($contact);
        $this->dispatcher->dispatch('contact.sent', $contactEvent);
        return View::create($contact, Response::HTTP_CREATED);
    }

    

}