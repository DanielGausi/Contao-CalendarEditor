<?php 

namespace DanielGausi\CalendarEditorBundle\Modules;

use BackendTemplate;
use Contao\Date;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use DanielGausi\CalendarEditorBundle\Models\CalendarModelEdit;
use DanielGausi\CalendarEditorBundle\Services\CheckAuthService;
use ModuleCalendar;

class ModuleCalenderEdit extends ModuleCalendar
{
	// variable which indicates whether events can be added or not (on elapsed days)
	protected bool $allowElapsedEvents;
	protected bool $allowEditEvents;
		
	
	public function getHolidayCalendarIDs($cals): array
    {
		$IDs = array();		
		
		if (is_array($cals)) {
		foreach ($cals as $flupp) {
			$IDs[] = $flupp;
		}
		}
		return $IDs;
	}

	// check whether the current FE User is allowed to edit any of the calendars
	public function checkUserAuthorizations($arrCalendars): void
    {
        /** @var CheckAuthService $checkAuthService */
        $checkAuthService = System::getContainer()->get('caledit.service.auth');
		$this->import('FrontendUser', 'User');			
		$this->allowElapsedEvents = false;
		$this->allowEditEvents = false;
				
		$calendarModels = CalendarModelEdit::findByIds($arrCalendars);
		foreach($calendarModels as $calendarModel) {
			$this->allowElapsedEvents = ($this->allowElapsedEvents || $checkAuthService->isUserAuthorizedElapsedEvents($calendarModel, $this->User) );
			$this->allowEditEvents    = ($this->allowEditEvents    || $checkAuthService->isUserAuthorized($calendarModel, $this->User) );
		}
	}

	// overwrite the compileWeeks-Method from ModuleCalendar
	protected function compileWeeks(): array
    {
		$intDaysInMonth = (int)date('t', $this->Date->monthBegin);
		$intFirstDayOffset = (int)(date('w', $this->Date->monthBegin) - $this->cal_startDay);

		if ($intFirstDayOffset < 0) {
			$intFirstDayOffset += 7;
		}

		// Check User Authorization to add Events into (one of) the Calendars used in this module
		// this will set the variables  $this->AllowEditEvents and $this->AllowElapsedEvents
		$this->checkUserAuthorizations($this->cal_calendar);

        $addUrl = '';
		if ($this->allowEditEvents){
			// get the JumpToAdd-Page for this calendar
            $page = PageModel::findByPk($this->caledit_add_jumpTo);
            if ($page !== null) {
				$addUrl = $page->getFrontendUrl();
			}
		}

		$intYear = date('Y', $this->Date->tstamp);
		$intMonth = date('m', $this->Date->tstamp);
		
		$intColumnCount = -1;
		$intNumberOfRows = ceil(($intDaysInMonth + $intFirstDayOffset) / 7);
		$allEvents = $this->getAllEvents($this->cal_calendar, $this->Date->monthBegin, $this->Date->monthEnd);
		$arrDays = [];

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
			if (empty($intKey) || !isset($allEvents[$intKey]))
			{
				$arrDays[$strWeekClass][$i]['label'] = $intDay;
				$arrDays[$strWeekClass][$i]['class'] = 'days' . $strClass;
				// add Links to add Events, if allowed
				if ($this->allowEditEvents && ($this->allowElapsedEvents || ($intKey >= date('Ymd')) )  ){
					$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
					$arrDays[$strWeekClass][$i]['addRef'] = $addUrl . '?add=' . Date::parse($dateformat, $ts);
				}
				$arrDays[$strWeekClass][$i]['events'] = [];

				continue;
			}		
			
			$events = [];
			$holidayEvents = [];

			$validHolidays = [];
            $this->cal_holidayCalendar = $this->sortOutProtected(StringUtil::deserialize($this->cal_holidayCalendar, true));
			if (is_array($this->cal_holidayCalendar) && !empty($this->cal_holidayCalendar)) {
				$validHolidays = $this->getHolidayCalendarIDs($this->cal_holidayCalendar);
			}

			// Get all events of a day
			foreach ($allEvents[$intKey] as $v) {
				foreach ($v as $vv) {
					if ( in_array($vv['parent'], $validHolidays)) {
						$holidayEvents[] = $vv;
					} else {
						$events[] = $vv;
					}
				}
			}
			
			if (count($holidayEvents) > 0) {
				$strClass .= ' holiday';
			}

			$arrDays[$strWeekClass][$i]['label'] = $intDay;				
			if ($this->allowEditEvents && ($this->allowElapsedEvents || ($intKey >= date('Ymd')) )  ){
				$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
				$arrDays[$strWeekClass][$i]['addRef'] = $addUrl . '?add=' . Date::parse($dateformat, $ts);
			}
			$arrDays[$strWeekClass][$i]['class'] = 'days active' . $strClass;
			$arrDays[$strWeekClass][$i]['href'] = $this->strLink . '?day=' . $intKey;
			$arrDays[$strWeekClass][$i]['title'] = sprintf(specialchars($GLOBALS['TL_LANG']['MSC']['cal_events']), count($events));
			$arrDays[$strWeekClass][$i]['events'] = $events;
			$arrDays[$strWeekClass][$i]['holidayEvents'] = $holidayEvents;
		}

		return $arrDays;
	}
	
	public function generate(): string
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

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
	protected function compile(): void
    {
        parent::compile();         	
	}
}
