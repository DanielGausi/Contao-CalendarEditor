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

