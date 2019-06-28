<?php

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayType;
use App\Repository\DayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/day")
 */
class DayController extends AbstractController
{
    /**
     * @Route("/", name="day_index", methods={"GET"})
     */
    public function index(DayRepository $dayRepository): Response
    {
        return $this->render('day/index.html.twig', [
            'days' => $dayRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="day_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $day = new Day();
        $form = $this->createForm(DayType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($day);
            $entityManager->flush();

            return $this->redirectToRoute('day_index');
        }

        return $this->render('day/new.html.twig', [
            'day' => $day,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="day_show", methods={"GET"})
     */
    public function show(Day $day): Response
    {
        return $this->render('day/show.html.twig', [
            'day' => $day,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="day_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Day $day): Response
    {
        $form = $this->createForm(DayType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('day_index', [
                'id' => $day->getId(),
            ]);
        }

        return $this->render('day/edit.html.twig', [
            'day' => $day,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="day_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Day $day): Response
    {
        if ($this->isCsrfTokenValid('delete'.$day->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($day);
            $entityManager->flush();
        }

        return $this->redirectToRoute('day_index');
    }
}
