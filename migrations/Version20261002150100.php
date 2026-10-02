<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002150100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute slug/pinned/content au projet, épingle SymAgri';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD slug VARCHAR(255) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE project ADD pinned BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE project ADD content_fr TEXT DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE project ADD content_en TEXT DEFAULT \'\' NOT NULL');

        // Slugs, et contenu initial repris du résumé existant — à étoffer
        // ensuite dans l'admin.
        $this->addSql("UPDATE project SET slug = 'petroslabs', content_fr = summary_fr, content_en = summary_en WHERE name = 'PetrosLabs'");
        $this->addSql("UPDATE project SET slug = 'symfony-env', content_fr = summary_fr, content_en = summary_en WHERE name = 'Symfony Env'");
        $this->addSql("UPDATE project SET slug = 'loto-quine', content_fr = summary_fr, content_en = summary_en WHERE name = 'Loto Quine'");
        $this->addSql("UPDATE project SET slug = 'symagri', content_fr = summary_fr, content_en = summary_en, pinned = true WHERE name = 'SymAgri'");

        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE989D9B62 ON project (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE989D9B62');
        $this->addSql('ALTER TABLE project DROP slug');
        $this->addSql('ALTER TABLE project DROP pinned');
        $this->addSql('ALTER TABLE project DROP content_fr');
        $this->addSql('ALTER TABLE project DROP content_en');
    }
}
