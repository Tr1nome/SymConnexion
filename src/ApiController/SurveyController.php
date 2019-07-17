<?php

namespace App\ApiController;

use App\Entity\User;
use App\Entity\Survey;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\UserRepository;

/**
 * @Route("/survey", host="api.fenrir-studio.fr")
 */
class SurveyController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="survey_api",
     * )
     * @Rest\View()
     */
    public function index(SurveyRepository $surveyRepository): View
    {
        $surveys = $surveyRepository->findAll();
        $survey = $this->normalize($surveys);
        return View::create($survey,Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="survey_create_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request, SurveyRepository $surveyRepository): View
    {
        $survey = new Survey();
        $survey->setNote($request->get('note'));
        $survey->setCommentary($request->get('commentary'));
        $survey->setUser($this->getUser());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($survey);
        $entityManager->flush();
        $survey = $this->normalize($survey);
        return View::create($survey,Response::HTTP_OK);
    }

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */
        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'note',
                'commentary',
                'user' => ['id','username','adherent','profilePicture'=>['id','path','imgPath']],
            ]]);
        return $object;
    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="survey_delete_api",
     * )
     * @Rest\View()
     */
    public function delete(Survey $survey): View
    {
        if ($survey) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($survey);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
