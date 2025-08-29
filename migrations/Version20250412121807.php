<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412121807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendez_vous CHANGE id_rdv id_rdv INT AUTO_INCREMENT NOT NULL, CHANGE id_entretien id_entretien INT DEFAULT NULL, ADD PRIMARY KEY (id_rdv)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AAB6D6AFD FOREIGN KEY (id_entretien) REFERENCES entretien (id_entretien)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_65E8AA0AAB6D6AFD ON rendez_vous (id_entretien)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendez_vous MODIFY id_rdv INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AAB6D6AFD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_65E8AA0AAB6D6AFD ON rendez_vous
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON rendez_vous
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rendez_vous CHANGE id_rdv id_rdv INT NOT NULL, CHANGE id_entretien id_entretien INT NOT NULL
        SQL);
    }
}
