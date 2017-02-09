<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  inspiredminds.at 2017
 * @author     Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @package    news_archiving
 */


$GLOBALS['TL_LANG']['tl_news_archive']['archiving_legend'] = 'Archiving';
$GLOBALS['TL_LANG']['tl_news_archive']['archiving'] = array('Automatic archiving','Activates the automatic archiving functionality. The news entries will be moved to the chosen news archive according to the chosen criteria.');
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTarget'] = array('Target','The target news archive.');
$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'] = array('Move expired entries','News entries with a set stop time will be moved to the target archive after it has expired. The stop time will be removed in the target archive, so that the news entry is published again.');
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTime'] = array('Time','The time after which a news entry will be moved. This setting is optional - if no time is defined, the news entries will be moved only according to the "'.$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'][0].'" setting, if set.');
