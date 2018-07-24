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
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeGoal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Time", mappedBy="project", orphanRemoval=true)
     */
    private $time;

    public function __construct()
    {
        $this->time = new ArrayCollection();
    }

    public function getId()
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTimeGoal(): ?int
    {
        return $this->timeGoal;
    }

    public function setTimeGoal(?int $timeGoal): self
    {
        $this->timeGoal = $timeGoal;

        return $this;
    }

    /**
     * @return Collection|Time[]
     */
    public function getTime(): Collection
    {
        return $this->time;
    }

    public function addTime(Time $time): self
    {
        if (!$this->time->contains($time)) {
            $this->time[] = $time;
            $time->setProject($this);
        }

        return $this;
    }

    public function removeTime(Time $time): self
    {
        if ($this->time->contains($time)) {
            $this->time->removeElement($time);
            // set the owning side to null (unless already changed)
            if ($time->getProject() === $this) {
                $time->setProject(null);
            }
        }

        return $this;
    }
}
