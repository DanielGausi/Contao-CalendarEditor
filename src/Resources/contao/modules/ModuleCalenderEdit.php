<?php 

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
 * @copyright  Daniel Gaussmann 2011-2018
 * @author     Daniel Gaussmann <mail@gausi.de>
 * @package    CalendarEditor
 * @license    GNU/LGPL
 */
 
namespace DanielGausi\CalendarEditorBundle;

use Contao\Calendar;
use Contao\CalendarModel;
use Contao\Events;

//use Contao\Calendar;
//use Contao\CalendarModel;

 include_once('CEAuthCheck.php');

/**
 * Class ModuleCalenderEdit
 */
class ModuleCalenderEdit extends \ModuleCalendar
{

	// variable which indicates whether events can be added or not (on elapsed days)
	protected $AllowElapsedEvents;	
	protected $AllowEditEvents;
		
	
	public function GetHolidayCalendarIDs($cals) {
		$IDs = array();		
		
		if (is_array($cals)) {
		foreach ($cals as $flupp) {
			$IDs[] = $flupp;
		}
		}
		return $IDs;
	}

	// check weather the current FE User is allowed to edit any of the calendars
	public function CheckUserAuthorizations($arrCalendars) {
		$this->import('FrontendUser', 'User');			
		$this->AllowElapsedEvents = False;
		$this->AllowEditEvents = False;
				
		foreach ($arrCalendars as $id)
		{			
			// get properties of this calendar
			$objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")
										->limit(1)
										->execute($id);

			$this->AllowElapsedEvents = ($this->AllowElapsedEvents || UserIsAuthorizedElapsedEvents($objCalendar, $this->User) );
			$this->AllowEditEvents    = ($this->AllowEditEvents    || UserIsAuthorizedUser($objCalendar, $this->User) );
		}		
	}

	

	// overwrite the compileWeeks-Method from ModuleCalendar
	protected function compileWeeks()
	{
		$intDaysInMonth = date('t', $this->Date->monthBegin);
		$intFirstDayOffset = date('w', $this->Date->monthBegin) - $this->cal_startDay;

		if ($intFirstDayOffset < 0) {
			$intFirstDayOffset += 7;
		}

		// Check User Authorization to add Events into (one of) the Calendars used in this module
		// this will set the variables  $this->AllowEditEvents and $this->AllowElapsedEvents
		$this->CheckUserAuthorizations($this->cal_calendar);
			 

		if ($this->AllowEditEvents){
			// get the JumpToAdd-Page for this calendar
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
							  ->limit(1)
							  ->execute($this->caledit_add_jumpTo);
			if ($objPage->numRows) {
				$addUrl = $this->generateFrontendUrl($objPage->row(), '');
			}
			else {
				$addUrl = $this->Environment->request;
			}
		}
		$intYear = date('Y', $this->Date->tstamp);
		$intMonth = date('m', $this->Date->tstamp);
		
		$intColumnCount = -1;
		$intNumberOfRows = ceil(($intDaysInMonth + $intFirstDayOffset) / 7);
		$arrAllEvents = $this->getAllEvents($this->cal_calendar, $this->Date->monthBegin, $this->Date->monthEnd);
		$arrDays = array();

		$dateformat = $GLOBALS['TL_CONFIG']['dateFormat'];	
		
		// Compile days
		for ($i=1; $i<=($intNumberOfRows * 7); $i++)
		{
			$intWeek = floor(++$intColumnCount / 7);
			$intDay = $i - $intFirstDayOffset;
			$intCurrentDay = ($i + $this->cal_startDay) % 7;

			$strWeekClass = 'week_' . $intWeek;
			$strWeekClass .= ($intWeek == 0) ? ' first' : '';
			$strWeekClass .= ($intWeek == ($intNumberOfRows - 1)) ? ' last' : '';

			$strClass = ($intCurrentDay < 2) ? ' weekend' : '';
			$strClass .= ($i == 1 || $i == 8 || $i == 15 || $i == 22 || $i == 29 || $i == 36) ? ' col_first' : '';
			$strClass .= ($i == 7 || $i == 14 || $i == 21 || $i == 28 || $i == 35 || $i == 42) ? ' col_last' : '';

			// Empty cell
			if ($intDay < 1 || $intDay > $intDaysInMonth) {
				$arrDays[$strWeekClass][$i]['label'] = '&nbsp;';
				$arrDays[$strWeekClass][$i]['class'] = 'days empty' . $strClass ;
				$arrDays[$strWeekClass][$i]['events'] = array();

				continue;
			}

			$intKey = date('Ym', $this->Date->tstamp) . ((strlen($intDay) < 2) ? '0' . $intDay : $intDay);
			$strClass .= ($intKey == date('Ymd')) ? ' today' : '';

			$arrDays[$strWeekClass][$i]['addLabel'] = $GLOBALS['TL_LANG']['MSC']['caledit_addLabel'];
			$arrDays[$strWeekClass][$i]['addTitle'] = $GLOBALS['TL_LANG']['MSC']['caledit_addTitle'];
			
			// Inactive days
			if (empty($intKey) || !isset($arrAllEvents[$intKey]))
			{
				$arrDays[$strWeekClass][$i]['label'] = $intDay;
				$arrDays[$strWeekClass][$i]['class'] = 'days' . $strClass;
				// add Links to add Events, if allowed
				if ($this->AllowEditEvents && ($this->AllowElapsedEvents || ($intKey >= date('Ymd')) )  ){					
					$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
					$arrDays[$strWeekClass][$i]['addRef'] = $addUrl.'?add='.$this->parseDate($dateformat, $ts);
				}
				$arrDays[$strWeekClass][$i]['events'] = array();

				continue;
			}		
			
			$arrEvents = array();
			$arrHolidayEvents = array();

			$ValidHolidays = array();
			$this->cal_holidayCalendar = $this->sortOutProtected(deserialize($this->cal_holidayCalendar, true));
			if (is_array($this->cal_holidayCalendar) && !empty($this->cal_holidayCalendar)) {
				$ValidHolidays = $this->GetHolidayCalendarIDs($this->cal_holidayCalendar);
			}

			// Get all events of a day
			foreach ($arrAllEvents[$intKey] as $v) {
				foreach ($v as $vv) {
					if ( in_array($vv['parent'], $ValidHolidays)) {
						$arrHolidayEvents[] = $vv;						
					} else {
						$arrEvents[] = $vv; 
					}
				}
			}
			
			if (count($arrHolidayEvents) > 0) {
				$strClass .= ' holiday';
			}

			$arrDays[$strWeekClass][$i]['label'] = $intDay;				
			if ($this->AllowEditEvents && ($this->AllowElapsedEvents || ($intKey >= date('Ymd')) )  ){					
				$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
				$arrDays[$strWeekClass][$i]['addRef'] = $addUrl.'?add='.$this->parseDate($dateformat, $ts);				
			}
			$arrDays[$strWeekClass][$i]['class'] = 'days active' . $strClass;
			$arrDays[$strWeekClass][$i]['href'] = $this->strLink . ($GLOBALS['TL_CONFIG']['disableAlias'] ? '&amp;' : '?') . 'day=' . $intKey;
			$arrDays[$strWeekClass][$i]['title'] = sprintf(specialchars($GLOBALS['TL_LANG']['MSC']['cal_events']), count($arrEvents));
			$arrDays[$strWeekClass][$i]['events'] = $arrEvents;
			$arrDays[$strWeekClass][$i]['holidayEvents'] = $arrHolidayEvents;
		}

		return $arrDays;
	}
	
	public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### CALENDAR WITH FE EDITING ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }       

        return parent::generate();
    }


	/**
	 * Generate module
	 */
	protected function compile()
	{
        parent::compile();         	
	}
}

?>