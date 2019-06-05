<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/users")
 */
class UserController extends AbstractController{

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserManagerInterface $userManager): Response
    {
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="users_enabled", methods={"GET"})
     */
    public function getThisUser(User $user): Response
    {
        return $this->render('user_list/index.html.twig', [
            'users' => $user,
        ]);
    }

    /**
     * @Route("/allow", name="user_confirm", methods={"GET"})
     */
    public function allow(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user->setEnabled(true);
        $em->persist($user);
        $em->flush();
        
        return $this->render('user_list/index.html.twig', [
            'users' => $user,
        ]);
    }
}