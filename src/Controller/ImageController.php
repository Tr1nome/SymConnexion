<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Event\ImageCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    protected $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * @Route("/", name="image_index", methods={"GET"})
     */
    public function index(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.imgPath IS NOT NULL')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/commented", name="image_activated", methods={"GET"})
     */
    public function imageActivated(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.alternative IS NOT NULL')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/pending", name="image_pending", methods={"GET"})
     */
    public function imagePending(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.allowed = false')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/allowedonly", name="image_allowed", methods={"GET"})
     */
    public function imageAllowed(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.allowed = true')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/orderAsc", name="image_order_high", methods={"GET"})
     */
    public function imageOrderHigh(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.allowed = true')
            ->orderBy('p.id', 'ASC')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/orderDesc", name="image_order_low", methods={"GET"})
     */
    public function imageOrderLow(Request $request,ImageRepository $imageRepository, PaginatorInterface $paginator): Response
    {
        $allImagesQuery = $imageRepository->createQueryBuilder('p')
            ->where('p.allowed = true')
            ->orderBy('p.id', 'DESC')
            ->getQuery();
        $images = $paginator->paginate(
            $allImagesQuery,
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('image/index.html.twig', [
            'images' => $images,
        ]);
    }

    /**
     * @Route("/new", name="image_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('file')->getData();
            $allowed = $form->get('allowed')->getData();
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();

            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $image->setPath($this->getParameter('images_directory') . '/' . $fileName);
                $image->setImgPath($this->getParameter('images_path') . '/' . $fileName);
            }
            if($allowed) {
                $image->setAllowed($allowed, true);
            }
            else {
                $image->setAllowed($allowed, false);
            }

            $image->setTitle($title);
            $image->setDescription($description);

            $entityManager = $this->getDoctrine()->getManager();
            //
            $imageEvent = new ImageCreatedEvent($image);
            $this->dispatcher->dispatch('image.created', $imageEvent);
            $entityManager->persist($image);
            //$entityManager->persist($allowed);
            $entityManager->flush();

            return $this->redirectToRoute('image_index');
        }




        return $this->render('image/new.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_show", methods={"GET"})
     */
    public function show(Image $image): Response
    {
        return $this->render('image/show.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('file')->getData();
            $allowed = $form->get('allowed')->getData();

            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $this->removeFile($image->getPath());
                $image->setPath($this->getParameter('images_directory') . '/' . $fileName);
                $image->setImgPath($this->getParameter('images_path') . '/' . $fileName);
                
                $this->getDoctrine()->getManager()->flush();
            }
            if($allowed) {
                $image->setAllowed($allowed, true);
                $this->getDoctrine()->getManager()->flush();
            }
            else {
                $image->setAllowed($allowed, false);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('image_index', [
                'id' => $image->getId(),
            ]);
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/comfirm", name="image_allow", methods={"GET","POST"})
     */
    public function toggleAllowed(Image $image): Response
    {
        $allowed = $image->getAllowed();
        if($allowed){
            $image->setAllowed(false);
            $this->getDoctrine()->getManager()->flush();
        }
        else{
            $image->setAllowed(true);
            $this->getDoctrine()->getManager()->flush();
        }
        
        return $this->redirectToRoute('image_index');
    }
    /**
     * @Route("/{id}", name="image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $this->removeFile($image->getPath());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_index');
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    private function removeFile($path){

        if(file_exists($path)){

            unlink($path);

        }
    }



}
