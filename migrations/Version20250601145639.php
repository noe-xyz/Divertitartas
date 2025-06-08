<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601145639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente ADD apellido1 VARCHAR(255) DEFAULT NULL, ADD apellido2 VARCHAR(255) DEFAULT NULL, DROP id_cliente, DROP email, DROP password, DROP apellidos, DROP telefono1, CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE fecha_nacimiento fecha_nacimiento DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa ADD nombre VARCHAR(255) DEFAULT NULL, ADD apellido1 VARCHAR(255) DEFAULT NULL, ADD apellido2 VARCHAR(255) DEFAULT NULL, DROP email, DROP password, DROP nombre_completo, CHANGE nombre_empresa nombre_empresa VARCHAR(255) NOT NULL, CHANGE nif_cif nif_cif VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B8D75A504CCE1B10 ON empresa (nombre_empresa)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B8D75A50B67D4AB7 ON empresa (nif_cif)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE usuario CHANGE telefono1 telefono1 INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente ADD id_cliente INT NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD apellidos VARCHAR(255) NOT NULL, ADD telefono1 INT DEFAULT NULL, DROP apellido1, DROP apellido2, CHANGE nombre nombre VARCHAR(255) NOT NULL, CHANGE fecha_nacimiento fecha_nacimiento DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_B8D75A504CCE1B10 ON empresa
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_B8D75A50B67D4AB7 ON empresa
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa ADD email VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD nombre_completo VARCHAR(255) NOT NULL, DROP nombre, DROP apellido1, DROP apellido2, CHANGE nombre_empresa nombre_empresa VARCHAR(255) DEFAULT NULL, CHANGE nif_cif nif_cif VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE usuario CHANGE telefono1 telefono1 INT DEFAULT NULL
        SQL);
    }
}
