<?php

namespace App\Controller;

use App\Entity\TaskList;
use App\Form\TaskListType;
use App\Repository\TaskListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task/list")
 */
class TaskListController extends AbstractController
{
    /**
     * @Route("/", name="task_list_index", methods={"GET"})
     */
    public function index(TaskListRepository $taskListRepository): Response
    {
        return $this->render('task_list/index.html.twig', [
            'task_lists' => $taskListRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="task_list_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $taskList = new TaskList();
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskList);
            $entityManager->flush();

            return $this->redirectToRoute('task_list_index');
        }

        return $this->render('task_list/new.html.twig', [
            'task_list' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_list_show", methods={"GET"})
     */
    public function show(TaskList $taskList): Response
    {
        return $this->render('task_list/show.html.twig', [
            'task_list' => $taskList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_list_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TaskList $taskList): Response
    {
        $form = $this->createForm(TaskListType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_list_index', [
                'id' => $taskList->getId(),
            ]);
        }

        return $this->render('task_list/edit.html.twig', [
            'task_list' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_list_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TaskList $taskList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_list_index');
    }
}
