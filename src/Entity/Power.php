<?php

namespace App\Entity;

use App\Repository\PowerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PowerRepository::class)
 */
class Power
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $watt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    private $products;
    /**
     * @ORM\OneToMany(targetEntity=ProductPurchase::class, mappedBy="power")
     */
    private $productPurchases;

    /**
     * @ORM\OneToMany(targetEntity=ProductPurchaseArchive::class, mappedBy="power")
     */
    private $productPurchaseArchives;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productPurchases = new ArrayCollection();
        $this->productSales = new ArrayCollection();
        $this->productPurchaseArchives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWatt(): ?int
    {
        return $this->watt;
    }

    public function setWatt(?int $watt): self
    {
        $this->watt = $watt;

        return $this;
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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setPower($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPower() === $this) {
                $product->setPower(null);
            }
        }

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
            $productPurchase->setPower($this);
        }

        return $this;
    }

    public function removeProductPurchase(ProductPurchase $productPurchase): self
    {
        if ($this->productPurchases->removeElement($productPurchase)) {
            // set the owning side to null (unless already changed)
            if ($productPurchase->getPower() === $this) {
                $productPurchase->setPower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductSale[]
     */
    public function getProductSales(): Collection
    {
        return $this->productSales;
    }

    public function addProductSale(ProductSale $productSale): self
    {
        if (!$this->productSales->contains($productSale)) {
            $this->productSales[] = $productSale;
            $productSale->setPower($this);
        }

        return $this;
    }

    public function removeProductSale(ProductSale $productSale): self
    {
        if ($this->productSales->removeElement($productSale)) {
            // set the owning side to null (unless already changed)
            if ($productSale->getPower() === $this) {
                $productSale->setPower(null);
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
            $productPurchaseArchive->setPower($this);
        }

        return $this;
    }

    public function removeProductPurchaseArchive(ProductPurchaseArchive $productPurchaseArchive): self
    {
        if ($this->productPurchaseArchives->removeElement($productPurchaseArchive)) {
            // set the owning side to null (unless already changed)
            if ($productPurchaseArchive->getPower() === $this) {
                $productPurchaseArchive->setPower(null);
            }
        }

        return $this;
    }
}
