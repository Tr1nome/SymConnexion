<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Image;
use App\Form\FormationType;
use App\Form\ImageType;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Knp\Component\Pager\PaginatorInterface;
use Exporter\Handler;
use Exporter\Source\PDOStatementSourceIterator;
use Exporter\Writer\CsvWriter;

/**
 * @Route("/formation", host="connexion.fr")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("/", name="formation_index", methods={"GET"})
     */
    public function index(Request $request, FormationRepository $formationRepository, PaginatorInterface $paginator): Response
    {
        $allFormationsQuery = $formationRepository->createQueryBuilder('p')
            ->where('p.description IS NOT NULL')
            ->getQuery();
        $formations = $paginator->paginate(
            $allFormationsQuery,
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
    /**
     * @Route("/new", name="formation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('image')->get('file')->getData();
            if($file) {
                $image= new Image();
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
                $entityManager->persist($image);
                $formation->setImage($image);

            }
            else{
                $formation->setImage(null);
            }

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('formation_index');
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formation_show", methods={"GET"})
     */
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="formation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
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
            return $this->redirectToRoute('formation_index', [
                'id' => $formation->getId(),
            ]);
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formations_img_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $image = $formation->getImage();
            $this->removeFile($image->getPath());
            $formation->setImage(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->persist($formation);
            $entityManager->flush();
        }
        return $this->redirectToRoute('formation_edit', array('id'=>$formation->getId()));
    }

    /**
     * @Route("/{id}", name="formation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $image = $formation->getImage();
            if($image){
                $this->removeFile($image->getPath());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('formation_index');
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
    /**
     * @Route("export", name="formation_export")
     */
    public function export():Response
    {
    // Prepare the data source
    $dbh = new \PDO('mysql:host="127.0.0.1",port="3306",dbname="connexion"');
    $stm = $dbh->prepare('SELECT id, username, email FROM user');
    $stm->execute();

    $source = new PDOStatementSourceIterator($stm);

    // Prepare the writer
    $writer = new CsvWriter('data.csv');

    // Export the data
    Handler::create($source, $writer)->export();
    }
}