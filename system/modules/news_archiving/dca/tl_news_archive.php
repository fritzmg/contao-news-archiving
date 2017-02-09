<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  inspiredminds.at 2017
 * @author     Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @package    news_archiving
 */


$GLOBALS['TL_DCA']['tl_news_archive']['config']['onload_callback'][] = array('NewsArchiving','archiveNews');
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] .= ';{archiving_legend:hide},archiving';
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['__selector__'][] = 'archiving';
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['archiving'] = 'archivingTarget,archivingTime,archivingStop';

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archiving'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['archiving'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array('submitOnChange'=>true),
    'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTarget'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingTarget'],
    'exclude'          => true,
    'inputType'        => 'radio',
    'options_callback' => array('tl_news_archiving', 'getNewsArchives'),
    'eval'             => array('mandatory'=>true),
    'sql'              => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingTime'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingTime'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array('', '1 day', '1 week', '1 month', '1 year'),
    'reference' => &$GLOBALS['TL_LANG']['tl_news_archive']['times'],
    'eval'      => array('tl_class'=>'w50'),
    'sql'       => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['archivingStop'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['archivingStop'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array('tl_class'=>'w50 m12'),
    'sql'       => "char(1) NOT NULL default ''"
);


class tl_news_archiving
{
    /**
     * Get all news archives and return them as array
     *
     * @return array
     */
    public function getNewsArchives()
    {
        $arrArchives = array();
        $objArchives = \Database::getInstance()->execute("SELECT id, title FROM tl_news_archive ORDER BY title");

        while( $objArchives->next() )
        {
            $arrArchives[$objArchives->id] = $objArchives->title;
        }

        return $arrArchives;
    }
}
