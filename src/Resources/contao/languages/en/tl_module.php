<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @copyright  Daniel Gaussmann 2011-2015
 * @author     Daniel Gaussmann <mail@gausi.de>
 * @package    Language
 * @license    GNU/LGPL
 */
 
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

$GLOBALS['TL_LANG']['tl_module']['caledit_sendMail']       = array('Send email','Send a notification email when a new event is created.');
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
$GLOBALS['TL_LANG']['tl_module']['caledit_holidayCalendar']['0'] = "Calendar with hints to holidays";
$GLOBALS['TL_LANG']['tl_module']['caledit_holidayCalendar']['1'] = "Select a calendar with holidays. These events will be displayed along with the date in the header of each day. Note: You have to select this calendar in the 'calendars' list above as well.";

?>