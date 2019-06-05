<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Image;

class ImageCreatedEvent extends Event
{
    protected $image;
    

    public function __construct(Image $image)
    {
        $this->image = $image;
        
    }

    public function getImage():Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

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