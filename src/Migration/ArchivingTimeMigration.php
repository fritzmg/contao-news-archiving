<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) INSPIRED MINDS
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoNewsArchiving\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Migrates tl_news_archive.archivingTime to the new format.
 */
class ArchivingTimeMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = method_exists($this->db, 'createSchemaManager') ? $this->db->createSchemaManager() : $this->db->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_news_archive'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_news_archive');

        if (!isset($columns['archivingtime']) || $columns['archivingtime']->getLength() < 255) {
            return false;
        }

        return (int) $this->db->fetchOne("SELECT COUNT(*) FROM tl_news_archive WHERE archivingTime != '' AND archivingTime NOT LIKE 'a:2:{%'") > 0;
    }

    public function run(): MigrationResult
    {
        $records = $this->db->fetchAllAssociative("SELECT id, archivingTime FROM tl_news_archive WHERE archivingTime != '' AND archivingTime NOT LIKE 'a:2:{%'");

        foreach ($records as $record) {
            [$value, $unit] = explode(' ', (string) $record['archivingTime']) + [null, null];
            $this->db->update('tl_news_archive', ['archivingTime' => serialize(['value' => $value, 'unit' => $unit.'s'])], ['id' => $record['id']]);
        }

        return $this->createResult(true);
    }
}
