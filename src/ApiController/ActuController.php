<?php

namespace App\ApiController;

use App\Entity\Actu;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\ActuType;
use App\Repository\ActuRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\ApiController\AuthController;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/actu", host="api.fenrir-studio.fr")
 */
class ActuController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     * path = "/",
     * name="actu_api",
     * )
     * @Rest\View()
     */
    public function index(ActuRepository $actuRepository): View
    {
        $actus = $actuRepository->findAll();
        $actualite = $this->normalize($actus);
        return View::create($actualite, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     * path = "/{id}",
     * name="actushow_api",
     * )
     * @Rest\View()
     */
    public function show(Actu $actu): View
    {
        $actualite = $this->normalize($actu);
        return View::create($actualite, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     * path = "/new",
     * name="actunew_api",
     * )
     * @Rest\View()
     */
    public function create(Request $request): View
    {
        $actu = new Actu();
        $actu->setTitle($request->get('title'));
        $actu->setContent($request->get('content'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($actu);
        $em->flush();
        return View::create($actu, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     * path = "/{id}",
     * name="actuedit_api",
     * )
     * @Rest\View()
     */
    public function edit(Request $request,Actu $actu): View
    {
        if($actu) {
            $actu->setTitle($request->get('title'));
            $actu->setContent($request->get('content'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Patch(
     * path = "/{id}",
     * name="actupatch_api",
     * )
     * @Rest\View()
     */
    public function patch(Request $request, Actu $actu): View
    {
        if($actu) {
            $form = $this->createForm(ActuType::class, $actu);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($actu);
            $em->flush();
        }
        return View::create($actu, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="actudelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Actu $actu): View
    {
        if ($actu) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actu);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Patch(
     * path = "/{id}/like",
     * name="actu_like_api",
     * )
     * @Rest\View()
     */
    public function like(Request $request, Actu $actu): View
    {
        $actu->addLovedBy($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($actu);
        $em->flush();
        $actualite = $this->normalize($actu);
        return View::create($actualite, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch(
     * path = "/{id}/dislike",
     * name="actu_dislike_api",
     * )
     * @Rest\View()
     */
    public function dislike(Request $request, Actu $actu): View
    {
        $actu->removeLovedBy($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($actu);
        $em->flush();
        $actualite = $this->normalize($actu);
        return View::create($actualite, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch(
     * path = "/{id}/comment",
     * name="actu_comment_api",
     * )
     * @Rest\View()
     */
    public function comment(Request $request, Actu $actu): View {

        $comment = new Comment();
        $comment->setContent($request->get('commentary'));
        $comment->setUser($this->getuser());
        $actu->addCommentary($comment);
        $entityManager = $this->getdoctrine()->getManager();
        $entityManager->persist($actu);
        $entityManager->persist($comment);
        $entityManager->flush();
        $actual = $this->normalize($actu);

        return View::create($actual ,Response::HTTP_CREATED);

    }

    private function normalize($object)
    {
        /* Serializer, normalizer exemple */
        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, null,
            ['attributes' => [
                'id',
                'title',
                'content',
                'commentaries'=>['id','content','user'=>['username','profilePicture'=>['imgPath']]],
                'lovedBy' => ['id','username','adherent','fname','lname','profilePicture'=>['id','file','path','imgPath']],
            ]]);
        return $object;
    }

}