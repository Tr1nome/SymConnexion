<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Formation;
use App\Entity\User;

class FormationAbsentedEvent extends Event
{
    protected $formation;
    protected $user;
    
    

    public function __construct(Formation $formation, User $user)
    {
        $this->formation = $formation;
        $this->user = $user;
        
        
    }

    public function getFormation():Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getUser():User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

}