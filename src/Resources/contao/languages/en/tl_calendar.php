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


/**
 * Miscellaneous
 */

$GLOBALS['TL_LANG']['tl_calendar']['AllowEdit'] = array('Enable Frontend editing','Allow Frontend-Users to add and edit events in this calendar.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyFuture'] = array('Only future events','Allow editing only for future events.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_jumpTo'] = array('Redirect page for editing','Please choose the event editor page to which the user will be redirected when clicking an edit link.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_loginRequired'] = array('Login required for Frontend editing (strongly recommended)', 'If this is checked, only registered user (from the groups below) are allowed to add/edit events. WARNING: Otherwise EVERYBODY can add/edit events.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyUser'] = array('Allow editing only for owner','If this is checked, only the creator of an event will be able to edit it later. NOTE: If this is checked, and an unregistered user creates an event, only Frontend-Admins can edit this event.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_groups'] = array('Allowed member groups for editing','These groups will be able to add (and edit) events into this calendar.');
$GLOBALS['TL_LANG']['tl_calendar']['caledit_adminGroup'] = array('Frontend-Admins for editing','Frontend-Admins can edit all events, even if "only user" is checked.');

$GLOBALS['TL_LANG']['tl_calendar']['edit_legend'] = 'Frontend editing';


?>