<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261109211735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'English, singular entity names';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price_excl_vat DOUBLE PRECISION NOT NULL, vat_rate DOUBLE PRECISION NOT NULL, images JSON NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04ADA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E3A6E39C4584665A (product_id), INDEX IDX_E3A6E39CBAD26311 (tag_id), PRIMARY KEY (product_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_color (product_id INT NOT NULL, color_id INT NOT NULL, INDEX IDX_C70A33B54584665A (product_id), INDEX IDX_C70A33B57ADA1FB5 (color_id), PRIMARY KEY (product_id, color_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        // Keep existing data
        $this->addSql('INSERT INTO address (id, street, zip_code, type, user_id) SELECT id, adresse, cp, type, user_id FROM adress');
        $this->addSql('INSERT INTO tag (id, name) SELECT id, nom FROM tags');
        $this->addSql('INSERT INTO product (id, name, description, price_excl_vat, vat_rate, images, category_id, user_id) SELECT id, nom, description, prix_ht, tva, image, category_id, user_id FROM products');
        $this->addSql('INSERT INTO product_tag (product_id, tag_id) SELECT products_id, tags_id FROM products_tags');
        $this->addSql('INSERT INTO product_color (product_id, color_id) SELECT products_id, color_id FROM products_color');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_color ADD CONSTRAINT FK_C70A33B54584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_color ADD CONSTRAINT FK_C70A33B57ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adress DROP FOREIGN KEY `FK_5CECC7BEA76ED395`');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY `FK_B3BA5A5A12469DE2`');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY `FK_B3BA5A5AA76ED395`');
        $this->addSql('ALTER TABLE products_color DROP FOREIGN KEY `FK_860FB6396C8A81A9`');
        $this->addSql('ALTER TABLE products_color DROP FOREIGN KEY `FK_860FB6397ADA1FB5`');
        $this->addSql('ALTER TABLE products_tags DROP FOREIGN KEY `FK_E3AB5A2C6C8A81A9`');
        $this->addSql('ALTER TABLE products_tags DROP FOREIGN KEY `FK_E3AB5A2C8D7B4FB4`');
        $this->addSql('DROP TABLE adress');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_color');
        $this->addSql('DROP TABLE products_tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('ALTER TABLE category CHANGE nom name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_group CHANGE nom name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE color CHANGE nom name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE date_commande ordered_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE seller CHANGE raison_social company_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE nom last_name VARCHAR(255) DEFAULT NULL, CHANGE prenom first_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adress (id INT AUTO_INCREMENT NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, cp VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, user_id INT DEFAULT NULL, INDEX IDX_5CECC7BEA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, prix_ht DOUBLE PRECISION NOT NULL, tva DOUBLE PRECISION NOT NULL, image JSON NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_B3BA5A5AA76ED395 (user_id), INDEX IDX_B3BA5A5A12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products_color (products_id INT NOT NULL, color_id INT NOT NULL, INDEX IDX_860FB6396C8A81A9 (products_id), INDEX IDX_860FB6397ADA1FB5 (color_id), PRIMARY KEY (products_id, color_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products_tags (products_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_E3AB5A2C6C8A81A9 (products_id), INDEX IDX_E3AB5A2C8D7B4FB4 (tags_id), PRIMARY KEY (products_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE adress ADD CONSTRAINT `FK_5CECC7BEA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT `FK_B3BA5A5A12469DE2` FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT `FK_B3BA5A5AA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE products_color ADD CONSTRAINT `FK_860FB6396C8A81A9` FOREIGN KEY (products_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_color ADD CONSTRAINT `FK_860FB6397ADA1FB5` FOREIGN KEY (color_id) REFERENCES color (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_tags ADD CONSTRAINT `FK_E3AB5A2C6C8A81A9` FOREIGN KEY (products_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_tags ADD CONSTRAINT `FK_E3AB5A2C8D7B4FB4` FOREIGN KEY (tags_id) REFERENCES tags (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA76ED395');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39C4584665A');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39CBAD26311');
        $this->addSql('ALTER TABLE product_color DROP FOREIGN KEY FK_C70A33B54584665A');
        $this->addSql('ALTER TABLE product_color DROP FOREIGN KEY FK_C70A33B57ADA1FB5');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_tag');
        $this->addSql('DROP TABLE product_color');
        $this->addSql('DROP TABLE tag');
        $this->addSql('ALTER TABLE category CHANGE name nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_group CHANGE name nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE color CHANGE name nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE ordered_at date_commande DATETIME NOT NULL');
        $this->addSql('ALTER TABLE seller CHANGE company_name raison_social VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, DROP last_name, DROP first_name');
    }
}
