<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506103521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_F41C9B25E7927C74 ON cliente (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa CHANGE nombre_empresa nombre_empresa VARCHAR(255) NOT NULL, CHANGE nif_cif nif_cif VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B8D75A50E7927C74 ON empresa (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B8D75A504CCE1B10 ON empresa (nombre_empresa)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B8D75A50B67D4AB7 ON empresa (nif_cif)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_B8D75A50E7927C74 ON empresa
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_B8D75A504CCE1B10 ON empresa
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_B8D75A50B67D4AB7 ON empresa
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa CHANGE nombre_empresa nombre_empresa VARCHAR(255) DEFAULT NULL, CHANGE nif_cif nif_cif VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_F41C9B25E7927C74 ON cliente
        SQL);
    }
}
