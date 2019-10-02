<?php

namespace App\ApiController;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\ApiController\AuthController;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;

/**
 * @Route("/project", host="api.fenrir-studio.fr")
 */
class ProjectController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="project_api",
     * )
     * @Rest\View()
     */
    public function index(ProjectRepository $actuRepository): View
    {
        $actus = $actuRepository->findAll();
        $actualite = $this->normalize($actus);
        return View::create($actualite, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="project_show_api",
     * )
     * @Rest\View()
     */
    public function getProjectById(Project $project): View
    {
        $project = $this->normalize($project);
        return View::create($project, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="project_new_api",
     * )
     * @Rest\View()
     */
    public function createProject(ProjectRepository $projectRepository, Request $request, UserManagerInterface $userManager):View
    {
        $user = $this->getUser();
        $project = new Project();
        $em = $this->getDoctrine()->getManager();
        $project->setName($request->get('name'));
        $project->setDescription($request->get('description'));
        $project->setNeeded($request->get('needed'));
        if($project->getNeeded() === 0) {
            $project->setValidated(true);
        } else {
            $project->setValidated(false);
        }
        $project->setCreator($user);
        $em->persist($project);
        $em->flush();
        $project = $this->normalize($project);
        return View::create($project, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Patch(
     * path = "/{id}/validate",
     * name="project_validate_api",
     * )
     * @Rest\View()
     */
    public function validate(Project $project, Request $request): View
    {
        $user = $this->getUser();
        $project->addTeam($user);
        $project = $this->serialize($project);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return View::create($project, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     * path = "/delete/{id}",
     * name="project_delete_api",
     * )
     * @Rest\View()
     */
    public function deleteProject(Project $project, Request $request): View
    {
        if($project){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($project);
        $entityManager->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Patch(
     * path = "/{id}/unvalidate",
     * name="project_unvalidate_api",
     * )
     * @Rest\View()
     */
    public function unvalidate(Project $project, Request $request): View
    {
        $user = $this->getUser();
        $project->removeTeam($user);
        $project = $this->serialize($project);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return View::create($project, Response::HTTP_OK);
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
                'needed',
                'validated',
                'creator'=>['username','profilePicture'=>['imgPath']],
                'team'=>['username','profilePicture'=>['imgPath'],'jobs'=>['name']]
                
            ]]);
        return $object;
    }

    private function serialize($object)
    {
        /* Serializer, normalizer exemple */
        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id' 
            ]]);
        return $object;
    }

}