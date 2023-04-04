<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404083354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_capsule_follow (user_id INT NOT NULL, capsule_id INT NOT NULL, INDEX IDX_E43A548FA76ED395 (user_id), INDEX IDX_E43A548F714704E9 (capsule_id), PRIMARY KEY(user_id, capsule_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_capsule_follow ADD CONSTRAINT FK_E43A548FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_capsule_follow ADD CONSTRAINT FK_E43A548F714704E9 FOREIGN KEY (capsule_id) REFERENCES capsule (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_capsule_follow DROP FOREIGN KEY FK_E43A548FA76ED395');
        $this->addSql('ALTER TABLE user_capsule_follow DROP FOREIGN KEY FK_E43A548F714704E9');
        $this->addSql('DROP TABLE user_capsule_follow');
    }
}
