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
	
	// Check, whether the User is an Admin to add/edit Events in this objCalendar
	function UserIsAdmin($objCalendar, $User){
		if (!$objCalendar->AllowEdit) {
			return false;
		} else {
			if (FE_USER_LOGGED_IN) {
				// Get Admin-Groups which are allowed to edit events in this calendar
				// (Admins are allowed to edit events even if the "only owner"-setting is checked)
				// (Admins are allowed to add events on elapsed days)
				$admin_groups = deserialize($objCalendar->caledit_adminGroup);
				if (is_array($admin_groups) 
					&& (count($admin_groups) > 0) 
					&& (count(array_intersect($admin_groups, $User->groups)) > 0)){
						return  TRUE;
				}
			}		
		}
		return FALSE;
	}
	
	// Check, whether the User is authorized to add/edit Events in this objCalendar
	function UserIsAuthorizedUser($objCalendar, $User){
		if (!$objCalendar->AllowEdit) {
			return false;
		} else {
			if (!$objCalendar->caledit_loginRequired) {	
				// if no Login is required, consider the User as "authorized"
				return TRUE;
			} 
			else {
				if (FE_USER_LOGGED_IN) {				
					// Admins are authorized as well ;-)
					if (UserIsAdmin($objCalendar, $User)) {
						return TRUE;					
					}
					
					// Get Groups which are allowed to edit events in this calendar
					$groups = deserialize($objCalendar->caledit_groups);
					if (is_array($groups) 
						&& (count($groups) > 0) 
						&& (count(array_intersect($groups, $User->groups)) > 0)) {
							return TRUE;
					}
				}
			}
		}
		// no authorized User, and no Admin 
		return FALSE;		
	}
	
	function UserIsAuthorizedElapsedEvents($objCalendar, $User){
		if (!$objCalendar->AllowEdit) {
			return false;
		} else {
			// User is authorized to edit/add elapsed Events if
			// 1.) the User is an Admin for the Calendar or
			// 2.) The User is an Authorized User and the CalendarSetting "only Future" is False		
			return (UserIsAdmin($objCalendar, $User)) || ( UserIsAuthorizedUser($objCalendar, $User) && (! $objCalendar->caledit_onlyFuture));
		}
	}
	
	// used in GetAllEvents Hook
	function EditLinksAreAllowed ($objCalendar, $aEvent, $userID, $UserIsAdmin, $UserIsMember, $currentTime){		
		if (!$objCalendar->AllowEdit) {
			return false;
		} else {
			if ($UserIsAdmin && (!$aEvent['disable_editing'])) {
				return TRUE;
			} else {						
				return 				
					(
					// Allow only if the editing is NOT disabled in the backend for this event
					(!$aEvent['disable_editing'])  
					// Allow only if the User belongs to an authorized Member group
					&& ($UserIsMember)				
					// Allow only if FE User is logged in or the calendar does not requie login
					&& ( FE_USER_LOGGED_IN || !$objCalendar->caledit_loginRequired)
					// Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime, 
					&& ((!$objCalendar->caledit_onlyFuture) ||  ($currentTime <= $aEvent['startTime']) )
					// Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
					&& ((!$objCalendar->caledit_onlyUser) || ($aEvent['fe_user'] == $userID))
					);
			}
		}
	}
	
	// used in Module ModuleEventReaderEdit
	function EditLinksAreAllowed2 ($objCalendar, $objEvent, $User, $UserIsAdmin, $UserIsMember){
		if (!$objCalendar->AllowEdit) {
			return false;
		} else {
			if ($UserIsAdmin && (!$objEvent->disable_editing)) {
				return TRUE;
			} else {
				return 
					(
					// Allow only if if the editing is NOT disabled in the backend for this event
					(!$objEvent->disable_editing) 
					// Allow only if the User belongs to an authorized Member group
					&& ($UserIsMember)				
					// Allow only if FE User is logged in or the calendar does not requie login
					&& ( FE_USER_LOGGED_IN || !$objCalendar->caledit_loginRequired)				
					// Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime, 
					&& ((!$objCalendar->caledit_onlyFuture) ||  (time() <= $objEvent->startTime) )
					// Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
					&& ((!$objCalendar->caledit_onlyUser) || ($objEvent->fe_user == $User->id))
					);
			}
		}
	}
	
	

?>