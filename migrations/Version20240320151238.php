<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320151238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mascotas (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, especie VARCHAR(100) NOT NULL, raza VARCHAR(100) NOT NULL, fecha_nacimiento DATE NOT NULL, INDEX IDX_D57E0219A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mascotas ADD CONSTRAINT FK_D57E0219A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mascotas DROP FOREIGN KEY FK_D57E0219A76ED395');
        $this->addSql('DROP TABLE mascotas');
    }
}
