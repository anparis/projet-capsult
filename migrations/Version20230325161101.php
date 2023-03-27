<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325161101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON connection');
        $this->addSql('ALTER TABLE connection DROP id, CHANGE connected_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE connection ADD PRIMARY KEY (capsule_id, bloc_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection ADD id INT AUTO_INCREMENT NOT NULL, CHANGE created_at connected_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
