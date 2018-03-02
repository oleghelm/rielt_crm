<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180302111019 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE param CHANGE use_in_export use_in_export TINYINT(1) DEFAULT NULL, CHANGE basic_param basic_param TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE object_param CHANGE `float` floatnumber DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object_param CHANGE floatnumber `float` DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE param CHANGE use_in_export use_in_export TINYINT(1) NOT NULL, CHANGE basic_param basic_param TINYINT(1) NOT NULL');
    }
}
