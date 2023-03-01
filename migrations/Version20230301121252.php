<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301121252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, bloc_id INT DEFAULT NULL, nom_fichier VARCHAR(150) NOT NULL, type_fichier VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_C53D045F5582E9C0 (bloc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lien (id INT AUTO_INCREMENT NOT NULL, bloc_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A532B4B55582E9C0 (bloc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE texte (id INT AUTO_INCREMENT NOT NULL, bloc_id INT DEFAULT NULL, texte LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_EAE1A6EE5582E9C0 (bloc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('ALTER TABLE lien ADD CONSTRAINT FK_A532B4B55582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('ALTER TABLE texte ADD CONSTRAINT FK_EAE1A6EE5582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('ALTER TABLE bloc DROP img_url, DROP img_nom_fichier, DROP img_extension, DROP txt_txt, DROP link_url, DROP link_thumbnail');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5582E9C0');
        $this->addSql('ALTER TABLE lien DROP FOREIGN KEY FK_A532B4B55582E9C0');
        $this->addSql('ALTER TABLE texte DROP FOREIGN KEY FK_EAE1A6EE5582E9C0');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE lien');
        $this->addSql('DROP TABLE texte');
        $this->addSql('ALTER TABLE bloc ADD img_url VARCHAR(255) NOT NULL, ADD img_nom_fichier VARCHAR(100) NOT NULL, ADD img_extension VARCHAR(10) NOT NULL, ADD txt_txt LONGTEXT NOT NULL, ADD link_url VARCHAR(255) NOT NULL, ADD link_thumbnail VARCHAR(255) DEFAULT NULL');
    }
}
