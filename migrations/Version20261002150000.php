<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Complète les placeholders de L'établi, ajoute OS et la catégorie Infra partagée";
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            UPDATE use_item SET value_fr = 'PhpStorm', value_en = 'PhpStorm'
            WHERE name_fr = 'Éditeur'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE use_item SET value_fr = 'Ghostty', value_en = 'Ghostty'
            WHERE name_fr = 'Terminal'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE use_item SET value_fr = 'bash', value_en = 'bash'
            WHERE name_fr = 'Shell'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE use_item SET value_fr = 'Framework Laptop 13', value_en = 'Framework Laptop 13'
            WHERE name_fr = 'Machine'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE use_item SET value_fr = 'Keychron K3 Pro (75%)', value_en = 'Keychron K3 Pro (75%)'
            WHERE name_fr = 'Clavier'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE use_item SET
                value_fr = 'Aucun pour l''instant — déploiement manuel (make deploy-prod)',
                value_en = 'None yet — manual deployment (make deploy-prod)'
            WHERE name_fr = 'CI/CD'
            SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO use_item (category_id, name_fr, name_en, value_fr, value_en, position)
            VALUES (
                (SELECT id FROM use_category WHERE name_fr = 'Éditeur & terminal'),
                'OS', 'OS',
                'Omarchy (Arch Linux + Hyprland)', 'Omarchy (Arch Linux + Hyprland)',
                4
            )
            SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO use_category (name_fr, name_en, position)
            VALUES ('Infra partagée', 'Shared infra', (SELECT COALESCE(MAX(position), 0) + 1 FROM use_category))
            SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO use_item (category_id, name_fr, name_en, value_fr, value_en, position) VALUES
                ((SELECT id FROM use_category WHERE name_fr = 'Infra partagée'), 'Reverse proxy', 'Reverse proxy', 'Traefik', 'Traefik', 1),
                ((SELECT id FROM use_category WHERE name_fr = 'Infra partagée'), 'Orchestration', 'Orchestration', 'Docker Compose (multi-dépôts)', 'Docker Compose (multi-repo)', 2),
                ((SELECT id FROM use_category WHERE name_fr = 'Infra partagée'), 'Certificats TLS (dev)', 'TLS certificates (dev)', 'mkcert', 'mkcert', 3)
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM use_item WHERE category_id = (SELECT id FROM use_category WHERE name_fr = 'Infra partagée')");
        $this->addSql("DELETE FROM use_category WHERE name_fr = 'Infra partagée'");
        $this->addSql("DELETE FROM use_item WHERE name_fr = 'OS' AND category_id = (SELECT id FROM use_category WHERE name_fr = 'Éditeur & terminal')");
    }
}
