<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

 
$GLOBALS['TL_LANG']['tl_module']['caledit_add_jumpTo']     = array('Redirect page for adding events', 'Please choose the page to which the FE user will be redirected when adding an event.');
$GLOBALS['TL_LANG']['tl_module']['caledit_template']       = array('Event editor template (edit)', 'Select the template for the event editor when editing an event.');

$GLOBALS['TL_LANG']['tl_module']['caledit_delete_template']= array('Event editor template (delete)', 'Select the template for the event editor when deleting an event.');
$GLOBALS['TL_LANG']['tl_module']['caledit_clone_template'] = array('Event editor template (duplicate)', 'Select the template for the event editor when duplicating an event');

$GLOBALS['TL_LANG']['tl_module']['caledit_showCloneLink']['0'] = "Add a 'Duplicate' link.";
$GLOBALS['TL_LANG']['tl_module']['caledit_showCloneLink']['1'] = "Create an additional link to duplicate the event.";

$GLOBALS['TL_LANG']['tl_module']['caledit_showDeleteLink']['0'] = "Add a 'Delete' link.";
$GLOBALS['TL_LANG']['tl_module']['caledit_showDeleteLink']['1'] = "Create an additional link to delete the event.";

$GLOBALS['TL_LANG']['tl_module']['caledit_tinMCEtemplate'] = array('Rich text editor', 'Select a configuration file if you want to use TinyMCE rich text editor. You can create custom configurations by adding a file called tinyXXX in system/config.');

$GLOBALS['TL_LANG']['tl_module']['caledit_allowPublish']   = array('Allow publishing','If this is checked, the FE user is allowed to publish/hide events. Otherwise a BE user has to publish it.');
$GLOBALS['TL_LANG']['tl_module']['caledit_allowDelete']    = array('Allow cloning','If this is checked, the FE user is allowed to clone events, to create multiple events of the same type on different days.');

$GLOBALS['TL_LANG']['tl_module']['caledit_sendMail']       = array('Send notification email','Send a notification email when a new event is created or edited.');
$GLOBALS['TL_LANG']['tl_module']['caledit_mailRecipient']  = array('Mail recipient','Enter the email address the notification should be sent to. Separate multiple addresses with comma.');
$GLOBALS['TL_LANG']['tl_module']['caledit_alternateCSSLabel'] = array('Label for field "CSS"','Enter an alternate label for the CSS field (e.g. "location" or "trainer")');
$GLOBALS['TL_LANG']['tl_module']['caledit_mandatoryfields']   = array('Additional mandatory fields','Select additional mandatory fields. Note: "Startdate" and "Title" are ALWAYS mandatory.');

$GLOBALS['TL_LANG']['tl_module']['caledit_usePredefinedCss'] = array('Use predefined CSS classes','Define some CSS-classes the FE user can choose.');
$GLOBALS['TL_LANG']['tl_module']['caledit_cssValues'] = array('CSS label/values', 'Enter a list of some label/values for CSS. The "label" is shown in the selection, the "value" is used as CSS class in the event.');

$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['starttime'] = 'Start time';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['location']    = 'Location';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['teaser']    = 'Teaser';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['details']   = 'Details';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['css']       = 'CSS class';

$GLOBALS['TL_LANG']['tl_module']['edit_legend'] = 'Frontend editing';
$GLOBALS['TL_LANG']['tl_module']['css_label'] = 'Label';
$GLOBALS['TL_LANG']['tl_module']['css_value'] = 'Value';

$GLOBALS['TL_LANG']['tl_module']['edit_holidays'] = "Holidays";
$GLOBALS['TL_LANG']['tl_module']['caledit_setting_publish'] = "Publishing";

$GLOBALS['TL_LANG']['tl_module']['caledit_holidayCalendar']['0'] = "Calendar with hints to holidays";
$GLOBALS['TL_LANG']['tl_module']['caledit_holidayCalendar']['1'] = "Select a calendar with holidays. These events will be displayed along with the date in the header of each day. Note: You have to select this calendar in the 'calendars' list above as well.";

// for the CalendarField extension
$GLOBALS['TL_LANG']['tl_module']['caledit_useDatePicker']['0'] = "Use Date-Picker (jQuery required)";
$GLOBALS['TL_LANG']['tl_module']['caledit_useDatePicker']['1'] = "Adds a jQuery calendar to the date fields to be able to select the date. The 'Load jQuery' option must be enabled in the page layout.";
 
$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection']          = array('Date direction', 'Select if date selection is restricted.');
$GLOBALS['TL_LANG']['tl_module']['caledit_dateIncludeCSSTheme']    = array('jQuery UI theme', 'Please select the jQuery UI theme (external Stylesheet). For further information please have a look at the jQuery UI webseite <a hre="http://jqueryui.com/themeroller">http://jqueryui.com/themeroller</a>. Leave the field empty if you want to design the calendar using your own CSS.');
$GLOBALS['TL_LANG']['tl_module']['caledit_dateImage']              = array('Show calendar icon', 'Click here to show a calendar picker icon.');
$GLOBALS['TL_LANG']['tl_module']['caledit_dateImageSRC']           = array('Custom icon', 'Select a custom image to replace the default calendar icon.');

$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection_ref']['all']     = 'Allow all dates';
$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection_ref']['ltToday'] = 'Only dates in the past (excluding today)';
$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection_ref']['leToday'] = 'Only dates in the past (including today)';
$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection_ref']['geToday'] = 'Only dates in the future (including today)';
$GLOBALS['TL_LANG']['tl_module']['caledit_dateDirection_ref']['gtToday'] = 'Only dates in the future (excluding today)';
?>