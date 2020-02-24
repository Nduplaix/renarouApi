<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CommandeLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reference", inversedBy="commandeLines")
     * @Groups({"getUser"})
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $refLabel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $refSize;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $refDiscount;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $priceWithDiscount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="commandeLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $refPrice;

    /**
     * @ORM\PrePersist()
     */
    public function init() {
        $this->setRefLabel($this->getReference()->getProduct()->getLabel())
            ->setRefPrice($this->getReference()->getProduct()->getPrice())
            ->setRefDiscount($this->getReference()->getProduct()->getDiscount() ?? 0)
            ->setRefSize($this->getReference()->getSize()->getLabel())
            ->setPrice($this->getRefPrice() * $this->getQuantity());

        if ($this->getRefDiscount() || $this->getRefDiscount() > 0) {
            $this->setPriceWithDiscount($this->getPrice() - $this->getPrice() * $this->getRefDiscount() / 100);
        } else {
            $this->setPriceWithDiscount($this->getPrice());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?Reference
    {
        return $this->reference;
    }

    public function setReference(?Reference $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRefLabel(): ?string
    {
        return $this->refLabel;
    }

    public function setRefLabel(string $refLabel): self
    {
        $this->refLabel = $refLabel;

        return $this;
    }

    public function getRefSize(): ?string
    {
        return $this->refSize;
    }

    public function setRefSize(string $refSize): self
    {
        $this->refSize = $refSize;

        return $this;
    }

    public function getRefDiscount(): ?string
    {
        return $this->refDiscount;
    }

    public function setRefDiscount(string $refDiscount): self
    {
        $this->refDiscount = $refDiscount;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceWithDiscount(): ?float
    {
        return $this->priceWithDiscount;
    }

    public function setPriceWithDiscount(float $priceWithDiscount): self
    {
        $this->priceWithDiscount = $priceWithDiscount;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getRefPrice(): ?float
    {
        return $this->refPrice;
    }

    public function setRefPrice(float $refPrice): self
    {
        $this->refPrice = $refPrice;

        return $this;
    }
}
