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

use DanielGausi\CalendarEditorBundle\Hooks\ListAllEventsHook;
use DanielGausi\CalendarEditorBundle\Modules\ModuleCalenderEdit;
use DanielGausi\CalendarEditorBundle\Modules\ModuleEventEditor;

$GLOBALS['FE_MOD']['events']['calendarEdit']        = ModuleCalenderEdit::class;
$GLOBALS['FE_MOD']['events']['EventEditor']         = ModuleEventEditor::class;
$GLOBALS['FE_MOD']['events']['EventReaderEditLink'] = 'DanielGausi\CalendarEditorBundle\ModuleEventReaderEdit';
$GLOBALS['FE_MOD']['events']['EventHiddenList']     = 'DanielGausi\CalendarEditorBundle\ModuleHiddenEventlist';

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [ListAllEventsHook::class, 'updateAllEvents'];