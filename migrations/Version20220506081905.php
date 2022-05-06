<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506081905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts ADD owner_id VARCHAR(255) NOT NULL, ADD owner_profile_picture VARCHAR(255) NOT NULL, ADD owner_username VARCHAR(255) NOT NULL, DROP owner');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts ADD owner VARCHAR(10) NOT NULL, DROP owner_id, DROP owner_profile_picture, DROP owner_username');
    }
}
