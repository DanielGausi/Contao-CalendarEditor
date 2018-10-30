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
 * Class EventEditor_Hook
 */
 
include_once('CEAuthCheck.php');
use DanielGausi\CalendarEditorBundle\CalendarModelEdit;
 
 
class ListAllEvents_Hook extends Frontend
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = '';	
	
		public function addEditLinks(&$aEvent, $strUrl)
		{
			$aEvent['editRef'] = $strUrl.'?edit='.$aEvent['id'];
			$aEvent['editLabel'] = $GLOBALS['TL_LANG']['MSC']['caledit_editLabel'];
			$aEvent['editTitle'] = $GLOBALS['TL_LANG']['MSC']['caledit_editTitle'];
		}
		
		
		/**
         Manipulate the arrEvents-Array generated by ModuleCalendar and ModuleEventlist
         **/
         public function updateAllEvents($arrEvents, $arrCalendars, $intStart, $intEnd, $objCalendarModule)
        {
			if (!is_array($arrCalendars)) {
				return $arrEvents;
			}

			if(version_compare(VERSION.'.'.BUILD, '3.5.1', '>=')) {
				$this->import('StringUtil');
			} else {
				$this->import('String');			
			}
		
			$time = time();
            $this->import('FrontendUser', 'User');
			
			// preperations: Get more information about the calendars used for these "all events"
			$CalendarObjects = array();		    // needed for a detailed authorization check
			$UserIsAdminForCalendar = array();  // 
			$UserIsMemberForCalendar = array(); // 
			$JumpPages = array();               // needed for the edit-links we want to add in his hook
			
			$objCalendars = CalendarModelEdit::findByIds($arrCalendars);
			foreach($objCalendars as $objCalendar) {
				$currentPID = $objCalendar->id; // this is the Parent-ID for the events in the Event-Array
				
				$CalendarObjects[$currentPID] = $objCalendar;				
				
				if ($objCalendar->AllowEdit) {
					// is user admin for this calendar?
					$UserIsAdminForCalendar[$currentPID]  = UserIsAdmin          ($objCalendar, $this->User);
					$UserIsMemberForCalendar[$currentPID] = UserIsAuthorizedUser ($objCalendar, $this->User);
					
					// get the jump-to-Edit-page for this calendar
					$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=(SELECT caledit_jumpTo FROM tl_calendar WHERE id=?)")
									  ->limit(1)
									  ->execute($objCalendar->id);
					if ($objPage->numRows) {
						$JumpPages[$currentPID] = $this->generateFrontendUrl($objPage->row(), '');
					}
					else {
						$JumpPages[$currentPID] = $this->Environment->request;
					}
				} else {
					// no editing allowed in this calendar
					$UserIsAdminForCalendar[$currentPID] = false;
					$UserIsMemberForCalendar[$currentPID] = false;
					$JumpPages[$currentPID] = $this->Environment->request;
				}
			}			
			
			// now: scan the events-array and add edit links where appropriate
			$currentTime = time();
			foreach ($arrEvents as &$intnext) {
				foreach ($intnext as &$intdate) {
					foreach ($intdate as &$aEvent){
						$cPID = $aEvent['pid'];
						if ( ($CalendarObjects[$cPID]->AllowEdit) && EditLinksAreAllowed($CalendarObjects[$cPID], $aEvent, $this->User->id, $UserIsAdminForCalendar[$cPID], $UserIsMemberForCalendar[$cPID], $currentTime)){
							$this->addEditLinks($aEvent, $JumpPages[$cPID]);
						}	
					}
				}
			}
			return $arrEvents;	
		}			
}

?>