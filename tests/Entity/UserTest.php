<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    // TEST UNITAIRE
    public function testAddUcFirstForUserFirstName()
    {
        $user = new User();
        $user->setUsername('tr1nome');

        // assert that your calculator added the numbers correctly!
        $this->assertEquals('tr1nome', $user->getUsername());
    }

    public function testAbsentUser()
    {
        $user = new User();
        $user->setAbsent('present');

        $this->assertEquals('absent', $user->getAbsent());
    }
}