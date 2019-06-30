<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190630193314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE user_social_networks (id UUID NOT NULL, user_id UUID NOT NULL, name VARCHAR(32) DEFAULT NULL, identity VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_45544C88A76ED395 ON user_social_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_45544C885E237E066A95E9C4 ON user_social_networks (name, identity)');
        $this->addSql('COMMENT ON COLUMN user_social_networks.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('CREATE TABLE user_users (id UUID NOT NULL, email VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, register_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, confirm_token VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(255) NOT NULL, reset_password_token VARCHAR(255) DEFAULT NULL, reset_password_expiry_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1E7927C74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1452C9EC5 ON user_users (reset_password_token)');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'(DC2Type:user_user_email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.register_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'(DC2Type:user_user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_password_expiry_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_social_networks ADD CONSTRAINT FK_45544C88A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_social_networks DROP CONSTRAINT FK_45544C88A76ED395');
        $this->addSql('DROP TABLE user_social_networks');
        $this->addSql('DROP TABLE user_users');
    }
}
