<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * PHP version 5
 *
 * @package		CalendarEditor
 * @author		Daniel Gaussmann <mail@gausi.de>
  * @copyright	Daniel Gaussmann 2011-2015
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Front end modules
 */

$GLOBALS['FE_MOD']['events']['calendarEdit'] = 'DanielGausi\CalendarEditor\ModuleCalenderEdit';
$GLOBALS['FE_MOD']['events']['EventEditor'] = 'DanielGausi\CalendarEditor\ModuleEventEditor';
$GLOBALS['FE_MOD']['events']['EventReaderEditLink'] = 'DanielGausi\CalendarEditor\ModuleEventReaderEdit';
$GLOBALS['FE_MOD']['events']['EventHiddenList'] = 'DanielGausi\CalendarEditor\ModuleHiddenEventlist';
$GLOBALS['TL_HOOKS']['getAllEvents'][] = array('ListAllEvents_Hook', 'updateAllEvents');