<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListItemRepository")
 */
class ListItem
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
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskList", inversedBy="listItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tasklist;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getTasklist(): ?TaskList
    {
        return $this->tasklist;
    }

    public function setTasklist(?TaskList $tasklist): self
    {
        $this->tasklist = $tasklist;

        return $this;
    }

    public function __toString(){

        return $this->getLabel();
    }
}
