<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228185844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc ADD img_url VARCHAR(255) NOT NULL, ADD img_nom_fichier VARCHAR(100) NOT NULL, ADD img_extension VARCHAR(10) NOT NULL, ADD txt_txt LONGTEXT NOT NULL, ADD link_url VARCHAR(255) NOT NULL, ADD link_thumbnail VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP img_url, DROP img_nom_fichier, DROP img_extension, DROP txt_txt, DROP link_url, DROP link_thumbnail');
    }
}
