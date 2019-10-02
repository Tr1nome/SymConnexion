<?php

namespace App\ApiController;

use App\Entity\Job;
use App\Entity\User;
use App\Form\JobType;
use App\Repository\JobRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\ApiController\AuthController;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/job", host="api.fenrir-studio.fr")
 */
class JobController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="job_api",
     * )
     * @Rest\View()
     */
    public function index(JobRepository $jobRepository): View
    {
        $jobs = $jobRepository->findAll();
        $jobs = $this->normalize($jobs);
        return View::create($jobs, Response::HTTP_OK);
    }

    

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null, ['attributes' => [
                'id',
                'name']]);
        return $object;
    }
}