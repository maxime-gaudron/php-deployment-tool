<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140921142138 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Deployment DROP FOREIGN KEY FK_A44F566E1844E6B7');
        $this->addSql('ALTER TABLE Deployment ADD CONSTRAINT FK_A44F566E1844E6B7 FOREIGN KEY (server_id) REFERENCES Server (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Deployment DROP FOREIGN KEY FK_A44F566E1844E6B7');
        $this->addSql('ALTER TABLE Deployment ADD CONSTRAINT FK_A44F566E1844E6B7 FOREIGN KEY (server_id) REFERENCES Server (id)');
    }
}
