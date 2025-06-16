<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616194906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE producto CHANGE sabor sabor JSON DEFAULT NULL COMMENT '(DC2Type:json)', CHANGE relleno relleno JSON DEFAULT NULL COMMENT '(DC2Type:json)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE valoraciones CHANGE estrellas estrellas INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE producto CHANGE sabor sabor VARCHAR(30) DEFAULT NULL, CHANGE relleno relleno VARCHAR(30) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE valoraciones CHANGE estrellas estrellas INT NOT NULL
        SQL);
    }
}
