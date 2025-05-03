<?php

namespace App\Entity;

use App\Enum\RentalStatus;
use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userID = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productID = null;

    #[ORM\Column]
    private ?\DateTime $startTimestamp = null;

    #[ORM\Column]
    private ?\DateTime $endTimestamp = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(nullable: true)]
    private ?float $buyoutCost = null;

    #[ORM\Column(enumType: RentalStatus::class)]
    private ?RentalStatus $status = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): static
    {
        $this->userID = $userID;

        return $this;
    }

    public function getProductID(): ?Product
    {
        return $this->productID;
    }

    public function setProductID(?Product $productID): static
    {
        $this->productID = $productID;

        return $this;
    }

    public function getStartTimestamp(): ?\DateTime
    {
        return $this->startTimestamp;
    }

    public function setStartTimestamp(\DateTime $startTimestamp): static
    {
        $this->startTimestamp = $startTimestamp;

        return $this;
    }

    public function getEndTimestamp(): ?\DateTime
    {
        return $this->endTimestamp;
    }

    public function setEndTimestamp(\DateTime $endTimestamp): static
    {
        $this->endTimestamp = $endTimestamp;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBuyoutCost(): ?float
    {
        return $this->buyoutCost;
    }

    public function setBuyoutCost(?float $buyoutCost): static
    {
        $this->buyoutCost = $buyoutCost;

        return $this;
    }

    public function getStatus(): ?RentalStatus
    {
        return $this->status;
    }

    public function setStatus(RentalStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

}
