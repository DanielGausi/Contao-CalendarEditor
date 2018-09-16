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
 * @copyright  Daniel Gaussmann 2011-2018
 * @author     Daniel Gaussmann <mail@gausi.de>
 * @package    CalendarEditor
 * @license    GNU/LGPL
 
 
 * This file includes some functions used in several module to check,
 * whether a User is authorized to edit a Calendar or a specific Event.
 
 */

	
	// Check, whether the User is an Admin to add/edit Events in this objCalendar
	function UserIsAdmin($objCalendar, $User){
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
		return FALSE;
	}
	
	// Check, whether the User is authorized to add/edit Events in this objCalendar
	function UserIsAuthorizedUser($objCalendar, $User){
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
		// no authorized User, and no Admin 
		return FALSE;		
	}
	
	function UserIsAuthorizedElapsedEvents($objCalendar, $User){
		// User is authorized to edit/add elapsed Events if
		// 1.) the User is an Admin for the Calendar or
		// 2.) The User is an Authorized User and the CalendarSetting "only Future" is False		
		return (UserIsAdmin($objCalendar, $User)) || ( UserIsAuthorizedUser($objCalendar, $User) && (! $objCalendar->caledit_onlyFuture));
	}
	
	function EditLinksAreAllowed ($objCalendar, $aEvent, $userID, $UserIsAdmin, $currentTime){		
		if ($UserIsAdmin && (!$aEvent['disable_editing'])) {
			return TRUE;
		} else
		{			
			return 				
				(
				// Allow only if if the editing is NOT disabled in the backend for this event
				(!$aEvent['disable_editing'])   				
				// Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime, 
				&& ((!$objCalendar->caledit_onlyFuture) ||  ($currentTime <= $aEvent['startTime']) )
				// Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
				&& ((!$objCalendar->caledit_onlyUser) || ($aEvent['fe_user'] == $userID))
				);

		}
	}
	
	// used in Module ModuleEventReaderEdit
	function EditLinksAreAllowed2 ($objCalendar, $objEvent, $User, $UserIsAdmin){
		if ($UserIsAdmin && (!$objEvent->disable_editing)) {
			return TRUE;
		} else
		{			
			return 				
				(
				// Allow only if if the editing is NOT disabled in the backend for this event
				(!$objEvent->disable_editing)   				
				// Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime, 
				&& ((!$objCalendar->caledit_onlyFuture) ||  (time() <= $objEvent->startTime) )
				// Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
				&& ((!$objCalendar->caledit_onlyUser) || ($objEvent->fe_user == $User->id))
				);

		}
	}
	
	

?>