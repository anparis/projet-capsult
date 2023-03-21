<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321205548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955A714704E9');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955A714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE capsule DROP FOREIGN KEY FK_C268A183A76ED395');
        $this->addSql('ALTER TABLE capsule CHANGE ouvert open TINYINT(1) NOT NULL, CHANGE statut status VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE capsule ADD CONSTRAINT FK_C268A183A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955A714704E9');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955A714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE capsule DROP FOREIGN KEY FK_C268A183A76ED395');
        $this->addSql('ALTER TABLE capsule CHANGE open ouvert TINYINT(1) NOT NULL, CHANGE status statut VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE capsule ADD CONSTRAINT FK_C268A183A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
