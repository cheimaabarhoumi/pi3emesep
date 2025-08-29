<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407192841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, voiture_id INT NOT NULL, titre VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_F65593E5181A8BA (voiture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5181A8BA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE annonce
        SQL);
    }
}
