<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getUser"})
     */
    private $price;

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
    private $priceWithDiscount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandeLine", mappedBy="commande", orphanRemoval=true)
     * @Groups({"getUser"})
     */
    private $commandeLines;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"getUser"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"getUser"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Delivery", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getUser"})
     */
    private $delivery;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CommandeStatus", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getUser"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"getUser"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DiscountCode", inversedBy="commandes")
     */
    private $discountCode;

    public function __construct()
    {
        $this->commandeLines = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function init() {
        $today = new \DateTime();

        $this->setCreatedAt($today)
            ->setUpdatedAt($today);

        $price = 0;
        $count = 0;
        $discount = 0;
        $discountCode = 0;

        foreach ($this->getCommandeLines() as $line) {
            $price += $line->getPrice();
            $count += $line->getQuantity();
            $lineProduct = $line->getReference()->getProduct();
            $discount += number_format($lineProduct->getDiscount() * $lineProduct->getPrice() / 100 , 2);
        }


        if (null !== $this->getDiscountCode()) {
            $code = $this->getDiscountCode();
            if ($code->getIsPercent()) {
                $discountWithCode = number_format(($price - $discount) * $code->getAmount() / 100, 2);
                $discountCode = $discountWithCode;
            } else {
                $discountCode = $code->getAmount();
            }
        }

        $this->setPrice($price + $this->getDelivery()->getShippingPrice())
            ->setProductCount($count)
            ->setTotalDiscount($discount)
            ->setPriceWithDiscount($this->getPrice() - $this->getTotalDiscount() - $discountCode);
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateDate() {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
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

    public function getPriceWithDiscount(): ?float
    {
        return $this->priceWithDiscount;
    }

    public function setPriceWithDiscount(float $priceWithDiscount): self
    {
        $this->priceWithDiscount = $priceWithDiscount;

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
            $commandeLine->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeLine(CommandeLine $commandeLine): self
    {
        if ($this->commandeLines->contains($commandeLine)) {
            $this->commandeLines->removeElement($commandeLine);
            // set the owning side to null (unless already changed)
            if ($commandeLine->getCommande() === $this) {
                $commandeLine->setCommande(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getStatus(): ?CommandeStatus
    {
        return $this->status;
    }

    public function setStatus(?CommandeStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getDiscountCode(): ?DiscountCode
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?DiscountCode $discountCode): self
    {
        $this->discountCode = $discountCode;

        return $this;
    }
}
