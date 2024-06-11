<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611122012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE institution ADD chef_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE institution ADD CONSTRAINT FK_3A9F98E5150A48F1 FOREIGN KEY (chef_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A9F98E5150A48F1 ON institution (chef_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE institution DROP FOREIGN KEY FK_3A9F98E5150A48F1');
        $this->addSql('DROP INDEX UNIQ_3A9F98E5150A48F1 ON institution');
        $this->addSql('ALTER TABLE institution DROP chef_id');
    }
}
