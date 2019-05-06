<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Form\ListItemType;
use App\Repository\ListItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/list/item")
 */
class ListItemController extends AbstractController
{
    /**
     * @Route("/", name="list_item_index", methods={"GET"})
     */
    public function index(ListItemRepository $listItemRepository): Response
    {
        return $this->render('list_item/index.html.twig', [
            'list_items' => $listItemRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="list_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $listItem = new ListItem();
        $form = $this->createForm(ListItemType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listItem);
            $entityManager->flush();

            return $this->redirectToRoute('list_item_index');
        }

        return $this->render('list_item/new.html.twig', [
            'list_item' => $listItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="list_item_show", methods={"GET"})
     */
    public function show(ListItem $listItem): Response
    {
        return $this->render('list_item/show.html.twig', [
            'list_item' => $listItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="list_item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ListItem $listItem): Response
    {
        $form = $this->createForm(ListItemType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_item_index', [
                'id' => $listItem->getId(),
            ]);
        }

        return $this->render('list_item/edit.html.twig', [
            'list_item' => $listItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="list_item_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ListItem $listItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($listItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_item_index');
    }
}
