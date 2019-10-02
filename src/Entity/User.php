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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Survey", mappedBy="user")
     */
    private $commentaries;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Actu", mappedBy="lovedBy")
     */
    private $actus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="team")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Job", inversedBy="users")
     */
    private $jobs;

    public function __construct()
    {
        parent::__construct();
        $this->formations = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->actus = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->jobs = new ArrayCollection();
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


    /**
     * @return Collection|Survey[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Survey $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setUser($this);
        }

        return $this;
    }

    public function removeCommentary(Survey $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getUser() === $this) {
                $commentary->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Actu[]
     */
    public function getActus(): Collection
    {
        return $this->actus;
    }

    public function addActus(Actu $actus): self
    {
        if (!$this->actus->contains($actus)) {
            $this->actus[] = $actus;
            $actus->addLovedBy($this);
        }

        return $this;
    }

    public function removeActus(Actu $actus): self
    {
        if ($this->actus->contains($actus)) {
            $this->actus->removeElement($actus);
            $actus->removeLovedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addTeam($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
        }

        return $this;
    }
}