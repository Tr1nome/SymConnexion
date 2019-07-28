<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Contact;

class ContactSentEvent extends Event
{
    protected $contact;
    
    

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
        
        
    }

    public function getContact():Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

}