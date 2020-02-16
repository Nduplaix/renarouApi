<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BasketLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reference", inversedBy="basketLines")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getUser"})
     */
    private $reference;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $totalPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="basketLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $basket;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initTotalPrice() {
        $discount = $this->getReference()->getProduct()->getDiscount();
        if ($discount || $discount > 0) {
            $referencePrice = $this->getReference()->getProduct()->getPrice();
            $priceAfterDiscount = $referencePrice * $discount / 100;
            $this->setTotalPrice($priceAfterDiscount * $this->quantity);
        } else {
            $this->setTotalPrice($this->getReference()->getProduct()->getPrice() * $this->quantity);
        }
        $this->getBasket()->initPrice();
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }
}
