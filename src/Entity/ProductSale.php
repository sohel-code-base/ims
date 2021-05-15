<?php

namespace App\Entity;

use App\Repository\ProductSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductSaleRepository::class)
 */
class ProductSale
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="productSales")
     */
    private $customer;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dueAmount;

    /**
     * @ORM\Column(type="date")
     */
    private $saleDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=ProductSaleDetails::class, mappedBy="sale", cascade={"persist"})
     */
    private $productSaleDetails;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="productSales")
     */
    private $employee;

    public function __construct()
    {
        $this->productSaleDetails = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection|ProductSaleDetails[]
     */
    public function getProductSaleDetails(): Collection
    {
        return $this->productSaleDetails;
    }

    public function addProductSaleDetail(ProductSaleDetails $productSaleDetail): self
    {
        if (!$this->productSaleDetails->contains($productSaleDetail)) {
            $this->productSaleDetails[] = $productSaleDetail;
            $productSaleDetail->setSale($this);
        }

        return $this;
    }

    public function removeProductSaleDetail(ProductSaleDetails $productSaleDetail): self
    {
        if ($this->productSaleDetails->removeElement($productSaleDetail)) {
            // set the owning side to null (unless already changed)
            if ($productSaleDetail->getSale() === $this) {
                $productSaleDetail->setSale(null);
            }
        }

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

    public function getDueAmount(): ?float
    {
        return $this->dueAmount;
    }

    public function setDueAmount(float $dueAmount): self
    {
        $this->dueAmount = $dueAmount;

        return $this;
    }

    public function getSaleDate(): ?\DateTimeInterface
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTimeInterface $saleDate): self
    {
        $this->saleDate = $saleDate;

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

        return $this;
    }
}
