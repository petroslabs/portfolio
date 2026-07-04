<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HubLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HubLinkRepository::class)]
class HubLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $labelFr = '';

    #[ORM\Column(length: 255)]
    private string $labelEn = '';

    #[ORM\Column(length: 255)]
    private string $url = '';

    #[ORM\Column(length: 32)]
    private string $icon = 'link';

    #[ORM\Column(length: 16)]
    private string $accent = 'teal';

    #[ORM\Column]
    private bool $external = false;

    #[ORM\Column(length: 255)]
    private string $descriptionFr = '';

    #[ORM\Column(length: 255)]
    private string $descriptionEn = '';

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelFr(): string
    {
        return $this->labelFr;
    }

    public function setLabelFr(string $labelFr): static
    {
        $this->labelFr = $labelFr;

        return $this;
    }

    public function getLabelEn(): string
    {
        return $this->labelEn;
    }

    public function setLabelEn(string $labelEn): static
    {
        $this->labelEn = $labelEn;

        return $this;
    }

    /**
     * Compat gabarits Twig : |localized attend un tableau { fr, en }.
     *
     * @return array{fr: string, en: string}
     */
    public function getLabel(): array
    {
        return ['fr' => $this->labelFr, 'en' => $this->labelEn];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getAccent(): string
    {
        return $this->accent;
    }

    public function setAccent(string $accent): static
    {
        $this->accent = $accent;

        return $this;
    }

    public function isExternal(): bool
    {
        return $this->external;
    }

    public function setExternal(bool $external): static
    {
        $this->external = $external;

        return $this;
    }

    public function getDescriptionFr(): string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(string $descriptionFr): static
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    public function getDescriptionEn(): string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn): static
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * @return array{fr: string, en: string}
     */
    public function getDescription(): array
    {
        return ['fr' => $this->descriptionFr, 'en' => $this->descriptionEn];
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
