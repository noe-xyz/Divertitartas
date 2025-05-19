<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519224252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE descuento (id INT AUTO_INCREMENT NOT NULL, codigo_descuento VARCHAR(10) NOT NULL, nombre VARCHAR(30) NOT NULL, cantidad_descontada DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE detalles_pedido (id INT AUTO_INCREMENT NOT NULL, id_producto INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, cantidad INT NOT NULL, id_pedido INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pedido (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, estado VARCHAR(20) NOT NULL, coste_total DOUBLE PRECISION NOT NULL, id_cliente INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, precio DOUBLE PRECISION NOT NULL, sabor VARCHAR(30) DEFAULT NULL, relleno VARCHAR(30) DEFAULT NULL, raciones INT DEFAULT NULL, descripcion VARCHAR(255) NOT NULL, comentario VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE proveedores (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, telefono INT NOT NULL, correo VARCHAR(40) NOT NULL, direccion VARCHAR(50) NOT NULL, notas VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, id_producto INT NOT NULL, cantidad INT NOT NULL, agotado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trabajador (id INT AUTO_INCREMENT NOT NULL, turno VARCHAR(40) NOT NULL, puesto VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE descuento
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE detalles_pedido
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pedido
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE proveedores
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stock
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trabajador
        SQL);
    }
}
