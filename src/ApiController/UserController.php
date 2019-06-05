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
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\UserRepository;

/**
 * @Route("/user", host="api.connexion.fr")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="user_list_api",
     * )
     * @Rest\View()
     */
    public function index(UserManagerInterface $userManager): View
    {
        $users = $userManager->findUsers();
        $users = $this->normalize($users);
        return View::create($users,Response::HTTP_OK);
    }

    
    
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

    /**
     * Edit a User
     * @Rest\Patch(
     *     path = "/{id}/allow",
     *     name = "user_allow_api",
     * )
     * @param Request $request
     * @Rest\View()
     * @return View;
     */
    public function confirmUser(Request $request, User $user): View {
        $user->setEnabled(true);
        return View::create($user, Response::HTTP_OK);
    }

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'email',
                'username',
                'roles',
                'image'=>['id','file','path','imgPath']
            ]]);
        return $object;
    }

}
