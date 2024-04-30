<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320151341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE citas ADD mascostas_id INT NOT NULL');
        $this->addSql('ALTER TABLE citas ADD CONSTRAINT FK_B88CF8E524C21B50 FOREIGN KEY (mascostas_id) REFERENCES mascotas (id)');
        $this->addSql('CREATE INDEX IDX_B88CF8E524C21B50 ON citas (mascostas_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE citas DROP FOREIGN KEY FK_B88CF8E524C21B50');
        $this->addSql('DROP INDEX IDX_B88CF8E524C21B50 ON citas');
        $this->addSql('ALTER TABLE citas DROP mascostas_id');
    }
}
