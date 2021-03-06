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
 * Class ModuleEventReaderEdit 
 */
 
namespace DanielGausi\CalendarEditorBundle; 

use Contao\Calendar;
use Contao\CalendarModel;
use Contao\Events;
 
include_once('CEAuthCheck.php');
 
class ModuleEventReaderEdit extends Events
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_event_ReaderEditLink';
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### EVENT READER EDIT LINK ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		// Return if no event has been specified
		if (!$this->Input->get('events')) {
			return '';
		}
		
		$this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar));

		// Return if there are no calendars
		if (!is_array($this->cal_calendar) || count($this->cal_calendar) < 1)
		{
			return '';
		}
		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->Template = new \FrontendTemplate($this->strTemplate);
		$this->Template->editRef = '';
		
		// FE user is logged in
		$this->import('FrontendUser', 'User');
		
		// Get current event
		$objEvent = $this->Database->prepare("SELECT *, author AS authorId, (SELECT title FROM tl_calendar WHERE tl_calendar.id=tl_calendar_events.pid) AS calendar, (SELECT name FROM tl_user WHERE id=author) author FROM tl_calendar_events WHERE pid IN(" . implode(',', array_map('intval', $this->cal_calendar)) . ") AND (id=? OR alias=?)" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1" : ""))
								   ->limit(1)
								   ->execute((is_numeric($this->Input->get('events')) ? $this->Input->get('events') : 0), $this->Input->get('events'), $time, $time);

		if ($objEvent->numRows < 1) {				
			$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'];
			$this->Template->error_class = 'error';			
			return;
		}
		
		// get Calender with PID
		$pid = $objEvent->pid;
		$objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")
                         			->limit(1)
									->execute($pid);

									
		if ($objCalendar->numRows < 1) {
			return; // No calendar found
		}
		
		if ($objCalendar->AllowEdit) {
			// Calendar allows editing
			// check user rights			
			
			$AuthorizedUser = UserIsAuthorizedUser($objCalendar, $this->User);
			$UserIsAdmin = UserIsAdmin($objCalendar, $this->User);
			
			$AuthorizedUserElapsedEvents = UserIsAuthorizedElapsedEvents($objCalendar, $this->User);	
			$AddEditLinks = EditLinksAreAllowed2 ($objCalendar, $objEvent, $this->User, $UserIsAdmin, $AuthorizedUser);
			
			if ($AddEditLinks) {				
				// get the JumpToEdit-Page for this calendar
				$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=(SELECT caledit_jumpTo FROM tl_calendar WHERE id=?)")
								  ->limit(1)
								  ->execute($objCalendar->id);
				if ($objPage->numRows) {
					$strUrl = $this->generateFrontendUrl($objPage->row(), '');
				}
				else {
					$strUrl = '';//$this->Environment->request;	
				}
					
				$this->Template->editRef = $strUrl.'?edit='.$objEvent->id;
				$this->Template->editLabel = $GLOBALS['TL_LANG']['MSC']['caledit_editLabel'];
				$this->Template->editTitle = $GLOBALS['TL_LANG']['MSC']['caledit_editTitle'];
				
				if ($this->caledit_showCloneLink) {
					$this->Template->cloneRef = $strUrl.'?clone='.$objEvent->id;
					$this->Template->cloneLabel = $GLOBALS['TL_LANG']['MSC']['caledit_cloneLabel'];
					$this->Template->cloneTitle = $GLOBALS['TL_LANG']['MSC']['caledit_cloneTitle'];
				}
				if ($this->caledit_showDeleteLink) {
					$this->Template->deleteRef = $strUrl.'?delete='.$objEvent->id;
					$this->Template->deleteLabel = $GLOBALS['TL_LANG']['MSC']['caledit_deleteLabel'];
					$this->Template->deleteTitle = $GLOBALS['TL_LANG']['MSC']['caledit_deleteTitle'];
				}

			} else {
				if (!$AuthorizedUser) {
					$this->Template->error_class = 'error';
					$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_UnauthorizedUser'];
					return;
				}
				
				if ($objEvent->disable_editing) {
					// the event is locked in the backend
					$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_DisabledEvent'];
					$this->Template->error_class = 'error';
				} else {
					if (!$AuthorizedUserElapsedEvents) {
						// the user is authorized, but the event has elapsed
						$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_NoPast'];
						$this->Template->error_class = 'error';
					} else {
						// the user is NOT authorized at all (reason: only the creator can edit it)						
						$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_OnlyUser'];
						$this->Template->error_class = 'error';
					}
				}
			}		
		} else {
			$this->Template->error_class = 'error';
			$this->Template->error = $GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'];
			return ;
		}		
	}
}

?>