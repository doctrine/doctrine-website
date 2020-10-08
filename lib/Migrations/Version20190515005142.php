<?php

declare(strict_types=1);

namespace Doctrine\Website\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190515005142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event_participants table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event_participants (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, quantity INT NOT NULL, eventId INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE event_participants');
    }
}
