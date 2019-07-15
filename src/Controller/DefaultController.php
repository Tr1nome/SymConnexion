<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/", name="accueilClass_", host="admin.fenrir-studio.fr")
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
     * @Route("/redirectionTo", name="redirectTo")
     */
    public function redirection()
    {
        $user = $this->getUser();
        if($user->hasRole('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->redirectToRoute('to_ng');
    }

}