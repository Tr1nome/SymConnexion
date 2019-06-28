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
 * @Route("/users", host="connexion.fr")
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

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $formation): Response
    {
        $form = $this->createForm(UserType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $formation->getImage();
            $file = $form->get('image')->get('file')->getData();
            if ($file){
                $fileName = $this->generateUniqueFileName().'.'. $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $this->removeFile($image->getPath());
                $image->setPath($this->getParameter('images_directory').'/'.$fileName) ;
                $image->setImgpath($this->getParameter('images_path').'/'.$fileName);
                $entityManager->persist($image);
            }
            if (empty($image->getId()) && !$file ){
                $formation->setImage(null);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_list', [
                'id' => $formation->getId(),
            ]);
        }
        return $this->render('user/edit.html.twig', [
            'user' => $formation,
            'form' => $form->createView(),
        ]);
    }
}