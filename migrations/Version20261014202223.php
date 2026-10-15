<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261014202223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le projet Homelab (épinglé, dépôt privé)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (slug, name, summary_fr, summary_en, content_fr, content_en, image, stack, status, pinned, repo_url, demo_url, position)
            VALUES (
                'homelab',
                'Homelab',
                'Infrastructure personnelle auto-hébergée (Proxmox, DNS récursif, reverse proxy, domotique) — zéro exposition Internet directe.',
                'Self-hosted personal infrastructure (Proxmox, recursive DNS, reverse proxy, home automation) — zero direct internet exposure.',
                'Infrastructure personnelle sur un Minisforum UN100P (Intel N100, 16 Go) sous Proxmox VE, avec un NAS UGREEN en RAID1 pour le stockage et les backups, un switch managé et un onduleur surveillé via NUT.

            Services : Technitium (résolveur DNS récursif pur, ad-blocking), Caddy (reverse proxy, certificats wildcard Let''s Encrypt via DNS-01), Home Assistant (domotique, Zigbee), Jellyfin (transcodage matériel sur l''iGPU), une stack d''automatisation de médiathèque derrière un VPN de sortie, Paperless-ngx (documents), Uptime Kuma (monitoring) et Tailscale pour l''accès distant.

            Règle unique, non négociable : aucune exposition Internet directe, jamais de port forwarding. Le reste des décisions — bare-metal vs conteneur, LXC vs VM — se tranche service par service plutôt que par cohérence forcée.',
                'Self-hosted infrastructure on a Minisforum UN100P (Intel N100, 16GB) running Proxmox VE, with a UGREEN NAS in RAID1 for storage and backups, a managed switch, and a UPS monitored via NUT.

            Services: Technitium (pure recursive DNS resolver, ad-blocking), Caddy (reverse proxy, wildcard Let''s Encrypt certs via DNS-01), Home Assistant (home automation, Zigbee), Jellyfin (hardware transcoding on the iGPU), a media automation stack behind an outbound VPN, Paperless-ngx (documents), Uptime Kuma (monitoring), and Tailscale for remote access.

            One non-negotiable rule: no direct internet exposure, ever, no port forwarding. Everything else — bare-metal vs container, LXC vs VM — gets decided service by service rather than forced consistency.',
                'images/projects/homelab.webp',
                '["Proxmox VE", "Docker", "Caddy", "Tailscale"]',
                'in_progress',
                true,
                NULL,
                NULL,
                5
            )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE slug = 'homelab'");
    }
}
