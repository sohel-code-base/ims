<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=SubCategory::class, inversedBy="products")
     */
    private $subCategory;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=ProductPurchase::class, mappedBy="product")
     */
    private $productPurchases;

    /**
     * @ORM\OneToMany(targetEntity=ProductPurchaseArchive::class, mappedBy="product")
     */
    private $productPurchaseArchives;

    public function __construct()
    {
        $this->productPurchases = new ArrayCollection();
        $this->productPurchaseArchives = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * @param mixed $subCategory
     */
    public function setSubCategory($subCategory): void
    {
        $this->subCategory = $subCategory;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Collection|ProductPurchase[]
     */
    public function getProductPurchases(): Collection
    {
        return $this->productPurchases;
    }

    public function addProductPurchase(ProductPurchase $productPurchase): self
    {
        if (!$this->productPurchases->contains($productPurchase)) {
            $this->productPurchases[] = $productPurchase;
            $productPurchase->setProduct($this);
        }

        return $this;
    }

    public function removeProductPurchase(ProductPurchase $productPurchase): self
    {
        if ($this->productPurchases->removeElement($productPurchase)) {
            // set the owning side to null (unless already changed)
            if ($productPurchase->getProduct() === $this) {
                $productPurchase->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductPurchaseArchive[]
     */
    public function getProductPurchaseArchives(): Collection
    {
        return $this->productPurchaseArchives;
    }

    public function addProductPurchaseArchive(ProductPurchaseArchive $productPurchaseArchive): self
    {
        if (!$this->productPurchaseArchives->contains($productPurchaseArchive)) {
            $this->productPurchaseArchives[] = $productPurchaseArchive;
            $productPurchaseArchive->setProduct($this);
        }

        return $this;
    }

    public function removeProductPurchaseArchive(ProductPurchaseArchive $productPurchaseArchive): self
    {
        if ($this->productPurchaseArchives->removeElement($productPurchaseArchive)) {
            // set the owning side to null (unless already changed)
            if ($productPurchaseArchive->getProduct() === $this) {
                $productPurchaseArchive->setProduct(null);
            }
        }

        return $this;
    }


}
