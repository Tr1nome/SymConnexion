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
 * @Route("/commentary", host="api.fenrir-studio.fr")
 */
class CommentaryController extends AbstractFOSRestController
{
/**
     * @Rest\Delete(
     *   path="/{id}",
     *   name="commentdelete_api",
     * )
     * @Rest\View()
     */
    public function delete(Comment $comment): View
    {
        if ($comment) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
