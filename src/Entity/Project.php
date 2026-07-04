<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_ARCHIVED = 'archived';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $summaryFr = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $summaryEn = '';

    #[ORM\Column(length: 255)]
    private string $image = '';

    /** @var list<string> */
    #[ORM\Column(type: Types::JSON)]
    private array $stack = [];

    #[ORM\Column(length: 32)]
    private string $status = self::STATUS_IN_PROGRESS;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repoUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $demoUrl = null;

    #[ORM\Column]
    private int $position = 0;

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

    public function getSummaryFr(): string
    {
        return $this->summaryFr;
    }

    public function setSummaryFr(string $summaryFr): static
    {
        $this->summaryFr = $summaryFr;

        return $this;
    }

    public function getSummaryEn(): string
    {
        return $this->summaryEn;
    }

    public function setSummaryEn(string $summaryEn): static
    {
        $this->summaryEn = $summaryEn;

        return $this;
    }

    /**
     * Compat gabarits Twig : |localized attend un tableau { fr, en }.
     *
     * @return array{fr: string, en: string}
     */
    public function getSummary(): array
    {
        return ['fr' => $this->summaryFr, 'en' => $this->summaryEn];
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @param list<string> $stack
     */
    public function setStack(array $stack): static
    {
        $this->stack = $stack;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRepoUrl(): ?string
    {
        return $this->repoUrl;
    }

    public function setRepoUrl(?string $repoUrl): static
    {
        $this->repoUrl = $repoUrl;

        return $this;
    }

    public function getDemoUrl(): ?string
    {
        return $this->demoUrl;
    }

    public function setDemoUrl(?string $demoUrl): static
    {
        $this->demoUrl = $demoUrl;

        return $this;
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
