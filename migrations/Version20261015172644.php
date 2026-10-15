<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015172644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Lie un article de blog à un projet (BlogPost.project), relie diagvalise et homelab';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_BA5AE01D166D1F9C ON blog_post (project_id)');

        $this->addSql(<<<'SQL'
            UPDATE blog_post SET project_id = (SELECT id FROM project WHERE slug = 'diagvalise')
            WHERE slug = 'diagvalise'
            SQL);
        $this->addSql(<<<'SQL'
            UPDATE blog_post SET project_id = (SELECT id FROM project WHERE slug = 'homelab')
            WHERE slug = 'homelab'
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01D166D1F9C');
        $this->addSql('DROP INDEX IDX_BA5AE01D166D1F9C');
        $this->addSql('ALTER TABLE blog_post DROP project_id');
    }
}
