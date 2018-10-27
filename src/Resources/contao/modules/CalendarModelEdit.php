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


class CalendarModelEdit extends \CalendarModel
{
	/**
	 * Find all calendars by an array of IDs
	 *
	 * @return Model\Collection|CalendarEventsModel[]|CalendarEventsModel|null A collection of models or null if there are no events
	 */
	public static function findByIds($arrIds, array $arrOptions=array())
	{		
		if (empty($arrIds) || !\is_array($arrIds))
		{
			return null;
		}
		$t = static::$strTable;		
		$arrColumns[] = "$t.id IN(" . implode(',', array_map('\intval', $arrIds)) . ")";

		return static::findBy($arrColumns, $varId, $arrOptions);
	}

}
