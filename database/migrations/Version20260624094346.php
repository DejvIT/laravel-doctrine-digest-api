<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260624094346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_categories (uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, removed TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62A97E95E237E06 ON article_categories (name)');
        $this->addSql('CREATE TABLE articles (uuid UUID NOT NULL, blogger_uuid UUID NOT NULL, article_category_uuid UUID NOT NULL, title VARCHAR(500) NOT NULL, content TEXT NOT NULL, distributed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, removed TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_BFDD3168F2AE321D ON articles (blogger_uuid)');
        $this->addSql('CREATE INDEX IDX_BFDD3168DD3FC544 ON articles (article_category_uuid)');
        $this->addSql('CREATE INDEX idx_article_distributed_at ON articles (distributed_at)');
        $this->addSql('CREATE TABLE blogger_article_category (blogger_uuid UUID NOT NULL, article_category_uuid UUID NOT NULL, PRIMARY KEY(blogger_uuid, article_category_uuid))');
        $this->addSql('CREATE INDEX IDX_55CF65A8F2AE321D ON blogger_article_category (blogger_uuid)');
        $this->addSql('CREATE INDEX IDX_55CF65A8DD3FC544 ON blogger_article_category (article_category_uuid)');
        $this->addSql('CREATE TABLE subscribers (uuid UUID NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, removed TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FCD16ACE7927C74 ON subscribers (email)');
        $this->addSql('CREATE TABLE subscriber_article_category (subscriber_uuid UUID NOT NULL, article_category_uuid UUID NOT NULL, PRIMARY KEY(subscriber_uuid, article_category_uuid))');
        $this->addSql('CREATE INDEX IDX_83594F751FDD7F86 ON subscriber_article_category (subscriber_uuid)');
        $this->addSql('CREATE INDEX IDX_83594F75DD3FC544 ON subscriber_article_category (article_category_uuid)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F2AE321D FOREIGN KEY (blogger_uuid) REFERENCES bloggers (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168DD3FC544 FOREIGN KEY (article_category_uuid) REFERENCES article_categories (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blogger_article_category ADD CONSTRAINT FK_55CF65A8F2AE321D FOREIGN KEY (blogger_uuid) REFERENCES bloggers (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blogger_article_category ADD CONSTRAINT FK_55CF65A8DD3FC544 FOREIGN KEY (article_category_uuid) REFERENCES article_categories (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriber_article_category ADD CONSTRAINT FK_83594F751FDD7F86 FOREIGN KEY (subscriber_uuid) REFERENCES subscribers (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriber_article_category ADD CONSTRAINT FK_83594F75DD3FC544 FOREIGN KEY (article_category_uuid) REFERENCES article_categories (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bloggers ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bloggers ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bloggers ADD name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55A2B56FE7927C74 ON bloggers (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD3168F2AE321D');
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD3168DD3FC544');
        $this->addSql('ALTER TABLE blogger_article_category DROP CONSTRAINT FK_55CF65A8F2AE321D');
        $this->addSql('ALTER TABLE blogger_article_category DROP CONSTRAINT FK_55CF65A8DD3FC544');
        $this->addSql('ALTER TABLE subscriber_article_category DROP CONSTRAINT FK_83594F751FDD7F86');
        $this->addSql('ALTER TABLE subscriber_article_category DROP CONSTRAINT FK_83594F75DD3FC544');
        $this->addSql('DROP TABLE article_categories');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE blogger_article_category');
        $this->addSql('DROP TABLE subscribers');
        $this->addSql('DROP TABLE subscriber_article_category');
        $this->addSql('DROP INDEX UNIQ_55A2B56FE7927C74');
        $this->addSql('ALTER TABLE bloggers DROP email');
        $this->addSql('ALTER TABLE bloggers DROP password');
        $this->addSql('ALTER TABLE bloggers DROP name');
    }
}
