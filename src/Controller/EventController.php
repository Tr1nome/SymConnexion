<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Egyg33k\CsvBundle;
use Egyg33k\CsvBundle\Services\Writer;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(Request $request, EventRepository $eventRepository, PaginatorInterface $paginator): Response
    {
        $events = $eventRepository->findAll();
        $paginatedEvents = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('event/index.html.twig', [
            'events' => $paginatedEvents,
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_index', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * @Route("/{id}/export", name="event_export", methods= "GET")
     */
    public function exportAction(Event $event)
    {
        $em= $this->getDoctrine()->getManager();
        $users = $event->getUser();
        #Writer
        $writer = new Writer();
        $csv = $writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['NOM D\'UTILISATEUR','NOM','PRENOM','EMAIL','ADHERENT']);
        foreach ($users as $user) {
            
            $csv->insertOne([$user->getUsername(), $user->getLname(), $user->getFname(), $user->getEmail(), $user->getAdherent()]);
        }
        $csv->output($event->getName().' users.csv');
        die;
    }
}
