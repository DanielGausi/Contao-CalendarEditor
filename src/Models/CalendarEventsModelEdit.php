<?php

namespace DanielGausi\CalendarEditorBundle\Models;

class CalendarEventsModelEdit extends \CalendarEventsModel
{
    public static function findByIdOrAlias($ids, array $options = []): ?\Contao\CalendarEventsModel
    {
        $t = static::$strTable;
        $arrColumns = !is_numeric($ids) ? array("$t.alias=?") : array("$t.id=?");

        if (!static::isPreviewMode($options)) {
            $time = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "')";
        }

        return static::findOneBy($arrColumns, $ids, $options);
    }

}
