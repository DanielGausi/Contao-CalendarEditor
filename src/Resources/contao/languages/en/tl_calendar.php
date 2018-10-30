<?php 

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