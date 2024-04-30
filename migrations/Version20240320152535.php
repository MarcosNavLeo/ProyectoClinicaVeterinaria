<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320152535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medicamentos (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, instrucciones VARCHAR(255) NOT NULL, foto VARCHAR(255) NOT NULL, dosis VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tratamientos ADD medicamentos_id INT NOT NULL');
        $this->addSql('ALTER TABLE tratamientos ADD CONSTRAINT FK_42DC56BC68CAEFE5 FOREIGN KEY (medicamentos_id) REFERENCES medicamentos (id)');
        $this->addSql('CREATE INDEX IDX_42DC56BC68CAEFE5 ON tratamientos (medicamentos_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tratamientos DROP FOREIGN KEY FK_42DC56BC68CAEFE5');
        $this->addSql('DROP TABLE medicamentos');
        $this->addSql('DROP INDEX IDX_42DC56BC68CAEFE5 ON tratamientos');
        $this->addSql('ALTER TABLE tratamientos DROP medicamentos_id');
    }
}
