<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512074540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY posts_ibfk_1');
        $this->addSql('DROP INDEX owner_2 ON posts');
        $this->addSql('DROP INDEX owner ON posts');
        $this->addSql('ALTER TABLE posts CHANGE owner owner VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE likes');
        $this->addSql('ALTER TABLE posts CHANGE owner owner INT NOT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT posts_ibfk_1 FOREIGN KEY (owner) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX owner_2 ON posts (owner)');
        $this->addSql('CREATE INDEX owner ON posts (owner)');
    }
}
