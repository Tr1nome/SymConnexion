<?php

namespace App\ApiController;

use App\Entity\Image;
use App\Entity\User;
use App\Entity\MediaType;
use App\Form\ImageType;
use App\Event\ImageCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Repository\ImageRepository;
use App\Repository\MediaTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
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
 * @Route("/image", host="api.fenrir-studio.fr")
 */
class ImageController extends AbstractFOSRestController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * @Rest\Get(
     * path = "/folio",
     * name="image_api",
     * )
     * @Rest\View()
     */
    public function index(ImageRepository $imageRepository, MediaTypeRepository $mediaRepo): View
    {
        $type = $mediaRepo->findBy(array('name'=>'Photo de portfolio'));
        $image = $imageRepository->findBy(array('type'=>$type));

        $images = $this->normalize($image);
        return View::create($images, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}/likes",
     * name="image_likes_num_api",
     * )
     * @Rest\View()
     */
    public function getLikes(Image $image): View
    {
        $likes = $image->getLikedBy();
        $likes = $this->serialize($likes);
        return View::create($likes, Response::HTTP_OK,[],[ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="imageshow_api",
     * )
     * @Rest\View()
     */
    public function show(Image $image): View
    {
        //$image = $this->normalize($image);
        return View::create($image, Response::HTTP_OK,[],[ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="imagenew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request, MediaTypeRepository $mediaRepo): View
    {

        $image = new Image();
        $type = $mediaRepo->findOneBy( array("name" => "Photo de portfolio"));
        $file = $request->files->get('file');
        
        if ($file){
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                
            }
            $image->setPath($this->getParameter('images_directory').'/'.$fileName);
            $image->setImgPath($this->getParameter('images_path').'/'.$fileName);
            $image->setAllowed(false);
            $image->setTitle($request->get('title'));
            $image->setDescription($request->get('description'));
            $image->setUploadedBy($this->getUser());
            $image->setType($type);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
            $imageEvent = new ImageCreatedEvent($image);
            $this->dispatcher->dispatch('image.created', $imageEvent);
            $image = $this->normalize($image);
            
        }

        return View::create($image, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Post(
     * path = "/newProfile",
     * name="imagenewProfile_api",
     * )
     * @Rest\View()
     */
    public function uploadProfilePicture(Request $request, MediaTypeRepository $mediaRepo): View
    {

        $image = new Image();
        $type = $mediaRepo->findOneBy( array("name" => "Photo de profil"));
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
            $image->setTitle($request->get('title'));
            $image->setDescription($request->get('description'));
            $image->setType($type);
            
            $image->setUploadedBy($this->getUser());
            $image->setUser($this->getUser());
            
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
            $image = $this->normalize($image);
            
        }

        return View::create($image, Response::HTTP_CREATED);
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

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="imageedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Image $image): View
    {
        if($image) {
            $image->setPath($request->get('path'));
            $image->setImgPath($request->get('imgPath'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($image, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="imagepatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request,Image $image): View
    {
        if($image) {
            $form = $this->createForm(ImageType::class, $image);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
        }
        return View::create($image, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}/like",
     * name="imagelike_api",
     * )
     * @Rest\View()
     */
    public function like(Request $request, Image $image)
    {
        $image->addLikedBy($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($image);
        $entityManager->flush();
        $image = $this->normalize($image);
        return View::create($image, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch(
     * path = "/{id}/dislike",
     * name="imagedislike_api",
     * )
     * @Rest\View()
     */
    public function dislike(Request $request, Image $image)
    {
        $image->removeLikedBy($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($image);
        $entityManager->flush();
        $image = $this->normalize($image);
        return View::create($image, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="imagedelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Image $image): View
    {
        $entityManager = $this->getDoctrine()->getManager();
            $this->removeFile($image->getPath());
            $entityManager->remove($image);
            $entityManager->flush();
        

        return View::create([], Response::HTTP_NO_CONTENT);
    }

    private function removeFile($path)
    {
        if(file_exists($path))
        {
            unlink($path);
        }
    }

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'file',
                'path',
                'imgPath',
                'title',
                'description',
                'alternative',
                'allowed',
                'type'=>['name'],
                'createdAt',
                'likedBy'=>['username'],
                'uploadedBy'=>['username','profilePicture'=>['imgPath']],
                
            ]]);
        return $object;
    }

    private function deserialize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'file',
                'path',
                'imgPath',
                'title',
                'description',
                'alternative',
                'allowed',
                'type',
                'createdAt',
                'uploadedBy'=>['username']
            ]]);
        return $object;
    }


    private function serialize($object)
    {
        /* Serializer, normalizer exemple */

        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'username'
            ]]);
        return $object;
    }
}