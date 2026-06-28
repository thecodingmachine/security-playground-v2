<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user, order, invoice and report tables for OWASP A10 module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, full_name VARCHAR(120) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sales_order (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, reference VARCHAR(40) NOT NULL, status VARCHAR(20) NOT NULL, amount_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', confirmed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_C714503AAEA34913 (reference), INDEX IDX_C714503A7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, issued_to_id INT NOT NULL, sales_order_id INT NOT NULL, invoice_number VARCHAR(40) NOT NULL, total_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_9065174498B9F4CB (invoice_number), INDEX IDX_90651744A1633427 (issued_to_id), INDEX IDX_906517442021B2A2 (sales_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, title VARCHAR(180) NOT NULL, storage_path VARCHAR(180) NOT NULL, is_sensitive TINYINT(1) NOT NULL, is_broken TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C42C72A77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sales_order ADD CONSTRAINT FK_C714503A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A1633427 FOREIGN KEY (issued_to_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517442021B2A2 FOREIGN KEY (sales_order_id) REFERENCES sales_order (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42C72A77E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sales_order DROP FOREIGN KEY FK_C714503A7E3C61F9');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A1633427');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517442021B2A2');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42C72A77E3C61F9');
        $this->addSql('DROP TABLE sales_order');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE app_user');
    }
}
