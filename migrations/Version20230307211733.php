<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307211733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, prix INT NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE club (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, id_panier_id INT DEFAULT NULL, montant INT NOT NULL, date DATETIME NOT NULL, UNIQUE INDEX UNIQ_6EEAA67D77482E5B (id_panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, auteur VARCHAR(10) NOT NULL, contenu VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_67F068BC7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_article (panier_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_F880CAE7F77D927C (panier_id), INDEX IDX_F880CAE77294869C (article_id), PRIMARY KEY(panier_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, id_club_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, INDEX IDX_AB55E24FBF84A342 (id_club_id), INDEX IDX_AB55E24F79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, seance_id INT DEFAULT NULL, date DATE NOT NULL, INDEX IDX_42C8495579F37AE5 (id_user_id), INDEX IDX_42C84955E3797A94 (seance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, nbr_grp INT NOT NULL, description LONGTEXT NOT NULL, nbr_seance INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, certificates VARCHAR(255) DEFAULT NULL, specialite VARCHAR(255) DEFAULT NULL, experiance VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, is_coach TINYINT(1) NOT NULL, approved TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_article (user_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_5A37106CA76ED395 (user_id), INDEX IDX_5A37106C7294869C (article_id), PRIMARY KEY(user_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D77482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE7F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_article ADD CONSTRAINT FK_F880CAE77294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FBF84A342 FOREIGN KEY (id_club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE user_article ADD CONSTRAINT FK_5A37106CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_article ADD CONSTRAINT FK_5A37106C7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D77482E5B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE7F77D927C');
        $this->addSql('ALTER TABLE panier_article DROP FOREIGN KEY FK_F880CAE77294869C');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FBF84A342');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F79F37AE5');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495579F37AE5');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E3797A94');
        $this->addSql('ALTER TABLE user_article DROP FOREIGN KEY FK_5A37106CA76ED395');
        $this->addSql('ALTER TABLE user_article DROP FOREIGN KEY FK_5A37106C7294869C');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_article');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_article');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
