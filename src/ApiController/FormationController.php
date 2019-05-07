<?php

namespace App\ApiController;

use App\Repository\FormationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/formation", host="api.connexion.fr")
 */
class FormationController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="formation_api", methods={ "GET" })
     * @Rest\View()
     */
    public function index(FormationRepository $formationRepository): View
    {
        $formations = $formationRepository->findAll();
        return View::create($formations, Response::HTTP_OK);
    }
}