<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\SizeRepository")
 */
class Size
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
     * @ORM\OneToMany(targetEntity="App\Entity\Reference", mappedBy="size", orphanRemoval=true)
     */
    private $t_references;

    public function __construct()
    {
        $this->t_references = new ArrayCollection();
    }

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

    /**
     * @return Collection|Reference[]
     */
    public function getTReferences(): Collection
    {
        return $this->t_references;
    }

    public function addTReference(Reference $tReference): self
    {
        if (!$this->t_references->contains($tReference)) {
            $this->t_references[] = $tReference;
            $tReference->setSize($this);
        }

        return $this;
    }

    public function removeTReference(Reference $tReference): self
    {
        if ($this->t_references->contains($tReference)) {
            $this->t_references->removeElement($tReference);
            // set the owning side to null (unless already changed)
            if ($tReference->getSize() === $this) {
                $tReference->setSize(null);
            }
        }

        return $this;
    }
}
