<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"getUser"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $streetType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getUser"})
     */
    private $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStreetType(): ?string
    {
        return $this->streetType;
    }

    public function setStreetType(string $streetType): self
    {
        $this->streetType = $streetType;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
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

    public function __toString()
    {
        return $this->number. ' ' .$this->streetType. ' ' .$this->street. ', ' .$this->postalCode. ' ' . $this->city;
    }
}
