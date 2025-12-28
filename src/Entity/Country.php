<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['country:read'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    #[Groups(['country:read'])]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['country:read'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['country:read'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country:read'])]
    private ?string $subRegion = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country:read'])]
    private ?string $demonym = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['country:read'])]
    private int $population = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['country:read'])]
    private bool $independant = true;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country:read'])]
    private ?string $flag = null;

    #[ORM\Embedded(class: CurrencyEmbeddable::class)]
    private CurrencyEmbeddable $currency;

    public function __construct()
    {
        $this->currency = new CurrencyEmbeddable();
    }

    // ====================== Getters ======================

    #[Groups(['country:read'])]
    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['country:read'])]
    public function getUuid(): string
    {
        return $this->uuid;
    }

    #[Groups(['country:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    #[Groups(['country:read'])]
    public function getRegion(): string
    {
        return $this->region;
    }

    #[Groups(['country:read'])]
    public function getSubRegion(): ?string
    {
        return $this->subRegion;
    }

    #[Groups(['country:read'])]
    public function getDemonym(): ?string
    {
        return $this->demonym;
    }

    #[Groups(['country:read'])]
    public function getPopulation(): int
    {
        return $this->population;
    }

    #[Groups(['country:read'])]
    public function isIndependant(): bool
    {
        return $this->independant;
    }

    #[Groups(['country:read'])]
    public function getFlag(): ?string
    {
        return $this->flag;
    }

    #[Groups(['country:read'])]
    public function getCurrency(): CurrencyEmbeddable
    {
        return $this->currency;
    }

    // ====================== Setters ======================

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function setSubRegion(?string $subRegion): void
    {
        $this->subRegion = $subRegion;
    }

    public function setDemonym(?string $demonym): void
    {
        $this->demonym = $demonym;
    }

    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    public function setIndependant(bool $independant): void
    {
        $this->independant = $independant;
    }

    public function setFlag(?string $flag): void
    {
        $this->flag = $flag;
    }

    public function setCurrency(CurrencyEmbeddable $currency): void
    {
        $this->currency = $currency;
    }
}