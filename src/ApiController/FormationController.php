<?php
namespace App\ApiController;
use App\Entity\Formation;
use App\Entity\Image;
use App\Entity\User;
use App\Event\InscriptionEvent;
use App\Form\ImageType;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\ImageRepository;
use App\Repository\MediaTypeRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Event\FormationRegisteredEvent;
use App\Event\FormationAbsentedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\ApiController\AuthController;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
/**
 * @Route("/formation", host="api.fenrir-studio.fr")
 */
class FormationController extends AbstractFOSRestController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * @Rest\Get(
     * path = "/",
     * name="formation_api",
     * )
     * @Rest\View()
     */
    public function index(FormationRepository $formationRepository): View
    {
        $formations = $formationRepository->findAll();
        $formation = $this->normalize($formations);
        return View::create($formation, Response::HTTP_OK);
    }
    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="formationshow_api",
     * )
     * @Rest\View()
     */
    public function show(Formation $formation): View
    {
        $formation = $this->normalize($formation);
        return View::create($formation, Response::HTTP_OK);
    }
    /**
     * @Rest\Post(
     * path = "/new",
     * name="formationnew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request, FormationRepository $formation, ImageRepository $imageRepo, MediaTypeRepository $mediaRepo): View
    {
        $formation = new Formation();
        $type = $mediaRepo->findOneBy( array("name" => "Photo privée"));
        $em = $this->getDoctrine()->getManager();
            $formation->setName($request->get('name'));
            $formation->setDescription($request->get('description'));
            $formation->setAllowed(false);
            
        if (!empty($request->get('image'))) {
            $image = $imageRepo->find($request->get('image'));
            $image->setAllowed(true);
            $image->setType($type);
            $formation->setImage($image);
        } else {
            $formation->setImage(null);
        }
            $em->persist($formation);
            $em->flush();
            $formationCreated = $this->normalize($formation);
        
        return View::create($formationCreated, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="formationedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Formation $formation): View
    {
        if($formation) {
            $formation->setName($request->get('name'));
            $formation->setDescription($request->get('description'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();
        }
        return View::create($formation, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="formationpatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Formation $formation, User $user): View
    {
        if($formation) {  
            $user = $this->getUser();
            $formation->addUser($user);
            $form = $this->createForm(FormationType::class, $formation);
            $form->submit($request->request->all(), true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();  
        }
        return View::create($formation, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Patch(
     * path = "/{id}/register",
     * name="formationreg_api",
     * )
     * @Rest\View()
     */
    public function register(Formation $formation, Request $request): View
    {
        $user = $this->getUser();
        $user->setAbsent('Présent');
        $formation->addUser($user);
        $formations = $this->normalize($formation);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($formation);
        $entityManager->persist($user);
        $entityManager->flush();
        $formationEvent = new FormationRegisteredEvent($formation);
        $this->dispatcher->dispatch('formation.registered', $formationEvent);
        return View::create($formations, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Patch(
     * path = "/{id}/leave",
     * name="formationunreg_api",
     * )
     * @Rest\View()
     */
    public function leave(Formation $formation, Request $request): View
    {
        $formation->removeUser($this->getUser());
        $formations = $this->normalize($formation);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($formation);
        $entityManager->flush();
        return View::create($formations, Response::HTTP_CREATED);
    }
    /**
     * @Rest\Post(
     * path = "/{id}/absent",
     * name="formation_abs_api",
     * )
     * @Rest\View()
     */
    public function absent(Formation $formation, Request $request): View
    {
        $user = $this->getUser();
        $user->setAbsent('Absent');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $absentEvent = new FormationAbsentedEvent($formation, $user);
        $this->dispatcher->dispatch('formation.absented', $absentEvent);
        return View::create('réussi', Response::HTTP_CREATED);
        
    }
    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="formationdelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Formation $formation): View
    {
        if ($formation) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }
    private function normalize($object)
    {
        /* Serializer, normalizer exemple */
        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'name',
                'description',
                'allowed',
                'user' => ['id','username','adherent','profilePicture'=>['id','file','path','imgPath']],
                'image'=> ['id','file','path','imgPath','likedBy'=>['username']],
                'hour',
                'time',
            ]]);
        return $object;
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}