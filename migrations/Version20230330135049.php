<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330135049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP slug');
        $this->addSql('ALTER TABLE capsule CHANGE slug slug VARCHAR(150) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C268A183989D9B62 ON capsule (slug)');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773665582E9C0');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366714704E9');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773665582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id)');
        $this->addSql('ALTER TABLE user CHANGE slug slug VARCHAR(150) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649989D9B62 ON user (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649989D9B62 ON user');
        $this->addSql('ALTER TABLE user CHANGE slug slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE bloc ADD slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_C268A183989D9B62 ON capsule');
        $this->addSql('ALTER TABLE capsule CHANGE slug slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366714704E9');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773665582E9C0');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773665582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
