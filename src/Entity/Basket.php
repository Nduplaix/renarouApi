<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Basket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="basket", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BasketLine", mappedBy="basket", orphanRemoval=true)
     * @Groups({"getUser"})
     */
    private $basketLines;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $productCount;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $totalDiscount;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $totalWithDiscount;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initPrice()
    {
        $price = 0;
        $count = 0;
        $discount = 0;

        foreach ($this->getBasketLines() as $line) {
            $price += $line->getTotalPrice();
            $count += $line->getQuantity();
            $lineProduct = $line->getReference()->getProduct();
            $discount += $lineProduct->getDiscount() * $lineProduct->getPrice() / 100;
        }

        $this->setPrice($price);
        $this->setProductCount($count);
        $this->setTotalDiscount($discount);
        $this->setTotalWithDiscount($this->getPrice() - $this->totalDiscount);
    }

    public function __construct()
    {
        $this->basketLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
            $basketLine->setBasket($this);
        }

        return $this;
    }

    public function removeBasketLine(BasketLine $basketLine): self
    {
        if ($this->basketLines->contains($basketLine)) {
            $this->basketLines->removeElement($basketLine);
            // set the owning side to null (unless already changed)
            if ($basketLine->getBasket() === $this) {
                $basketLine->setBasket(null);
            }
        }

        return $this;
    }

    public function getProductCount(): ?int
    {
        return $this->productCount;
    }

    public function setProductCount(int $productCount): self
    {
        $this->productCount = $productCount;

        return $this;
    }

    public function getTotalDiscount(): ?float
    {
        return $this->totalDiscount;
    }

    public function setTotalDiscount(float $totalDiscount): self
    {
        $this->totalDiscount = $totalDiscount;

        return $this;
    }

    public function getTotalWithDiscount(): ?float
    {
        return $this->totalWithDiscount;
    }

    public function setTotalWithDiscount(float $totalWithDiscount): self
    {
        $this->totalWithDiscount = $totalWithDiscount;

        return $this;
    }

    public function clearBasket()
    {
        $this->basketLines = new ArrayCollection();
    }
}
