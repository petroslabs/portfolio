<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UseItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UseItemRepository::class)]
class UseItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UseCategory::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UseCategory $category = null;

    #[ORM\Column(length: 255)]
    private string $nameFr = '';

    #[ORM\Column(length: 255)]
    private string $nameEn = '';

    #[ORM\Column(length: 255)]
    private string $valueFr = '';

    #[ORM\Column(length: 255)]
    private string $valueEn = '';

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?UseCategory
    {
        return $this->category;
    }

    public function setCategory(?UseCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getNameFr(): string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): static
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * @return array{fr: string, en: string}
     */
    public function getName(): array
    {
        return ['fr' => $this->nameFr, 'en' => $this->nameEn];
    }

    public function getValueFr(): string
    {
        return $this->valueFr;
    }

    public function setValueFr(string $valueFr): static
    {
        $this->valueFr = $valueFr;

        return $this;
    }

    public function getValueEn(): string
    {
        return $this->valueEn;
    }

    public function setValueEn(string $valueEn): static
    {
        $this->valueEn = $valueEn;

        return $this;
    }

    /**
     * @return array{fr: string, en: string}
     */
    public function getValue(): array
    {
        return ['fr' => $this->valueFr, 'en' => $this->valueEn];
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
