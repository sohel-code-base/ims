<?php

namespace App\Entity;

use App\Repository\ProductSaleDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductSaleDetailsRepository::class)
 */
class ProductSaleDetails
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProductSale::class, inversedBy="productSaleDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sale;

    /**
     * @ORM\ManyToOne(targetEntity=ProductPurchase::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $perPcsPrice;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSale(): ?ProductSale
    {
        return $this->sale;
    }

    public function setSale(?ProductSale $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function getProduct(): ?ProductPurchase
    {
        return $this->product;
    }

    public function setProduct(?ProductPurchase $product): self
    {
        $this->product = $product;

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

    public function getPerPcsPrice(): ?float
    {
        return $this->perPcsPrice;
    }

    public function setPerPcsPrice(float $perPcsPrice): self
    {
        $this->perPcsPrice = $perPcsPrice;

        return $this;
    }
}
