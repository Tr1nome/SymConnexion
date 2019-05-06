<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="accueilClass_")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $random = random_int(0, 1200);
        return $this->render('default/index.html.twig');

    }

    /**
     * @Route("/admin", name="indexAdmin")
     *
     */
    public function indexAdmin()
    {
        //$this->deny
        $random = random_int(0, 1200);
        return $this->render('default/index.html.twig');

    }

}