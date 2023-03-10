<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309112733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_address (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone INT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD etat VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D77482E5B');
        $this->addSql('ALTER TABLE commande ADD id_address_id INT DEFAULT NULL, ADD confirme_admin TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D503D2FA2 FOREIGN KEY (id_address_id) REFERENCES billing_address (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D77482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6EEAA67D503D2FA2 ON commande (id_address_id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier ADD id_commande_id INT DEFAULT NULL, ADD prix INT NOT NULL, ADD quantity INT NOT NULL, ADD confirme TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF29AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF29AF8E3A3 ON panier (id_commande_id)');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE7F77D927C');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE77294869C');
        $this->addSql('DROP INDEX IDX_F880CAE7F77D927C ON panier_article');
        $this->addSql('DROP INDEX IDX_F880CAE77294869C ON panier_article');
        $this->addSql('ALTER TABLE panier_article ADD id INT AUTO_INCREMENT NOT NULL, ADD id_panier_id INT DEFAULT NULL, ADD id_article_id INT DEFAULT NULL, ADD quantity INT NOT NULL, DROP panier_id, DROP article_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE777482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE7D71E064B FOREIGN KEY (id_article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_F880CAE777482E5B ON panier_article (id_panier_id)');
        $this->addSql('CREATE INDEX IDX_F880CAE7D71E064B ON panier_article (id_article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D503D2FA2');
        $this->addSql('DROP TABLE billing_address');
        $this->addSql('ALTER TABLE article DROP etat');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D77482E5B');
        $this->addSql('DROP INDEX UNIQ_6EEAA67D503D2FA2 ON commande');
        $this->addSql('ALTER TABLE commande DROP id_address_id, DROP confirme_admin');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D77482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF29AF8E3A3');
        $this->addSql('DROP INDEX UNIQ_24CC0DF29AF8E3A3 ON panier');
        $this->addSql('ALTER TABLE panier DROP id_commande_id, DROP prix, DROP quantity, DROP confirme');
        $this->addSql('ALTER TABLE panier_article MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE777482E5B');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE7D71E064B');
        $this->addSql('DROP INDEX IDX_F880CAE777482E5B ON panier_article');
        $this->addSql('DROP INDEX IDX_F880CAE7D71E064B ON panier_article');
        $this->addSql('DROP INDEX `PRIMARY` ON panier_article');
        $this->addSql('ALTER TABLE panier_article ADD article_id INT NOT NULL, DROP id, DROP id_panier_id, DROP id_article_id, CHANGE quantity panier_id INT NOT NULL');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE7F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE77294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F880CAE7F77D927C ON panier_article (panier_id)');
        $this->addSql('CREATE INDEX IDX_F880CAE77294869C ON panier_article (article_id)');
        $this->addSql('ALTER TABLE panier_article ADD PRIMARY KEY (panier_id, article_id)');
    }
}
