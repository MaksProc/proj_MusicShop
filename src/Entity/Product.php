<?php

namespace App\Entity;

use App\Enum\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: ProductType::class)]
    private ?ProductType $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $base_price = null;

    #[ORM\Column]
    private ?float $base_rent_per_day = null;

    #[ORM\Column]
    private ?float $base_rent_per_week = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column]
    private ?bool $availability = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    public function __construct()
    {
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?ProductType
    {
        return $this->type;
    }

    public function setType(ProductType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBasePrice(): ?string
    {
        return $this->base_price;
    }

    public function setBasePrice(string $base_price): static
    {
        $this->base_price = $base_price;

        return $this;
    }

    public function getBaseRentPerDay(): ?float
    {
        return $this->base_rent_per_day;
    }

    public function setBaseRentPerDay(float $base_rent_per_day): static
    {
        $this->base_rent_per_day = $base_rent_per_day;

        return $this;
    }

    public function getBaseRentPerWeek(): ?float
    {
        return $this->base_rent_per_week;
    }

    public function setBaseRentPerWeek(float $base_rent_per_week): static
    {
        $this->base_rent_per_week = $base_rent_per_week;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(?string $image_path): static
    {
        $this->image_path = $image_path;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }
}