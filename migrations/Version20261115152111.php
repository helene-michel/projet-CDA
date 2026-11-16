<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261115152111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE platform_setting (id INT AUTO_INCREMENT NOT NULL, commission_rate DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE order_line ADD commission_rate DOUBLE PRECISION NOT NULL, ADD seller_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE18DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9CE58EE18DE820D9 ON order_line (seller_id)');
        // Existing orders: seller from the product, default 10 % rate
        $this->addSql('UPDATE order_line l JOIN product p ON p.id = l.product_id SET l.seller_id = p.user_id');
        $this->addSql('UPDATE order_line SET commission_rate = 10');
        $this->addSql('INSERT INTO platform_setting (commission_rate) VALUES (10)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE platform_setting');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE18DE820D9');
        $this->addSql('DROP INDEX IDX_9CE58EE18DE820D9 ON order_line');
        $this->addSql('ALTER TABLE order_line DROP commission_rate, DROP seller_id');
    }
}
