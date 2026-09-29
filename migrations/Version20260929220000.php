<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260929220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le projet Loto Quine';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO project (name, summary_fr, summary_en, image, stack, status, repo_url, demo_url, position)
            VALUES (
                'Loto Quine',
                'Tableau d''affichage pour animer un loto quine : grille 1-90, dernier numéro tiré, historique des manches. Une seule page HTML/CSS/JS, sans dépendance ni serveur — fonctionne entièrement hors connexion, pensé pour être projeté en soirée.',
                'A display board for hosting a loto quine (French bingo-style game): 1-90 grid, last number drawn, round history. A single HTML/CSS/JS page, no dependencies or server — works fully offline, built to be projected during the event.',
                'images/projects/project-4.webp',
                '["HTML", "CSS", "JavaScript"]',
                'done',
                'https://github.com/petroslabs/loto-quine-app',
                NULL,
                (SELECT COALESCE(MAX(position), 0) + 1 FROM project)
            )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM project WHERE name = 'Loto Quine'");
    }
}
