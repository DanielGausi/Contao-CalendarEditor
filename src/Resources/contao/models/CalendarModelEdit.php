<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
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
