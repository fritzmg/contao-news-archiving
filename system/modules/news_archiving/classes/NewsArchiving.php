<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  inspiredminds.at 2017
 * @author     Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @package    news_archiving
 */

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\NewsArchiveModel;
use Contao\System;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class NewsArchiving
{
    /**
     * Whether archiving was done already in this process.
     * @var boolean
     */
    protected static $blnArchived = false;

    /**
     * Moves news entries from one news archive to another according to certain criterias.
     *
     * @return void
     */
    public function archiveNews()
    {
        // check if archiving was done already within this process
        if( self::$blnArchived )
        {
            return;
        }

        // get the Database object
        $db = Database::getInstance();

        // get all news archives where archiving is active
        $objArchives = $db->execute("SELECT id, title, archivingTarget, archivingTime, archivingStop 
                                       FROM tl_news_archive 
                                      WHERE archiving = '1' 
                                        AND archivingTarget != '0' 
                                        AND (archivingTime != '' OR archivingStop = '1') 
                                      ORDER BY title ASC");

        // go through each archive
        while( $objArchives->next() )
        {
            // get the target
            $objTarget = NewsArchiveModel::findById( $objArchives->archivingTarget );

            // check for valid target
            if( !$objTarget )
            {
                continue;
            }

            // move according to time
            if( $objArchives->archivingTime )
            {
                // get the time
                if( ( $time = strtotime('-'.$objArchives->archivingTime) ) )
                {
                    // move the news entries
                    $result = $db->prepare("UPDATE tl_news SET pid = ? WHERE pid = ? AND time < ?")->execute( $objTarget->id, $objArchives->id, $time );

                    // log
                    if( $result->affectedRows > 0 )
                    {
                        $this->log('Moved '.$result->affectedRows.' news entries from "'.$objArchives->title.'" to "'.$objTarget->title.'" due to time criteria.', __METHOD__);
                    }
                }
            }

            // move according to stop time
            if( $objArchives->archivingStop )
            {
                // move the news entries
                $result = $db->prepare("UPDATE tl_news SET pid = ?, stop = '' WHERE pid = ? AND stop != '' AND stop < UNIX_TIMESTAMP()")->execute( $objTarget->id, $objArchives->id );

                // log
                if( $result->affectedRows > 0 )
                {
                    $this->log('Moved '.$result->affectedRows.' news entries from "'.$objArchives->title.'" to "'.$objTarget->title.'" due to stop criteria.', __METHOD__);
                }
            }
        }

        // set to archived
        self::$blnArchived = true;
    }

    private function log(string $message, string $method): void
    {
        if (method_exists(System::class, 'log')) {
            System::log($message, $method, TL_GENERAL);
        } else {
            $context = new ContaoContext($method, ContaoContext::GENERAL);

            try {
                $logger = System::getContainer()->get('monolog.logger.contao.general');
            } catch (ServiceNotFoundException $e) {
                $logger = System::getContainer()->get('logger');
            }

            /** @var LoggerInterface $logger */
            $logger->info($message, ['contao' => $context]); 
        }
    }
}
