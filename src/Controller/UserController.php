<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/users", host="admin.fenrir-studio.fr")
 */
class UserController extends AbstractController{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request, UserManagerInterface $userManager, PaginatorInterface $paginator): Response
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

    /**
     * @Route("/adhere/{id}", name="user_adhere", methods={"GET"})
     */
    public function adhere(UserManagerInterface $userManager, Request $request): Response
    {
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));
        $user->setAdherent(true);
        $userManager->updateUser($user);
        
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/unadhere/{id}", name="user_unadhere", methods={"GET"})
     */
    public function unadhere(UserManagerInterface $userManager, Request $request): Response
    {
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));
        $user->setAdherent(false);
        $userManager->updateUser($user);
        
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/promote/{id}", name="user_promote")
     */
    public function promoteUserAction(UserManagerInterface $userManager, Request $request): Response{
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));    
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/demote/{id}", name="user_demote")
     */
    public function demoteUserAction(UserManagerInterface $userManager, Request $request): Response{
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));    
        $user->removeRole('ROLE_ADMIN');
        $userManager->updateUser($user);
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/former/{id}", name="user_former")
     */
    public function formerUserAction(UserManagerInterface $userManager, Request $request): Response{
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));    
        $user->setFormateur(true);
        $userManager->updateUser($user);
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/unformer/{id}", name="user_unformer")
     */
    public function unformerUserAction(UserManagerInterface $userManager, Request $request): Response{
        $user = $userManager->findUserBy(array('id'=> $request->get('id')));    
        $user->setFormateur(false);
        $userManager->updateUser($user);
        return $this->render('user_list/index.html.twig', [
            'users' => $userManager->findUsers(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET"})
     */
    public function editAction(Request $request, UserManagerInterface $userManager)
    {
        $user = $userManager->findUserBy(['id']);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_list/index.html.twig');
        }

        return $this->render('user/edit_user.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),

        ));
}
}