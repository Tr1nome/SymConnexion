<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
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

            $entityManager = $this->getDoctrine()->getManager();
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
