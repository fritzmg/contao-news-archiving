<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_news_archive']['archiving_legend'] = 'Archiving';
$GLOBALS['TL_LANG']['tl_news_archive']['archiving'] = ['Automatic archiving', 'Activates the automatic archiving functionality. The news entries will be moved to the chosen news archive according to the chosen criteria.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTarget'] = ['Target', 'The target news archive.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'] = ['Move expired entries', 'News entries with a set stop time will be moved to the target archive after it has expired. The stop time will be removed in the target archive, so that the news entry is published again.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTime'] = ['Time', 'The time after which a news entry will be moved. This setting is optional - if no time is defined, the news entries will be moved only according to the "'.$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'][0].'" setting, if set.'];
