<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260704222548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Retire la bannière (abandonnée) du profil';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE profile DROP banner');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE profile ADD banner VARCHAR(255) DEFAULT NULL');
    }
}
