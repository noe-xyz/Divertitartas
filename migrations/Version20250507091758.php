<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507091758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25BF396750 FOREIGN KEY (id) REFERENCES usuario (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa ADD CONSTRAINT FK_B8D75A50BF396750 FOREIGN KEY (id) REFERENCES usuario (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente DROP FOREIGN KEY FK_F41C9B25BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa DROP FOREIGN KEY FK_B8D75A50BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cliente CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE empresa CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
    }
}
