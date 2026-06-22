<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user, sensitive note, password reset token and integration secret tables for OWASP A04 module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, full_name VARCHAR(120) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensitive_note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(140) NOT NULL, encoded_value LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6A4B7EED9A76ED39 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6A1ED789A76ED39 (user_id), INDEX IDX_B6A1ED785F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE integration_secret (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(140) NOT NULL, encrypted_value LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C6FF1FD89A76ED39 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sensitive_note ADD CONSTRAINT FK_6A4B7EED9A76ED39 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_B6A1ED789A76ED39 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE integration_secret ADD CONSTRAINT FK_C6FF1FD89A76ED39 FOREIGN KEY (user_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sensitive_note DROP FOREIGN KEY FK_6A4B7EED9A76ED39');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_B6A1ED789A76ED39');
        $this->addSql('ALTER TABLE integration_secret DROP FOREIGN KEY FK_C6FF1FD89A76ED39');
        $this->addSql('DROP TABLE sensitive_note');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE integration_secret');
        $this->addSql('DROP TABLE app_user');
    }
}
