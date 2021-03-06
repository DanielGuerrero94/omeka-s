<?php
namespace Omeka\Db\Migrations;

use Doctrine\DBAL\Connection;
use Omeka\Db\Migration\MigrationInterface;

class SiteAttachmentOnDelete implements MigrationInterface
{
    public function up(Connection $conn)
    {
        $conn->query('ALTER TABLE site_block_attachment DROP FOREIGN KEY FK_236473FE126F525E');
        $conn->query('ALTER TABLE site_block_attachment DROP FOREIGN KEY FK_236473FEEA9FDD75');
        $conn->query('ALTER TABLE site_block_attachment ADD CONSTRAINT FK_236473FE126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $conn->query('ALTER TABLE site_block_attachment ADD CONSTRAINT FK_236473FEEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE SET NULL');
    }
}
