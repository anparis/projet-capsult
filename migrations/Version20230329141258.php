<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230329141258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773665582E9C0');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366714704E9');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773665582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366714704E9');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773665582E9C0');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773665582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
