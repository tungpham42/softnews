<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261007074300 extends AbstractMigration
{
    public function getDescription(): string { return 'Create Newsroom CMS tables'; }
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, display_name VARCHAR(120) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, UNIQUE INDEX UNIQ_64C19C1B5E237E06 (name), UNIQUE INDEX UNIQ_64C19C8982B7E63 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), UNIQUE INDEX UNIQ_389B7838982B7E63 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(200) NOT NULL, excerpt LONGTEXT NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, cover_image VARCHAR(255) DEFAULT NULL, views INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', published_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_5A8A6C8B989D9B62 (slug), INDEX IDX_5A8A6C8B12469DE2 (category_id), INDEX IDX_5A8A6C8B60BB8D92 (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE post_tags (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_622FAD2AA5A8C8 (post_id), INDEX IDX_622FAD2ABAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_name VARCHAR(120) NOT NULL, author_email VARCHAR(180) NOT NULL, body LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8B60BB8D92 FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE RESTRICT");
        $this->addSql("ALTER TABLE post_tags ADD CONSTRAINT FK_622FAD2AA5A8C8 FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post_tags ADD CONSTRAINT FK_622FAD2ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
    }
    public function down(Schema $schema): void { $this->addSql('DROP TABLE comment'); $this->addSql('DROP TABLE post_tags'); $this->addSql('DROP TABLE post'); $this->addSql('DROP TABLE tag'); $this->addSql('DROP TABLE category'); $this->addSql('DROP TABLE users'); }
}
