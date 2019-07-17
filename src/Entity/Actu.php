<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActuRepository")
 */
class Actu
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="actus")
     */
    private $lovedBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="actu")
     */
    private $commentaries;

    public function __construct()
    {
        $this->lovedBy = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLovedBy(): Collection
    {
        return $this->lovedBy;
    }

    public function addLovedBy(User $lovedBy): self
    {
        if (!$this->lovedBy->contains($lovedBy)) {
            $this->lovedBy[] = $lovedBy;
        }

        return $this;
    }

    public function removeLovedBy(User $lovedBy): self
    {
        if ($this->lovedBy->contains($lovedBy)) {
            $this->lovedBy->removeElement($lovedBy);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Comment $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setActu($this);
        }

        return $this;
    }

    public function removeCommentary(Comment $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getActu() === $this) {
                $commentary->setActu(null);
            }
        }

        return $this;
    }

    public function __tostring() {
        return $this->getTitle();
    }
}
