<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228152202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955A3DA5256D');
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955AEDAAC352');
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955AEA6DF1F1');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE lien');
        $this->addSql('DROP TABLE texte');
        $this->addSql('DROP INDEX UNIQ_C778955AEDAAC352 ON bloc');
        $this->addSql('DROP INDEX UNIQ_C778955AEA6DF1F1 ON bloc');
        $this->addSql('DROP INDEX UNIQ_C778955A3DA5256D ON bloc');
        $this->addSql('ALTER TABLE bloc DROP lien_id, DROP texte_id, DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, nom_fichier VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type_fichier VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE lien (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE texte (id INT AUTO_INCREMENT NOT NULL, texte LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bloc ADD lien_id INT DEFAULT NULL, ADD texte_id INT DEFAULT NULL, ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955A3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955AEA6DF1F1 FOREIGN KEY (texte_id) REFERENCES texte (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955AEDAAC352 FOREIGN KEY (lien_id) REFERENCES lien (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C778955AEDAAC352 ON bloc (lien_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C778955AEA6DF1F1 ON bloc (texte_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C778955A3DA5256D ON bloc (image_id)');
    }
}
