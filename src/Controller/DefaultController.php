<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use App\Entity\Formation;
use App\Entity\Event;
use App\Entity\Actu;
use App\Repository\ImageRepository;
use App\Repository\FormationRepository;
use App\Repository\EventRepository;
use App\Repository\ActuRepository;

/**
 * @Route("/home", name="accueilClass_", host="admin.fenrir-studio.fr")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ImageRepository $imageRepo, FormationRepository $formationrepo, EventRepository $eventRepo, ActuRepository $actuRepo)
    {
        $images = $imageRepo->findAll();
        $formations = $formationrepo->findAll();
        $events = $eventRepo->findAll();
        $actus = $actuRepo->findAll();
        $random = random_int(0, 1200);
        return $this->render('default/index.html.twig', [
            'actus'=>$actus,'formations'=> $formations,'events'=>$events,'images'=>$images,
        ]);

    }

}