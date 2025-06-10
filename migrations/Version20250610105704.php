<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610105704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido CHANGE id_pedido pedido_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido ADD CONSTRAINT FK_DBD868FC4854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DBD868FC4854653A ON detalles_pedido (pedido_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido DROP FOREIGN KEY FK_DBD868FC4854653A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DBD868FC4854653A ON detalles_pedido
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido CHANGE pedido_id id_pedido INT NOT NULL
        SQL);
    }
}
