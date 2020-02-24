<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\ReferenceRepository")
 */
class Reference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"products", "getUser"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"products", "getUser"})
     */
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Size", inversedBy="t_references")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"products", "getUser"})
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="tReferences")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getUser"})
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BasketLine", mappedBy="reference", orphanRemoval=true)
     */
    private $basketLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandeLine", mappedBy="reference")
     */
    private $commandeLines;

    public function __construct()
    {
        $this->basketLines = new ArrayCollection();
        $this->commandeLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function __toString()
    {
        return $this->product->getLabel() . ' - ' . $this->size->getLabel();
    }

    /**
     * @return Collection|BasketLine[]
     */
    public function getBasketLines(): Collection
    {
        return $this->basketLines;
    }

    public function addBasketLine(BasketLine $basketLine): self
    {
        if (!$this->basketLines->contains($basketLine)) {
            $this->basketLines[] = $basketLine;
            $basketLine->setReference($this);
        }

        return $this;
    }

    public function removeBasketLine(BasketLine $basketLine): self
    {
        if ($this->basketLines->contains($basketLine)) {
            $this->basketLines->removeElement($basketLine);
            // set the owning side to null (unless already changed)
            if ($basketLine->getReference() === $this) {
                $basketLine->setReference(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommandeLine[]
     */
    public function getCommandeLines(): Collection
    {
        return $this->commandeLines;
    }

    public function addCommandeLine(CommandeLine $commandeLine): self
    {
        if (!$this->commandeLines->contains($commandeLine)) {
            $this->commandeLines[] = $commandeLine;
            $commandeLine->setReference($this);
        }

        return $this;
    }

    public function removeCommandeLine(CommandeLine $commandeLine): self
    {
        if ($this->commandeLines->contains($commandeLine)) {
            $this->commandeLines->removeElement($commandeLine);
            // set the owning side to null (unless already changed)
            if ($commandeLine->getReference() === $this) {
                $commandeLine->setReference(null);
            }
        }

        return $this;
    }
}
