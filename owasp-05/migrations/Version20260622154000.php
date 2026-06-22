<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user, product, customer and customer comment tables for OWASP A05 module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, full_name VARCHAR(120) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price_cents INT NOT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_D34A04AD7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(140) NOT NULL, company VARCHAR(180) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_comment (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1D6A40179395C3F3 (customer_id), INDEX IDX_1D6A4017F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE customer_comment ADD CONSTRAINT FK_1D6A40179395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_comment ADD CONSTRAINT FK_1D6A4017F675F31B FOREIGN KEY (author_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7E3C61F9');
        $this->addSql('ALTER TABLE customer_comment DROP FOREIGN KEY FK_1D6A40179395C3F3');
        $this->addSql('ALTER TABLE customer_comment DROP FOREIGN KEY FK_1D6A4017F675F31B');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_comment');
        $this->addSql('DROP TABLE app_user');
    }
}
