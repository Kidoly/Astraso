<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607055724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD institution_id INT DEFAULT NULL, ADD hashtag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D10405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D10405986 ON post (institution_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DFB34EF56 ON post (hashtag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D10405986');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFB34EF56');
        $this->addSql('DROP INDEX IDX_5A8A6C8D10405986 ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8DFB34EF56 ON post');
        $this->addSql('ALTER TABLE post DROP institution_id, DROP hashtag_id');
    }
}
