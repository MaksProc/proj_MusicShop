<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503174010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE rental_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE transaction_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP CONSTRAINT fk_1619c27dde18e50b
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP CONSTRAINT fk_1619c27d9d86650f
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP CONSTRAINT fk_723705d1de18e50b
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP CONSTRAINT fk_723705d1e4af10b8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rental
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transaction
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE rental_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rental (id SERIAL NOT NULL, product_id_id INT NOT NULL, user_id_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(255) NOT NULL, calculated_price DOUBLE PRECISION NOT NULL, buyout_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_1619c27d9d86650f ON rental (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_1619c27dde18e50b ON rental (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transaction (id SERIAL NOT NULL, product_id_id INT NOT NULL, rental_id_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, transaction_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_723705d1e4af10b8 ON transaction (rental_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_723705d1de18e50b ON transaction (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT fk_1619c27dde18e50b FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT fk_1619c27d9d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT fk_723705d1de18e50b FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT fk_723705d1e4af10b8 FOREIGN KEY (rental_id_id) REFERENCES rental (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
