<?php

namespace App\ApiController;

use App\Entity\User;
use App\Event\UserCreatedEvent;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\UserRepository;


/**
 * @Rest\Route("/auth", host="api.fenrir-studio.fr")
 */

class AuthController extends AbstractFOSRestController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * @Rest\Post(
     *     path="/register",
     *     name="auth_register_api"
     * )
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return View
     */
    public function register(Request $request, UserManagerInterface $userManager)
    {
        $user = $userManager->createUser();
        $user
            ->setUsername($request->get('username'))
            ->setPlainPassword($request->get('password'))
            ->setEmail($request->get('email'))
            ->setEnabled(false)
            ->setRoles(['ROLE_USER'])
            ->setSuperAdmin(false)
        ;
        try {
            $em = $this->getDoctrine()->getManager();
            $userEvent = new UserCreatedEvent($user);
            $this->dispatcher->dispatch('user.registered', $userEvent);
            $em->persist($user);
            $em->flush();

        } catch (\Exception $e) {
            return View::create(["error" => $e->getMessage()], 500);
        }
        return View::create($user, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Get(
     *     path="/profile",
     *     name="auth_profile_api"
     * )
     */
    public function profile()
    {
        $user = $this->getUser();
        $user = $this->normalize($user);
        return View::create($user, Response::HTTP_OK);
        
    }

/**
     * @Rest\Put(
     *     path="/profile/edit",
     *     name="auth_edit_profile_api"
     * )
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return View
     */
    public function profileEdit(Request $request, UserManagerInterface $userManager, UserRepository $userRepository)
    {
        $user = $userRepository->find($this->getUser());
        $user->setUsername($request->get('username'));

        $userManager->updateUser($user);

        $user = $this->normalize($user);
        return View::create($user, Response::HTTP_OK);
    }


    private function normalize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null, ['attributes' => [
                'id',
                'email',
                'username',
                'roles',
                'image'=>['id','file','path','imgPath','alternative'],
                'formations'=>['name','description'],
                'events'=>['name','description'],
                'photos'=>['id','path','file','imgPath','alternative','title','description']
            ]]);
        return $object;
    }
}
