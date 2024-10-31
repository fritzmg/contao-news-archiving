<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) INSPIRED MINDS
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoNewsArchiving;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;
use Contao\NewsArchiveModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Executes news deletion according to each news archive's settings.
 *
 * @CronJob("daily")
 */
class NewsDeleter
{
    public function __construct(
        private readonly LoggerInterface $generalLogger,
        private readonly Connection $db,
        private readonly ContaoFramework $contaoFramework,
    ) {
    }

    public function __invoke(): void
    {
        $this->contaoFramework->initialize();

        // Get all news archives where deletion is active
        $archives = NewsArchiveModel::findBy(
            [
                "deletion = '1'",
                "(deletionTime != '' OR deletionStop = 1)",
            ],
            [],
            ['order' => 'title ASC'],
        );

        Controller::loadDataContainer('tl_news');

        // Go through each archive
        foreach ($archives ?? [] as $archive) {
            $deletionTime = StringUtil::deserialize($archive->deletionTime, true);
            $value = $deletionTime['value'] ?? null;
            $unit = $deletionTime['unit'] ?? null;

            // Delete according to time
            if ($value && $unit && ($time = strtotime('-'.$value.' '.$unit))) {
                $records = $this->db->fetchFirstColumn('SELECT id FROM tl_news WHERE pid = ? AND time < ?', [$archive->id, $time]);

                foreach ($records as $record) {
                    $this->deleteNews((int) $record);
                }

                if ([] !== $records) {
                    $this->generalLogger->info('Deleted '.\count($records).' news entries from "'.$archive->title.'" due to time criteria.');
                }
            }

            // Delete according to stop time
            if ($archive->deletionStop) {
                $records = $this->db->fetchFirstColumn("SELECT id FROM tl_news WHERE pid = ? AND stop != '' AND stop < UNIX_TIMESTAMP()", [$archive->id]);

                foreach ($records as $record) {
                    $this->deleteNews((int) $record);
                }

                if ([] !== $records) {
                    $this->generalLogger->info('Deleted '.\count($records).' news entries from "'.$archive->title.'" due to stop criteria.');
                }
            }
        }
    }

    private function deleteNews(int $newsId): void
    {
        $this->deleteChildren('tl_news', $newsId);
        $this->db->delete('tl_news', ['id' => $newsId]);
    }

    private function deleteChildren(string $ptable, int $pid): void
    {
        Controller::loadDataContainer($ptable);

        foreach ($GLOBALS['TL_DCA'][$ptable]['config']['ctable'] ?? [] as $ctable) {
            Controller::loadDataContainer($ctable);

            $records = [];

            if ($GLOBALS['TL_DCA'][$ctable]['config']['dynamicPtable'] ?? false) {
                $records = $this->db->fetchFirstColumn("SELECT * FROM $ctable WHERE ptable = ? AND pid = ?", [$ptable, $pid]);
            } else {
                $records = $this->db->fetchFirstColumn("SELECT * FROM $ctable WHERE pid = ?", [$pid]);
            }

            foreach ($records as $record) {
                $this->deleteChildren($ctable, (int) $record);
                $this->db->delete($ctable, ['id' => $record['id']]);
            }
        }
    }
}
