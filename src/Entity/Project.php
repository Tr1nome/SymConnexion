<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="project")
     */
    private $medias;

    /**
     * @ORM\Column(type="string", length=1500)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="projects")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="integer")
     */
    private $needed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->team = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Image $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setProject($this);
        }

        return $this;
    }

    public function removeMedia(Image $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            // set the owning side to null (unless already changed)
            if ($media->getProject() === $this) {
                $media->setProject(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(User $team): self
    {
        if (!$this->team->contains($team)) {
            $this->team[] = $team;
        }

        return $this;
    }

    public function removeTeam(User $team): self
    {
        if ($this->team->contains($team)) {
            $this->team->removeElement($team);
        }

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getNeeded(): ?int
    {
        return $this->needed;
    }

    public function setNeeded(int $needed): self
    {
        $this->needed = $needed;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

}
