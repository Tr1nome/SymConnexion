<?php

namespace App\ApiController;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/user", host="api.connexion.fr")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="user_show_api",
     * )
     * @Rest\View()
     */
    public function show(User $user): View
    {
        return View::create($user, Response::HTTP_OK);
    }
    /**
     * Edit a User
     * @Rest\Patch(
     *     path = "/edit/{id}",
     *     name = "user_edit_api",
     * )
     * @param Request $request
     * @Rest\View()
     * @return View;
     */
    public function edit(Request $request, User $user, ImageRepository $imageRepository): View
    {
        if ($user){
            $em = $this->getDoctrine()->getManager();
            if (!empty($request->get('image')))
            {
                $currentImage = $user->getImage();
                if (!empty($currentImage)){
                    if($currentImage){
                        $this->removeFile($currentImage->getPath());
                        $em->remove($currentImage);
                        $user->setImage(null);
                    }
                }
                $image = $imageRepository->find($request->get('image'));
                $user->setImage($image);
            }
            $em->persist($user);
            $em->flush();
        }
        return View::create($user, Response::HTTP_OK);
    }

    private function removeFile($path)
    {
        if(file_exists($path))
        {
            unlink($path);
        }
    }

}
