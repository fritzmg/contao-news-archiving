<?php

declare(strict_types=1);

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) INSPIRED MINDS
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * This file is part of the Contao News Archiving extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archiving'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTarget'] = [
    'exclude' => true,
    'inputType' => 'radio',
    'foreignKey' => 'tl_news_archive.title',
    'eval' => ['mandatory' => true],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTime'] = [
    'exclude' => true,
    'inputType' => 'inputUnit',
    'options' => ['hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_news_archive']['times'],
    'eval' => ['tl_class' => 'w50', 'rgxp' => 'natural'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingStop'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['deletion'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['deletionTime'] = [
    'exclude' => true,
    'inputType' => 'inputUnit',
    'options' => ['days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_news_archive']['times'],
    'eval' => ['tl_class' => 'w50', 'rgxp' => 'natural'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['deletionStop'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['__selector__'][] = 'archiving';
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['__selector__'][] = 'deletion';
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['archiving'] = 'archivingTarget,archivingTime,archivingStop';
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['deletion'] = 'deletionTime,deletionStop';

PaletteManipulator::create()
    ->addLegend('archiving_legend', null, PaletteManipulator::POSITION_AFTER, true)
    ->addField('archiving', 'archiving_legend', PaletteManipulator::POSITION_APPEND)
    ->addLegend('deletion_legend', null, PaletteManipulator::POSITION_AFTER, true)
    ->addField('deletion', 'deletion_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_news_archive')
;
