<?php 

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
  *
 * @copyright  Daniel Gaussmann 2011-2018
 * @author     Daniel Gaussmann <mail@gausi.de> 
 * @package    CalendarEditor
 * @license    GNU/LGPL
 */


/**
 * Add palettes to tl_calendar
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] .= ';{edit_legend},allowEdit';

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'allowEdit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['allowEdit']='caledit_onlyFuture, caledit_jumpTo, caledit_loginRequired, caledit_onlyUser;';



$GLOBALS['TL_DCA']['tl_calendar']['fields']['allowEdit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['allowEdit'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_onlyFuture'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyFuture'],
	'inputType'               => 'checkbox',
	'eval'                    => array('default'=>'true'),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_jumpTo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_jumpTo'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array('fieldType'=>'radio'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_loginRequired'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_loginRequired'],
	'inputType'               => 'checkbox',
	'eval'                    => array('default'=>'true'),
	'sql'					  => "char(1) NOT NULL default '1'"
);
$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_onlyUser'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyUser'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'sql'					  => "char(1) NOT NULL default ''"
);