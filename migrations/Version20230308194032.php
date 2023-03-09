<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308194032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, club_id INT DEFAULT NULL, article_id INT DEFAULT NULL, datereclamation DATE NOT NULL, etat VARCHAR(255) DEFAULT NULL, reponse VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_CE606404A76ED395 (user_id), INDEX IDX_CE6064043C105691 (coach_id), INDEX IDX_CE60640461190A32 (club_id), INDEX IDX_CE6064047294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, coach_id INT DEFAULT NULL, user_id INT DEFAULT NULL, note INT DEFAULT NULL, INDEX IDX_329937513C105691 (coach_id), INDEX IDX_32993751A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064043C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640461190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_329937513C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064043C105691');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640461190A32');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047294869C');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_329937513C105691');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751A76ED395');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE score');
    }
}
