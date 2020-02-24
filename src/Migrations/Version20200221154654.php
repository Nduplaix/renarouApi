<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200221154654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commande_line (id INT AUTO_INCREMENT NOT NULL, reference_id INT DEFAULT NULL, commande_id INT NOT NULL, ref_label VARCHAR(255) NOT NULL, ref_size VARCHAR(255) NOT NULL, ref_discount VARCHAR(255) NOT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, price_with_discount DOUBLE PRECISION NOT NULL, ref_price DOUBLE PRECISION NOT NULL, INDEX IDX_BA78B5151645DEA9 (reference_id), INDEX IDX_BA78B51582EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, product_count INT NOT NULL, total_discount DOUBLE PRECISION NOT NULL, price_with_discount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_line ADD CONSTRAINT FK_BA78B5151645DEA9 FOREIGN KEY (reference_id) REFERENCES reference (id)');
        $this->addSql('ALTER TABLE commande_line ADD CONSTRAINT FK_BA78B51582EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande_line DROP FOREIGN KEY FK_BA78B51582EA2E54');
        $this->addSql('DROP TABLE commande_line');
        $this->addSql('DROP TABLE commande');
    }
}
