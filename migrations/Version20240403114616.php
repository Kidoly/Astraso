<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403114616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, body LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follow (id INT AUTO_INCREMENT NOT NULL, following_user_id INT NOT NULL, followed_user_id INT DEFAULT NULL, institution_id INT DEFAULT NULL, hashtag_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_683444701896F387 (following_user_id), INDEX IDX_68344470AF2612FD (followed_user_id), INDEX IDX_6834447010405986 (institution_id), INDEX IDX_68344470FB34EF56 (hashtag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hashtag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hashtagpc (id INT AUTO_INCREMENT NOT NULL, comment_id INT DEFAULT NULL, post_id INT DEFAULT NULL, hashtag_id INT NOT NULL, INDEX IDX_1D00926EF8697D13 (comment_id), INDEX IDX_1D00926E4B89032C (post_id), INDEX IDX_1D00926EFB34EF56 (hashtag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, image LONGBLOB NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_post (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_A8B086133DA5256D (image_id), INDEX IDX_A8B086134B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE institution (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, postal_code INT NOT NULL, address VARCHAR(255) NOT NULL, accepted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, superlike TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_AC6340B3A76ED395 (user_id), INDEX IDX_AC6340B34B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT DEFAULT NULL, timing TIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, user_reporter_id INT NOT NULL, user_reported_id INT DEFAULT NULL, post_id INT DEFAULT NULL, reason_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C42F778448D42F60 (user_reporter_id), INDEX IDX_C42F77843DA62723 (user_reported_id), INDEX IDX_C42F77844B89032C (post_id), INDEX IDX_C42F778459BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, institution_id INT DEFAULT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, biography LONGTEXT DEFAULT NULL, token_forgotten_password INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id), INDEX IDX_8D93D64910405986 (institution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_683444701896F387 FOREIGN KEY (following_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470AF2612FD FOREIGN KEY (followed_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_6834447010405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470FB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hashtagpc ADD CONSTRAINT FK_1D00926EF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hashtagpc ADD CONSTRAINT FK_1D00926E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hashtagpc ADD CONSTRAINT FK_1D00926EFB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_post ADD CONSTRAINT FK_A8B086133DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_post ADD CONSTRAINT FK_A8B086134B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778448D42F60 FOREIGN KEY (user_reporter_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77843DA62723 FOREIGN KEY (user_reported_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77844B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778459BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64910405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_683444701896F387');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470AF2612FD');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_6834447010405986');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470FB34EF56');
        $this->addSql('ALTER TABLE hashtagpc DROP FOREIGN KEY FK_1D00926EF8697D13');
        $this->addSql('ALTER TABLE hashtagpc DROP FOREIGN KEY FK_1D00926E4B89032C');
        $this->addSql('ALTER TABLE hashtagpc DROP FOREIGN KEY FK_1D00926EFB34EF56');
        $this->addSql('ALTER TABLE image_post DROP FOREIGN KEY FK_A8B086133DA5256D');
        $this->addSql('ALTER TABLE image_post DROP FOREIGN KEY FK_A8B086134B89032C');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778448D42F60');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77843DA62723');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77844B89032C');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778459BB1592');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64910405986');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE follow');
        $this->addSql('DROP TABLE hashtag');
        $this->addSql('DROP TABLE hashtagpc');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE image_post');
        $this->addSql('DROP TABLE institution');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE reason');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
