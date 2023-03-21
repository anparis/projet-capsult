<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321171559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE texte DROP FOREIGN KEY FK_EAE1A6EE5582E9C0');
        $this->addSql('DROP TABLE texte');
        $this->addSql('ALTER TABLE bloc ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP date_creation');
        $this->addSql('ALTER TABLE capsule CHANGE date_creation created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE texte (id INT AUTO_INCREMENT NOT NULL, bloc_id INT DEFAULT NULL, texte LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_EAE1A6EE5582E9C0 (bloc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE texte ADD CONSTRAINT FK_EAE1A6EE5582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE bloc ADD date_creation DATETIME NOT NULL, DROP created_at');
        $this->addSql('ALTER TABLE user DROP username');
        $this->addSql('ALTER TABLE capsule CHANGE created_at date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
