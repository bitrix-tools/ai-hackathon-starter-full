<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028121417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_installation ALTER created_at_utc TYPE TIMESTAMP(3) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE application_installation ALTER update_at_utc TYPE TIMESTAMP(3) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER created_at_utc TYPE TIMESTAMP(3) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER updated_at_utc TYPE TIMESTAMP(3) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER auth_token_expires TYPE BIGINT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE application_installation ALTER created_at_utc TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE application_installation ALTER update_at_utc TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER created_at_utc TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER updated_at_utc TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE bitrix24account ALTER auth_token_expires TYPE INT');
    }
}
