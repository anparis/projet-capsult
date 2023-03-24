<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324171603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE connection (id INT AUTO_INCREMENT NOT NULL, capsule_id INT NOT NULL, bloc_id INT NOT NULL, connected_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_29F77366714704E9 (capsule_id), INDEX IDX_29F773665582E9C0 (bloc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773665582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366714704E9');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773665582E9C0');
        $this->addSql('DROP TABLE connection');
    }
}
