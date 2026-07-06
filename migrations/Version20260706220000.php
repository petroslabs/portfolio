<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le projet Symfony Env (infra Docker partagée)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (name, summary_fr, summary_en, image, stack, status, repo_url, demo_url, position)
            VALUES (
                'Symfony Env',
                'Infrastructure Docker partagée pour héberger plusieurs projets Symfony : Traefik (reverse proxy + TLS), PostgreSQL et Redis mutualisés, un environnement par sous-domaine et sa propre base isolée. Certificats Let''s Encrypt automatiques et sauvegardes PostgreSQL quotidiennes en production.',
                'Shared Docker infrastructure hosting multiple Symfony projects: Traefik (reverse proxy + TLS), pooled PostgreSQL and Redis, one subdomain and isolated database per app. Automatic Let''s Encrypt certificates and daily PostgreSQL backups in production.',
                'images/projects/project-2.webp',
                '["Docker Compose", "Traefik", "PostgreSQL", "Redis", "Lets Encrypt"]',
                'done',
                'https://github.com/petroslabs/environment',
                NULL,
                2
            )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE name = 'Symfony Env'");
    }
}
