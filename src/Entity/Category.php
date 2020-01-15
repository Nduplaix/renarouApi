<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks
 * UniqueEntity(fields={'slug'},
 * message="Cette categorie existe deja"
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("categories")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("categories")
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("categories")
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SubCategory", mappedBy="category", orphanRemoval=true)
     * @Groups("categories")
     */
    private $subCategories;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug
     *@ORM\PrePersist
     *@ORM\PreUpdate
     *
     * @return void
     */
    public function initSlug(){
        if (empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug= $slugify->slugify($this->label);
        }
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|SubCategory[]
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): self
    {
        if ($this->subCategories->contains($subCategory)) {
            $this->subCategories->removeElement($subCategory);
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

        return $this;
    }
}
