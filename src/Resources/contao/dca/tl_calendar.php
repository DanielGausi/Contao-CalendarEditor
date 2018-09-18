<?php 

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @copyright  Daniel Gaussmann 2011-2018
 * @author     Daniel Gaussmann <mail@gausi.de> 
 * @package    CalendarEditor
 * @license    GNU/LGPL
 */


/**
 * Add palettes to tl_calendar
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = '{title_legend},name,headline,type;{edit_legend},allowEdit';

//$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] .= ';{edit_legend},allowEdit';

//$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace
//(
 //   '{extended_legend},bg_color,fg_color;',
 //   '{extended_legend},bg_color,fg_color;{edit_legend},allowEdit;',
 //   $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']
//);


$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['allowEdit']='caledit_onlyFuture, caledit_jumpTo, caledit_loginRequired, caledit_onlyUser, caledit_groups, caledit_adminGroup';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'allowEdit';


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

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_onlyUser'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyUser'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'sql'					  => "char(1) NOT NULL default ''"
);