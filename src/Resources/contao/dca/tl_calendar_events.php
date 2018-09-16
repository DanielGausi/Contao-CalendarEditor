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


$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] .= ';{edit_legend},fe_user, disable_editing';


$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['fe_user'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['fe_user'],
	'inputType'               => 'select',
	'exclude'                 => true,
	'foreignKey'              => 'tl_member.username',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['disable_editing'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['disable_editing'],
	'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);

