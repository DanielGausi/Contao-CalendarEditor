<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace DanielGausi\CalendarEditorBundle;


class CalendarEventsModelEdit extends \CalendarEventsModel
{
	/**
	 * Find a published or unpublished event by its ID or alias
	 *
	 * @param mixed $varId      The numeric ID or alias name	 
	 * @param array $arrOptions An optional options array
	 *
	 * @return CalendarEventsModel|null The model or null if there is no event
	 */
	public static function findByIdOrAlias($varId, array $arrOptions=array())
	{		
		$t = static::$strTable;
		$arrColumns = !is_numeric($varId) ? array("$t.alias=?") : array("$t.id=?");

		if (!static::isPreviewMode($arrOptions))
		{
			$time = \Date::floorToMinute();
			$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "')";
		}

		return static::findOneBy($arrColumns, $varId, $arrOptions);
	}

}
