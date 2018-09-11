<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'ListAllEvents_Hook'    => 'system/modules/calendar_editor/modules/ListAllEvents_Hook.php',
	'ModuleCalenderEdit'    => 'system/modules/calendar_editor/modules/ModuleCalenderEdit.php',
	'ModuleEventEditor'     => 'system/modules/calendar_editor/modules/ModuleEventEditor.php',
	'ModuleEventReaderEdit' => 'system/modules/calendar_editor/modules/ModuleEventReaderEdit.php',
	'ModuleHiddenEventlist' => 'system/modules/calendar_editor/modules/ModuleHiddenEventlist.php',	
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cal_default_edit'           => 'system/modules/calendar_editor/templates',
	'event_list_edit'            => 'system/modules/calendar_editor/templates',
	'event_list_hidden'          => 'system/modules/calendar_editor/templates',	
	'event_teaser_edit'          => 'system/modules/calendar_editor/templates',
	'event_upcoming_edit'        => 'system/modules/calendar_editor/templates',
	'eventEdit_default'          => 'system/modules/calendar_editor/templates',
	'eventEdit_delete'           => 'system/modules/calendar_editor/templates',
	'eventEdit_duplicate'        => 'system/modules/calendar_editor/templates',		
	'mod_event_ReaderEditLink'   => 'system/modules/calendar_editor/templates',
));
