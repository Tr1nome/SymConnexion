<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Formation;
use App\Entity\User;

class FormationRegisteredEvent extends Event
{
    protected $formation;
    
    

    public function __construct(Formation $formation)
    {
        $this->formation = $formation;
        
        
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

}