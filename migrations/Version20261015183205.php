<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015183205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le projet DiagValise (dépôt privé), lie l\'article de blog existant';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (slug, name, summary_fr, summary_en, content_fr, content_en, image, stack, status, pinned, repo_url, demo_url, position)
            VALUES (
                'diagvalise',
                'DiagValise',
                'Valise de diagnostic automobile sur Raspberry Pi 5 : lecture OBD-II en wifi, codes défaut, historique de sessions — encore en chantier.',
                'Car diagnostic suitcase on a Raspberry Pi 5: OBD-II readings over wifi, trouble codes, session history — still a work in progress.',
                'Adaptateur ELM327 (VLinker FS) branché sur le port OBD-II du véhicule, Raspberry Pi 5 qui lit les données du bus et les expose en HTTP/WebSocket, interface consultée depuis un laptop ou un téléphone en wifi — la valise crée son propre hotspot, pas besoin de réseau existant sur place.

            Fonctionnalités : lecture en direct (régime moteur, vitesse, températures, papillon, niveau carburant, tension batterie), lecture/effacement des codes défaut (DTC), lecture du VIN, historique des sessions de diagnostic, dashboard web temps réel + CLI, reconnexion automatique si l''adaptateur n''est pas branché au démarrage.

            Projet non terminé, poursuivi au fil des soirées disponibles.',
                'ELM327 adapter (VLinker FS) plugged into the vehicle''s OBD-II port, Raspberry Pi 5 reading the bus data and exposing it over HTTP/WebSocket, interface checked from a laptop or phone over wifi — the suitcase creates its own hotspot, no existing network needed on site.

            Features: live readings (RPM, speed, temperatures, throttle, fuel level, battery voltage), reading/clearing trouble codes (DTC), VIN reading, diagnostic session history, real-time web dashboard + CLI, automatic reconnection if the adapter isn''t plugged in at startup.

            Not finished, worked on as evenings allow.',
                'images/projects/diagvalise.webp',
                '["Python", "FastAPI", "Docker", "Raspberry Pi"]',
                'in_progress',
                false,
                NULL,
                NULL,
                6
            )
            SQL);

        $this->addSql(<<<'SQL'
            UPDATE blog_post SET project_id = (SELECT id FROM project WHERE slug = 'diagvalise')
            WHERE slug = 'diagvalise'
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE slug = 'diagvalise'");
    }
}
