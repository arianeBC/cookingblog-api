<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528065715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT DEFAULT NULL, rating SMALLINT DEFAULT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipes (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, theme VARCHAR(60) DEFAULT NULL, title VARCHAR(60) NOT NULL, ingredients LONGTEXT NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(70) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_A369E2B512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, usergroup VARCHAR(40) NOT NULL, username VARCHAR(60) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id)');
        $this->addSql('ALTER TABLE recipes ADD CONSTRAINT FK_A369E2B512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A59D8A214');
        $this->addSql('ALTER TABLE recipes DROP FOREIGN KEY FK_A369E2B512469DE2');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE recipes');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE users');
    }
}
