<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_news_archive']['archiving_legend'] = 'Archivierung';
$GLOBALS['TL_LANG']['tl_news_archive']['archiving'] = ['Automatische Archivierung', 'Aktiviert die automatische Archivierung für dieses Nachrichtenarchiv. Die Nachrichtenbeiträge werden anhand der eingestellten Kriterien in das ausgewählte Nachrichtenarchiv verschoben.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTarget'] = ['Ziel', 'Das Ziel-Nachrichtenarchiv für die automatische Archivierung.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'] = ['Abgelaufene Einträge verschieben', 'Nachrichtenbeiträge wo eine Stop-Zeit gesetzt wurde, werden nach Ablauf automatisch in das Ziel-Archiv verschoben. Die Stop-Zeit wird im neuen Archiv entfernt, sodass der Eintrag wieder veröffentlich ist.'];
$GLOBALS['TL_LANG']['tl_news_archive']['archivingTime'] = ['Zeit', 'Die Zeit nach der die Nachrichtenbeiträge automatisch verschoben werden. Falls keine Zeit gesetzt ist, werden die Einträge anhand der "'.$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'][0].'" Einstellung verschoben, falls aktiviert.'];
$GLOBALS['TL_LANG']['tl_news_archive']['times'] = [
    '1 day' => '1 Tag',
    '1 month' => '1 Monat',
    '1 week' => '1 Woche',
    '1 year' => '1 Jahr',
];
