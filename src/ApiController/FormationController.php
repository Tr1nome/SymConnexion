<?php

namespace App\ApiController;

use App\Entity\Formation;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/formation", host="api.connexion.fr")
 */
class FormationController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="formation_api",
     * )
     * @Rest\View()
     */
    public function index(FormationRepository $formationRepository): View
    {
        $formations = $formationRepository->findAll();
        return View::create($formations, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="formationshow_api",
     * )
     * @Rest\View()
     */
    public function show(Formation $formation): View
    {
        return View::create($formation, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="formationnew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        $formation = new Formation();
        $image = new Image();
        $formation->setName($request->get('name'));
        $formation->setDescription($request->get('description'));
        $formation->setImage($request->get('image'));
        $formation->setUser($this->getUser());
        $em->persist($formation);
        $em->persist($image);
        $em->flush();
        return View::create($formation, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="formationedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Formation $formation): View
    {
        if($formation) {
            $formation->setName($request->get('name'));
            $formation->setDescription($request->get('description'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();
        }
        return View::create($formation, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="formationpatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Formation $formation, User $user): View
    {
        if($formation) {  
        $user = $this->getUser();
        $formation->addUser($user);
        $form = $this->createForm(FormationType::class, $formation);
        $form->submit($request->request->all(), true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($formation);
        $em->flush();  
        }
        return View::create($formation, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="formationdelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Formation $formation): View
    {
        if ($formation) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
    /**
     * @Rest\POST(
     *   path="/{id}/registerTo",
     *   name="formationregist_api",
     * )
     * @Rest\View()
     */
    public function inscription(Request $request, Formation $formation, User $user): View
    {
        $formation->addUser($request->get('user'));
    }
}