<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180302102117 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE price_uah price_uah DOUBLE PRECISION DEFAULT NULL, CHANGE price_m2 price_m2 DOUBLE PRECISION DEFAULT NULL, CHANGE price_m2_uah price_m2_uah DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE object CHANGE price price INT NOT NULL, CHANGE price_uah price_uah INT DEFAULT NULL, CHANGE price_m2 price_m2 INT DEFAULT NULL, CHANGE price_m2_uah price_m2_uah INT DEFAULT NULL');
    }
}
