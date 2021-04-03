<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
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
    private $cusName;

    /**
     * @ORM\Column(type="string")
     */
    private $cusPhone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cusAddress;

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

    /**
     * @ORM\OneToMany(targetEntity=ProductSale::class, mappedBy="customer")
     */
    private $productSales;

    public function __construct()
    {
        $this->productSales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCusName(): ?string
    {
        return $this->cusName;
    }

    public function setCusName(string $cusName): self
    {
        $this->cusName = $cusName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCusPhone()
    {
        return $this->cusPhone;
    }

    /**
     * @param mixed $cusPhone
     */
    public function setCusPhone($cusPhone): void
    {
        $this->cusPhone = $cusPhone;
    }

    public function getCusAddress(): ?string
    {
        return $this->cusAddress;
    }

    public function setCusAddress(?string $cusAddress): self
    {
        $this->cusAddress = $cusAddress;

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
            $productSale->setCustomer($this);
        }

        return $this;
    }

    public function removeProductSale(ProductSale $productSale): self
    {
        if ($this->productSales->removeElement($productSale)) {
            // set the owning side to null (unless already changed)
            if ($productSale->getCustomer() === $this) {
                $productSale->setCustomer(null);
            }
        }

        return $this;
    }
}
