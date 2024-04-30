<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320151558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultas (id INT AUTO_INCREMENT NOT NULL, citas_id INT NOT NULL, fecha_hora DATETIME NOT NULL, sintomas VARCHAR(150) NOT NULL, diagnostico VARCHAR(255) NOT NULL, INDEX IDX_7AC3CEE7F103737D (citas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultas ADD CONSTRAINT FK_7AC3CEE7F103737D FOREIGN KEY (citas_id) REFERENCES citas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultas DROP FOREIGN KEY FK_7AC3CEE7F103737D');
        $this->addSql('DROP TABLE consultas');
    }
}
