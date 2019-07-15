<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Formation", mappedBy="user")
     */
    private $formations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="user")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", mappedBy="likedBy")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="uploadedBy")
     */
    private $photos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $formateur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", inversedBy="user", cascade={"persist", "remove"})
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $lname;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $fname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adherent;

    public function __construct()
    {
        parent::__construct();
        $this->formations = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->addUser($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->contains($formation)) {
            $this->formations->removeElement($formation);
            $formation->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->addLikedBy($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->removeLikedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Image $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setUploadedBy($this);
        }

        return $this;
    }

    public function removePhoto(Image $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getUploadedBy() === $this) {
                $photo->setUploadedBy(null);
            }
        }

        return $this;
    }

    public function getFormateur(): ?bool
    {
        return $this->formateur;
    }

    public function setFormateur(bool $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function getProfilePicture(): ?Image
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?Image $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getLname(): ?string
    {
        return $this->lname;
    }

    public function setLname(?string $lname): self
    {
        $this->lname = $lname;

        return $this;
    }

    public function getFname(): ?string
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function getAdherent(): ?bool
    {
        return $this->adherent;
    }

    public function setAdherent(bool $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }
}