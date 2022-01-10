<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119134600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18ABE04EA9');
        $this->addSql('DROP INDEX IDX_CD1DE18ABE04EA9 ON department');
        $this->addSql('ALTER TABLE department DROP job_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE department ADD job_id INT NOT NULL');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18ABE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('CREATE INDEX IDX_CD1DE18ABE04EA9 ON department (job_id)');
    }
}
