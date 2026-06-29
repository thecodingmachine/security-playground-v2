<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for OWASP A08 software and data integrity failure module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, full_name VARCHAR(120) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, reference VARCHAR(40) NOT NULL, status VARCHAR(20) NOT NULL, amount_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_F52993988AEA3491 (reference), INDEX IDX_F52993989395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, invoice_number VARCHAR(60) NOT NULL, amount_cents INT NOT NULL, currency VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_9065174498CE3F7D (invoice_number), INDEX IDX_90651748D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_history (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, event_id VARCHAR(80) NOT NULL, event_type VARCHAR(80) NOT NULL, payload_snapshot LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D855A3D38D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE internal_notification (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, message VARCHAR(140) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CDB95BAC8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loyalty_credit (id INT AUTO_INCREMENT NOT NULL, beneficiary_id INT NOT NULL, order_id INT NOT NULL, points INT NOT NULL, reason VARCHAR(140) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BF4A31AA76ED395 (beneficiary_id), INDEX IDX_3BF4A31A8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE processed_webhook_event (id INT AUTO_INCREMENT NOT NULL, event_id VARCHAR(80) NOT NULL, processed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_14C5CB2D71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog_product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(40) NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price_cents INT NOT NULL, discount_percent INT NOT NULL, is_public TINYINT(1) NOT NULL, is_featured TINYINT(1) NOT NULL, status VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_BA8A72108AEA3491 (sku), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog_import (id INT AUTO_INCREMENT NOT NULL, imported_by_id INT NOT NULL, filename VARCHAR(255) NOT NULL, checksum VARCHAR(64) NOT NULL, row_count INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3698E5CEB85B3F4B (imported_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651748D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE payment_history ADD CONSTRAINT FK_D855A3D38D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE internal_notification ADD CONSTRAINT FK_CDB95BAC8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE loyalty_credit ADD CONSTRAINT FK_3BF4A31AA76ED395 FOREIGN KEY (beneficiary_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE loyalty_credit ADD CONSTRAINT FK_3BF4A31A8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE catalog_import ADD CONSTRAINT FK_3698E5CEB85B3F4B FOREIGN KEY (imported_by_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651748D9F6D38');
        $this->addSql('ALTER TABLE payment_history DROP FOREIGN KEY FK_D855A3D38D9F6D38');
        $this->addSql('ALTER TABLE internal_notification DROP FOREIGN KEY FK_CDB95BAC8D9F6D38');
        $this->addSql('ALTER TABLE loyalty_credit DROP FOREIGN KEY FK_3BF4A31AA76ED395');
        $this->addSql('ALTER TABLE loyalty_credit DROP FOREIGN KEY FK_3BF4A31A8D9F6D38');
        $this->addSql('ALTER TABLE catalog_import DROP FOREIGN KEY FK_3698E5CEB85B3F4B');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE payment_history');
        $this->addSql('DROP TABLE internal_notification');
        $this->addSql('DROP TABLE loyalty_credit');
        $this->addSql('DROP TABLE processed_webhook_event');
        $this->addSql('DROP TABLE catalog_product');
        $this->addSql('DROP TABLE catalog_import');
        $this->addSql('DROP TABLE app_user');
    }
}
