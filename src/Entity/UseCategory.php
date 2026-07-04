<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UseCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UseCategoryRepository::class)]
class UseCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nameFr = '';

    #[ORM\Column(length: 255)]
    private string $nameEn = '';

    #[ORM\Column]
    private int $position = 0;

    /**
     * @var Collection<int, UseItem>
     */
    #[ORM\OneToMany(targetEntity: UseItem::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * Compat gabarits Twig : |localized attend un tableau { fr, en }.
     *
     * @return array{fr: string, en: string}
     */
    public function getName(): array
    {
        return ['fr' => $this->nameFr, 'en' => $this->nameEn];
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

    /**
     * @return Collection<int, UseItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }
}
