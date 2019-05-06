<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskListRepository")
 */
class TaskList
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
     * @ORM\OneToMany(targetEntity="App\Entity\ListItem", cascade={"all"}, orphanRemoval=true, mappedBy="tasklist")
     */
    private $listItems;

    public function __construct()
    {
        $this->listItems = new ArrayCollection();
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
     * @return Collection|ListItem[]
     */
    public function getListItems(): Collection
    {
        return $this->listItems;
    }

    public function addListItem(ListItem $listItem): self
    {
        if (!$this->listItems->contains($listItem)) {
            $this->listItems[] = $listItem;
            $listItem->setTasklist($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): self
    {
        if ($this->listItems->contains($listItem)) {
            $this->listItems->removeElement($listItem);
            // set the owning side to null (unless already changed)
            if ($listItem->getTasklist() === $this) {
                $listItem->setTasklist(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->getName();
    }
}
