<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119133213 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8296CD8AE');
        $this->addSql('DROP INDEX IDX_FBD8E0F8296CD8AE ON job');
        $this->addSql('ALTER TABLE job DROP team_id');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176296CD8AE');
        $this->addSql('DROP INDEX IDX_34DCD176296CD8AE ON person');
        $this->addSql('ALTER TABLE person DROP team_id');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F8F93B6FC');
        $this->addSql('DROP INDEX IDX_C4E0A61F8F93B6FC ON team');
        $this->addSql('ALTER TABLE team DROP movie_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8296CD8AE ON job (team_id)');
        $this->addSql('ALTER TABLE person ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176296CD8AE ON person (team_id)');
        $this->addSql('ALTER TABLE team ADD movie_id INT NOT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F8F93B6FC ON team (movie_id)');
    }
}
