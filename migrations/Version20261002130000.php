<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le projet SymAgri';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (name, summary_fr, summary_en, image, stack, status, repo_url, demo_url, position)
            VALUES (
                'SymAgri',
                'SaaS pour les entreprises de travaux agricoles (ETA) : saisie terrain par les chauffeurs (PWA offline-first), remontée automatique, facturation avec barèmes et TVA métier. Projet en cours, destiné à la commercialisation.',
                'SaaS for agricultural contracting businesses: offline-first field data entry by drivers, automatic sync, invoicing with trade-specific rates and VAT rules. In progress, built for commercial sale.',
                'images/projects/project-5.webp',
                '["Symfony", "PHP 8.5", "Vue 3", "PostgreSQL"]',
                'in_progress',
                NULL,
                'https://symagri.fr',
                (SELECT COALESCE(MAX(position), 0) + 1 FROM project)
            )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE name = 'SymAgri'");
    }
}
