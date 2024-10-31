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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\ServiceAnnotation\CronJob;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\NewsArchiveModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Executes news archiving according to each news archive's settings.
 *
 * @Callback(table="tl_news", target="config.onload")
 * @Callback(table="tl_news_archive", target="config.onload")
 *
 * @CronJob("minutely")
 *
 * @Hook("getPageLayout")
 */
class NewsArchiver
{
    // Whether archiving was done already in this process.
    protected static bool $archived = false;

    public function __construct(
        private readonly LoggerInterface $generalLogger,
        private readonly Connection $db,
        private readonly ContaoFramework $contaoFramework,
    ) {
    }

    public function __invoke(): void
    {
        // Check if archiving was done already within this process
        if (self::$archived) {
            return;
        }

        $this->contaoFramework->initialize();

        // Get all news archives where archiving is active
        $archives = NewsArchiveModel::findBy(
            [
                "archiving = '1'",
                'archivingTarget != 0',
                "(archivingTime != '' OR archivingStop = 1)",
            ],
            [],
            ['order' => 'title ASC'],
        );

        // Go through each archive
        foreach ($archives ?? [] as $archive) {
            // Get the target archive
            $target = NewsArchiveModel::findById($archive->archivingTarget);

            if (null === $target) {
                continue;
            }

            $archivingTime = StringUtil::deserialize($archive->archivingTime, true);
            $value = $archivingTime['value'] ?? null;
            $unit = $archivingTime['unit'] ?? null;

            // Move according to time
            if ($value && $unit && ($time = strtotime('-'.$value.' '.$unit))) {
                $result = $this->db->executeQuery('UPDATE tl_news SET pid = ? WHERE pid = ? AND time < ?', [$target->id, $archive->id, $time]);
                $count = $result->rowCount();

                if ($count > 0) {
                    $this->generalLogger->info('Moved '.$count.' news entries from "'.$archive->title.'" to "'.$target->title.'" due to time criteria.');
                }
            }

            // Move according to stop time
            if ($archive->archivingStop) {
                $result = $this->db->executeQuery("UPDATE tl_news SET pid = ?, stop = '' WHERE pid = ? AND stop != '' AND stop < UNIX_TIMESTAMP()", [$target->id, $archive->id]);
                $count = $result->rowCount();

                if ($count > 0) {
                    $this->generalLogger->info('Moved '.$count.' news entries from "'.$archive->title.'" to "'.$target->title.'" due to stop criteria.');
                }
            }
        }

        // Save that archiving was executed
        self::$archived = true;
    }
}
