<?php

namespace App\Entity;

use App\Repository\DietRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DietRepository::class)]
class Diet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Calories = null;

    #[ORM\Column]
    private ?float $Fat = null;

    #[ORM\Column]
    private ?float $Carbohydrates = null;

    #[ORM\Column]
    private ?float $Proteins = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalories(): ?float
    {
        return $this->Calories;
    }

    public function setCalories(float $Calories): static
    {
        $this->Calories = $Calories;

        return $this;
    }

    public function getFat(): ?float
    {
        return $this->Fat;
    }

    public function setFat(float $Fat): static
    {
        $this->Fat = $Fat;

        return $this;
    }

    public function getCarbohydrates(): ?float
    {
        return $this->Carbohydrates;
    }

    public function setCarbohydrates(float $Carbohydrates): static
    {
        $this->Carbohydrates = $Carbohydrates;

        return $this;
    }

    public function getProteins(): ?float
    {
        return $this->Proteins;
    }

    public function setProteins(float $Proteins): static
    {
        $this->Proteins = $Proteins;

        return $this;
    }
}
