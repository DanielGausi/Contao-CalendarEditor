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
