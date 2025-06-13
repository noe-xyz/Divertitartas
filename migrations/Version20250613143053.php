<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613143053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido CHANGE id_producto producto_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido ADD CONSTRAINT FK_DBD868FC7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DBD868FC7645698E ON detalles_pedido (producto_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido DROP FOREIGN KEY FK_DBD868FC7645698E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DBD868FC7645698E ON detalles_pedido
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detalles_pedido CHANGE producto_id id_producto INT NOT NULL
        SQL);
    }
}
