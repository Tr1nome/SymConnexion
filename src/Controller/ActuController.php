<?php

namespace App\Controller;

use App\Entity\Actu;
use App\Form\ActuType;
use App\Repository\ActuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actu", host="admin.fenrir-studio.fr")
 */
class ActuController extends AbstractController
{
    /**
     * @Route("/", name="actu_index", methods={"GET"})
     */
    public function index(ActuRepository $actuRepository): Response
    {
        return $this->render('actu/index.html.twig', [
            'actus' => $actuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="actu_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $actu = new Actu();
        $form = $this->createForm(ActuType::class, $actu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actu);
            $entityManager->flush();

            return $this->redirectToRoute('actu_index');
        }

        return $this->render('actu/new.html.twig', [
            'actu' => $actu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="actu_show", methods={"GET"})
     */
    public function show(Actu $actu): Response
    {
        return $this->render('actu/show.html.twig', [
            'actu' => $actu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="actu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Actu $actu): Response
    {
        $form = $this->createForm(ActuType::class, $actu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('actu_index', [
                'id' => $actu->getId(),
            ]);
        }

        return $this->render('actu/edit.html.twig', [
            'actu' => $actu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="actu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Actu $actu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('actu_index');
    }
}
