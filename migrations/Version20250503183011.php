<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503183011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id SERIAL NOT NULL, user_id_id INT NOT NULL, product_id_id INT NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13B9D86650F ON purchase (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13BDE18E50B ON purchase (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rental (id SERIAL NOT NULL, user_id_id INT NOT NULL, product_id_id INT NOT NULL, start_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount DOUBLE PRECISION NOT NULL, buyout_cost DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1619C27D9D86650F ON rental (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1619C27DDE18E50B ON rental (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT FK_1619C27D9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT FK_1619C27DDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP CONSTRAINT FK_6117D13BDE18E50B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP CONSTRAINT FK_1619C27D9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP CONSTRAINT FK_1619C27DDE18E50B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rental
        SQL);
    }
}
