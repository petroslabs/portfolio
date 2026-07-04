<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profil du hub (landing) : identité + bio. Singleton — une seule ligne,
 * éditée depuis /admin/profile (pas de création/suppression).
 */
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(length: 255)]
    private string $taglineFr = '';

    #[ORM\Column(length: 255)]
    private string $taglineEn = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $bioFr = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $bioEn = '';

    #[ORM\Column(length: 255)]
    private string $logo = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTaglineFr(): string
    {
        return $this->taglineFr;
    }

    public function setTaglineFr(string $taglineFr): static
    {
        $this->taglineFr = $taglineFr;

        return $this;
    }

    public function getTaglineEn(): string
    {
        return $this->taglineEn;
    }

    public function setTaglineEn(string $taglineEn): static
    {
        $this->taglineEn = $taglineEn;

        return $this;
    }

    /**
     * Compat gabarits Twig : |localized attend un tableau { fr, en }.
     *
     * @return array{fr: string, en: string}
     */
    public function getTagline(): array
    {
        return ['fr' => $this->taglineFr, 'en' => $this->taglineEn];
    }

    public function getBioFr(): string
    {
        return $this->bioFr;
    }

    public function setBioFr(string $bioFr): static
    {
        $this->bioFr = $bioFr;

        return $this;
    }

    public function getBioEn(): string
    {
        return $this->bioEn;
    }

    public function setBioEn(string $bioEn): static
    {
        $this->bioEn = $bioEn;

        return $this;
    }

    /**
     * @return array{fr: string, en: string}
     */
    public function getBio(): array
    {
        return ['fr' => $this->bioFr, 'en' => $this->bioEn];
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}
