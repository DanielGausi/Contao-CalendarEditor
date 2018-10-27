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

namespace DanielGausi\CalendarEditorBundle;

use Contao\Calendar;
use Contao\CalendarModel;
use Contao\Events;


class ModuleHiddenEventlist extends \ModuleEventlist
{

	/**
	 * Current date object
	 * @var integer
	 */
	protected $Date;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_eventlist';
	
	protected static $strTabelle = 'tl_calendar_events';
	
	
	public static function findCurrentUnPublishedByPid($intPid, $intStart, $intEnd, array $arrOptions=array())
	{
		$t = static::$strTabelle;
		$intStart = intval($intStart);
		$intEnd = intval($intEnd);

		$arrColumns = array("$t.pid=? AND $t.published!='1' AND (($t.startTime>=$intStart AND $t.startTime<=$intEnd) OR ($t.endTime>=$intStart AND $t.endTime<=$intEnd) OR ($t.startTime<=$intStart AND $t.endTime>=$intEnd) OR ($t.recurring='1' AND ($t.recurrences=0 OR $t.repeatEnd>=$intStart) AND $t.startTime<=$intEnd))");

		//if (!BE_USER_LOGGED_IN)
		//{
		//	$time = \Date::floorToMinute();
		//	$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
		//}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order']  = "$t.startTime";
		}

		return \CalendarEventsModel::findBy($arrColumns, $intPid, $arrOptions);
	}

	
	
	/**
	 * GetAllEvents: Almost the same as in class Events, but with "published=0"
	 */
	protected function getAllEvents($arrCalendars, $intStart, $intEnd)
	{
		if (!is_array($arrCalendars))
		{
			return array();
		}
				
		if(version_compare(VERSION.'.'.BUILD, '3.5.1', '>=')) {
			$this->import('StringUtil');
		} else {
			$this->import('String');			
		}
				
		
		$this->arrEvents = array();
				
		foreach ($arrCalendars as $id)
		{
			$strUrl = $this->strUrl;		
			$objCalendar = \CalendarModel::findByPk($id);

			// Get the current "jumpTo" page
			if ($objCalendar !== null && $objCalendar->jumpTo && ($objTarget = $objCalendar->getRelated('jumpTo')) !== null)
			{
				/** @var \PageModel $objTarget */
				$strUrl = $objTarget->getFrontendUrl((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/events/%s');
			}
			$objEvents = $this->findCurrentUnPublishedByPid($id, $intStart, $intEnd);

			if ($objEvents === null)
			{				
				continue;
			}
			

			while ($objEvents->next()) 
			{
				$this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);
				
				// Recurring events
				if ($objEvents->recurring) {
					$count = 0;
					$arrRepeat = deserialize($objEvents->repeatEach);

					while ($objEvents->endTime < $intEnd) {
						if ($objEvents->recurrences > 0 && $count++ >= $objEvents->recurrences) {
							break;
						}

						$arg = $arrRepeat['value'];
						$unit = $arrRepeat['unit'];

						if ($arg < 1) {
							break;
						}

						$strtotime = '+ ' . $arg . ' ' . $unit;

						$objEvents->startTime = strtotime($strtotime, $objEvents->startTime);
						$objEvents->endTime = strtotime($strtotime, $objEvents->endTime);

						// Skip events outside the scope
						if ($objEvents->endTime < $intStart || $objEvents->startTime > $intEnd) {
							continue;
						}

						$this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);
					}
				}
			}
		}

		// Sort data
		foreach (array_keys($this->arrEvents) as $key)
		{
			ksort($this->arrEvents[$key]);
		}
		
		//$this->arrEvents = $this->ListAllEvents_Hook->updateAllEvents($this->arrEvents, $arrCalendars, $intStart, $intEnd, $this);

		// HOOK: modify result set
		if (isset($GLOBALS['TL_HOOKS']['getAllEvents']) && is_array($GLOBALS['TL_HOOKS']['getAllEvents']))
		{
			foreach ($GLOBALS['TL_HOOKS']['getAllEvents'] as $callback)
			{
				$this->import($callback[0]);
				$this->arrEvents = $this->{$callback[0]}->{$callback[1]}($this->arrEvents, $arrCalendars, $intStart, $intEnd, $this);
			}
		}		

		return $this->arrEvents;
	}


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### UNPULISHED EVENT LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar, true));

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
		parent::compile();
	}
}

?>