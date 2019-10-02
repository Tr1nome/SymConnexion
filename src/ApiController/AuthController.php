<?php

namespace App\ApiController;

use App\Entity\User;
use App\Entity\Job;
use App\Event\FilterUserRegistrationEvent;
use App\Entity\Image;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Repository\JobRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Rest\Route("/auth", host="api.fenrir-studio.fr")
 */

class AuthController extends AbstractFOSRestController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/redirectionTo", name="redirectTo")
     */
    public function redirection()
    {
        /*$user = $this->getUser();
        if($user->hasRole('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('fos_user_profile_show');
        }*/
        return $this->redirectToRoute('to_ng');
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
            ->setLname($request->get('lname'))
            ->setFname($request->get('fname'))
            ->setPlainPassword($request->get('password'))
            ->setEmail($request->get('email'))
            ->setEnabled(false)
            ->setRoles(['ROLE_USER'])
            ->setSuperAdmin(false)
            ->setFormateur(false)
            ->setAdherent(false)
        ;
        try {
            $em = $this->getDoctrine()->getManager();
            $this->eventDispatcher->dispatch('user_registration.created', new FilterUserRegistrationEvent($user, $request));
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
     * @Rest\Patch(
     *     path="/profile/jobs/add",
     *     name="auth_jobs_api"
     * )
     */
    public function addJob(UserManagerInterface $userManager, JobRepository $jobRepo, Request $request): View
    {
        $job = $jobRepo->findOneBy(array('id'=>$request->get('id')));
        $user = $this->getUser();
        $user->addJob($job);
        $userManager->updateUser($user);
        $user = $this->normalize($user);
        return View::create($user, Response::HTTP_OK);

    }

    /**
     * @Rest\Patch(
     *     path="/profile/jobs/remove",
     *     name="auth_jobs_rem_api"
     * )
     */
    public function removeJob(UserManagerInterface $userManager, JobRepository $jobRepo, Request $request): View
    {
        $job = $jobRepo->findOneBy(array('name'=>$request->get('name')));
        $user = $this->getUser();
        $user->removeJob($job);
        $userManager->updateUser($user);
        $user = $this->normalize($user);
        return View::create($user, Response::HTTP_OK);

    }



/**
     * @Rest\Patch(
     *     path="/profile/edit",
     *     name="auth_edit_profile_api"
     * )
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return View
     */
    public function profileEdit(Request $request, UserManagerInterface $userManager): View
    {
        $user = $userRepository->find($this->getUser());
        $currentImage = $user->getImage();

                if (!empty($currentImage)){
                    if($currentImage){
                        $this->removeFile($currentImage->getPath());
                        $em->remove($currentImage);
                        $user->setImage(null);
                    }
                }
        $image = new profilePicture();
        $file = $request->files->get('file');
        
        if ($file){
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $image->setPath($this->getParameter('images_directory').'/'.$fileName);
            $image->setImgPath($this->getParameter('images_path').'/'.$fileName);
            $image->setAllowed(true);
            //$image->setTitle($request->get('title'));
            //$image->setDescription($request->get('description'));
            $image->setUploadedBy($this->getUser());
            $user->setProfilePicture($image);
            $user=$this->normalize($user);
            $userManager->updateUser($user);
        }
            
            
        
        
        return View::create($user, Response::HTTP_CREATED);
    
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
                'formateur',
                'fname',
                'lname',
                'jobs'=>['name'],
                'adherent',
                'profilePicture'=>['imgPath'],
                'formations'=>['name','description','hour','time','image'=>['id','file','path','imgPath']],
                'events'=>['name','description','hour','time'],
                'photos'=>['id','createdAt','imgPath','alternative','title','description','type'=>['name']]
            ]]);
        return $object;
    }

    private function serialize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null, ['attributes' => [
                'id',
                'email',
                'username',
                'roles',
                'formateur',
                'fname',
                'lname',
                
            ]]);
        return $object;
    }
}
