<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  inspiredminds.at 2017
 * @author     Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @package    news_archiving
 */


$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('NewsArchiving','archiveNews');
