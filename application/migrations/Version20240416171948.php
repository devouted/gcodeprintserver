<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416171948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE print_job (id INT AUTO_INCREMENT NOT NULL, device VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE print_io ADD printjob_id INT NOT NULL');
        $this->addSql('ALTER TABLE print_io ADD CONSTRAINT FK_51F3EA24F0BD28FE FOREIGN KEY (printjob_id) REFERENCES print_job (id)');
        $this->addSql('CREATE INDEX IDX_51F3EA24F0BD28FE ON print_io (printjob_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE print_io DROP FOREIGN KEY FK_51F3EA24F0BD28FE');
        $this->addSql('DROP TABLE print_job');
        $this->addSql('DROP INDEX IDX_51F3EA24F0BD28FE ON print_io');
        $this->addSql('ALTER TABLE print_io DROP printjob_id');
    }
}
