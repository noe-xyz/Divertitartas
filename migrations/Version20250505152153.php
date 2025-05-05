<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505152153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente ADD apellido2 VARCHAR(255) DEFAULT NULL, CHANGE apellidos apellido1 VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa ADD nombre VARCHAR(255) DEFAULT NULL, ADD apellido1 VARCHAR(255) DEFAULT NULL, ADD apellido2 VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente ADD apellidos VARCHAR(255) DEFAULT NULL, DROP apellido1, DROP apellido2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa DROP nombre, DROP apellido1, DROP apellido2
        SQL);
    }
}
