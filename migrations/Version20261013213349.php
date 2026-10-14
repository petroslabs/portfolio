<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261013213349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Logo du profil converti en WebP (297 Ko → 52 Ko, redimensionné de 1254² à 512²)";
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE profile SET logo = 'images/logo.webp' WHERE logo = 'images/logo.png'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE profile SET logo = 'images/logo.png' WHERE logo = 'images/logo.webp'");
    }
}
