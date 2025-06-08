<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602222849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trabajador CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trabajador ADD CONSTRAINT FK_42157CDFBF396750 FOREIGN KEY (id) REFERENCES usuario (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trabajador DROP FOREIGN KEY FK_42157CDFBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trabajador CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
    }
}
