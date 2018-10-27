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
 * Front end modules
 */

$GLOBALS['FE_MOD']['events']['calendarEdit']        = 'DanielGausi\CalendarEditorBundle\ModuleCalenderEdit';
$GLOBALS['FE_MOD']['events']['EventEditor']         = 'DanielGausi\CalendarEditorBundle\ModuleEventEditor';
$GLOBALS['FE_MOD']['events']['EventReaderEditLink'] = 'DanielGausi\CalendarEditorBundle\ModuleEventReaderEdit';
$GLOBALS['FE_MOD']['events']['EventHiddenList']     = 'DanielGausi\CalendarEditorBundle\ModuleHiddenEventlist';

$GLOBALS['TL_HOOKS']['getAllEvents'][] = array('ListAllEvents_Hook', 'updateAllEvents');