<?php 

/**
 * This file is part of 
 * 
 * CalendarEditorBundle
 * @copyright  Daniel Gaußmann 2018
 * @author     Daniel Gaußmann (Gausi) 
 * @package    Calendar_Editor
 * @license    LGPL-3.0-or-later
 * @see        https://github.com/DanielGausi/Contao-CalendarEditor
 *
 * an extension for
 * Contao Open Source CMS
 * (c) Leo Feyer, LGPL-3.0-or-later
 *
 */


/**
 * Add palettes to tl_calendar
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] .= ';{edit_legend},AllowEdit';

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'AllowEdit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['AllowEdit']='caledit_onlyFuture, caledit_jumpTo, caledit_loginRequired, caledit_onlyUser, caledit_groups, caledit_adminGroup';



$GLOBALS['TL_DCA']['tl_calendar']['fields']['AllowEdit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['AllowEdit'],
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

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_groups'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_groups'],
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
    'eval'                    => array(/*'mandatory'=>true, */ 'multiple'=>true),
	'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_adminGroup'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_adminGroup'],
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('mandatory'=>false, 'multiple'=>true),
	'sql'					  => "blob NULL"
);