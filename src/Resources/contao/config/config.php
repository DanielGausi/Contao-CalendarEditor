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

$GLOBALS['FE_MOD']['events']['calendarEdit'] = 'ModuleCalenderEdit';
$GLOBALS['FE_MOD']['events']['EventEditor'] = 'ModuleEventEditor';
$GLOBALS['FE_MOD']['events']['EventReaderEditLink'] = 'ModuleEventReaderEdit';
$GLOBALS['FE_MOD']['events']['EventHiddenList'] = 'ModuleHiddenEventlist';
$GLOBALS['TL_HOOKS']['getAllEvents'][] = array('ListAllEvents_Hook', 'updateAllEvents');