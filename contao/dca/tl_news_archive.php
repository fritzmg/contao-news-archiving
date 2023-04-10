<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archiving'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['archiving'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTarget'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingTarget'],
    'exclude' => true,
    'inputType' => 'radio',
    'foreignKey' => 'tl_news_archive.title',
    'eval' => ['mandatory' => true],
    'sql' => "int(10) unsigned NOT NULL default '0'",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTime'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingTime'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['', '1 day', '1 week', '1 month', '1 year'],
    'reference' => &$GLOBALS['TL_LANG']['tl_news_archive']['times'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingStop'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['__selector__'][] = 'archiving';
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['archiving'] = 'archivingTarget,archivingTime,archivingStop';

PaletteManipulator::create()
    ->addLegend('archiving_legend', null, PaletteManipulator::POSITION_AFTER, true)
    ->addField('archiving')
    ->applyToPalette('default', 'tl_news_archive')
;
