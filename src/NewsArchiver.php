<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspireMinds\ContaoNewsArchiving;

use Contao\CoreBundle\Cron\CronJob;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\NewsArchiveModel;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Executes news archivin according to each news archive's settings.
 *
 * @Callback(table="tl_news", target="config.onload")
 * @Callback(table="tl_news_archive", target="config.onload")
 * @CronJob("minutely")
 * @Hook("getPageLayout")
 */
class NewsArchiver
{
    // Whether archiving was done already in this process.
    protected static bool $archived = false;

    private LoggerInterface $generalLogger;
    private Connection $db;

    public function __construct(LoggerInterface $generalLogger, Connection $db)
    {
        $this->generalLogger = $generalLogger;
        $this->db = $db;
    }

    public function __invoke(): void
    {
        // Check if archiving was done already within this process
        if (self::$archived) {
            return;
        }

        // Get all news archives where archiving is active
        $archives = NewsArchiveModel::findBy(
            [
                "archiving = '1'",
                'archivingTarget != 0',
                "(archivingTime != '' OR archivingStop = 1)",
            ],
            [],
            ['order' => 'title ASC']
        );

        // Go through each archive
        foreach ($archives as $archive) {
            // Get the target archive
            $target = NewsArchiveModel::findById($archive->archivingTarget);

            if (null === $target) {
                continue;
            }

            // Move according to time
            if ($archive->archivingTime && ($time = strtotime('-'.$archive->archivingTime))) {
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
