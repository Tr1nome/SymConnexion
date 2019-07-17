<?php

namespace App\Tests\Comment;

use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    // TEST UNITAIRE
    public function testIfContentIsset()
    {
        $comment = new Comment();
        $comment->setContent('Trop cool ce truc !');
        $this->assertEquals('Trop cool ce truc !', $comment->getContent());
    }

    //Test pour savoir si le nom de l'utilisateur qui a un commentaire appartient bien a cet utilisateur
    public function testIfUserIsAdded()
    {
        $user = new User();
        $user->setUsername('Tr1nome');
        $comment = new Comment();
        $comment->setUser($user);
        $this->assertEquals('Tr1nome', $comment->getUser()->getUsername());
    }
}