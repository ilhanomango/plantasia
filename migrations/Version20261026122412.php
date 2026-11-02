<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026122412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_plante (id INT AUTO_INCREMENT NOT NULL, dernier_arrosage DATETIME DEFAULT NULL, surnom VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, plante_id INT NOT NULL, INDEX IDX_3428727AA76ED395 (user_id), INDEX IDX_3428727A177B16E8 (plante_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_plante ADD CONSTRAINT FK_3428727AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_plante ADD CONSTRAINT FK_3428727A177B16E8 FOREIGN KEY (plante_id) REFERENCES plantes (id)');
        $this->addSql('ALTER TABLE plantes DROP dernier_arrosage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_plante DROP FOREIGN KEY FK_3428727AA76ED395');
        $this->addSql('ALTER TABLE user_plante DROP FOREIGN KEY FK_3428727A177B16E8');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_plante');
        $this->addSql('ALTER TABLE plantes ADD dernier_arrosage INT NOT NULL');
    }
}
