<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261027091735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_plante ADD frequence_arrosage INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_plante ADD CONSTRAINT FK_3428727AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_plante ADD CONSTRAINT FK_3428727A177B16E8 FOREIGN KEY (plante_id) REFERENCES plantes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_plante_note ADD CONSTRAINT FK_6DB8FED9179B9B0C FOREIGN KEY (user_plante_id) REFERENCES user_plante (id)');
        $this->addSql('ALTER TABLE user_plante_photo ADD CONSTRAINT FK_F1C03F11179B9B0C FOREIGN KEY (user_plante_id) REFERENCES user_plante (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_plante DROP FOREIGN KEY FK_3428727AA76ED395');
        $this->addSql('ALTER TABLE user_plante DROP FOREIGN KEY FK_3428727A177B16E8');
        $this->addSql('ALTER TABLE user_plante DROP frequence_arrosage');
        $this->addSql('ALTER TABLE user_plante_note DROP FOREIGN KEY FK_6DB8FED9179B9B0C');
        $this->addSql('ALTER TABLE user_plante_photo DROP FOREIGN KEY FK_F1C03F11179B9B0C');
    }
}
