<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002151300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remplace le projet Symfony Env (démantelé) par Infra';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE project SET
                slug = 'infra',
                name = 'Infra',
                summary_fr = 'Reverse proxy Traefik partagé entre les projets PetrosLabs hébergés sur un même VPS — chaque projet reste autonome (sa propre base embarquée), seul le proxy est mutualisé.',
                summary_en = 'Shared Traefik reverse proxy for PetrosLabs projects colocated on the same VPS — each project stays self-contained (its own embedded database), only the proxy is pooled.',
                content_fr = 'Reverse proxy Traefik partagé entre les projets PetrosLabs hébergés sur le même VPS. Chaque projet reste autonome — sa propre base de données embarquée, son propre conteneur applicatif — mais un seul process peut tenir les ports 80 et 443 sur une machine. Ce dépôt fournit ce proxy, une fois, pour tous les projets colocalisés.

            Un projet rejoint le proxy en pointant `PROXY_NETWORK` vers le réseau Docker qu''il possède, et en déclarant son routage par des étiquettes Traefik — aucune dépendance de code, aucune donnée partagée.

            Certificats mkcert en développement, Let''s Encrypt automatique en production. Accès au socket Docker en lecture seule uniquement, via `tecnativa/docker-socket-proxy`.',
                content_en = 'Shared Traefik reverse proxy for PetrosLabs projects colocated on the same VPS. Each project stays self-contained — its own embedded database, its own application container — but only one process can hold ports 80 and 443 on a machine. This repository provides that proxy, once, for every project sharing the host.

            A project joins the proxy by pointing `PROXY_NETWORK` at the Docker network it owns, and declaring its routing through Traefik labels — no code dependency, no shared data.

            mkcert certificates in development, automatic Let''s Encrypt in production. Read-only access to the Docker socket only, via `tecnativa/docker-socket-proxy`.',
                stack = '["Traefik", "Docker Compose", "mkcert", "Let''s Encrypt"]',
                status = 'done',
                repo_url = 'https://github.com/petroslabs/infra',
                demo_url = NULL
            WHERE slug = 'symfony-env'
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE project SET
                slug = 'symfony-env',
                name = 'Symfony Env',
                summary_fr = 'Infrastructure Docker partagée pour héberger plusieurs projets Symfony : Traefik (reverse proxy + TLS), PostgreSQL et Redis mutualisés, un environnement par sous-domaine et sa propre base isolée. Certificats Let''s Encrypt automatiques et sauvegardes PostgreSQL quotidiennes en production.',
                summary_en = 'Shared Docker infrastructure hosting multiple Symfony projects: Traefik (reverse proxy + TLS), pooled PostgreSQL and Redis, one subdomain and isolated database per app. Automatic Let''s Encrypt certificates and daily PostgreSQL backups in production.',
                content_fr = 'Infrastructure Docker partagée pour héberger plusieurs projets Symfony : Traefik (reverse proxy + TLS), PostgreSQL et Redis mutualisés, un environnement par sous-domaine et sa propre base isolée. Certificats Let''s Encrypt automatiques et sauvegardes PostgreSQL quotidiennes en production.',
                content_en = 'Shared Docker infrastructure hosting multiple Symfony projects: Traefik (reverse proxy + TLS), pooled PostgreSQL and Redis, one subdomain and isolated database per app. Automatic Let''s Encrypt certificates and daily PostgreSQL backups in production.',
                stack = '["Docker Compose", "Traefik", "PostgreSQL", "Redis", "Lets Encrypt"]',
                status = 'done',
                repo_url = 'https://github.com/petroslabs/environment',
                demo_url = NULL
            WHERE slug = 'infra'
            SQL);
    }
}
