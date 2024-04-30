<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320152236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE citas DROP FOREIGN KEY FK_B88CF8E5C981A62B');
        $this->addSql('DROP INDEX IDX_B88CF8E5C981A62B ON citas');
        $this->addSql('ALTER TABLE citas DROP tratamientos_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE citas ADD tratamientos_id INT NOT NULL');
        $this->addSql('ALTER TABLE citas ADD CONSTRAINT FK_B88CF8E5C981A62B FOREIGN KEY (tratamientos_id) REFERENCES tratamientos (id)');
        $this->addSql('CREATE INDEX IDX_B88CF8E5C981A62B ON citas (tratamientos_id)');
    }
}
