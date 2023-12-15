<?php

namespace DanielGausi\CalendarEditorBundle\Models;

use Contao\CalendarEventsModel;
use Date;

class CalendarEventsModelEdit extends \CalendarEventsModel
{
    public static function findByIdOrAlias($ids, array $options = []): ?CalendarEventsModel
    {
        $t = static::$strTable;
        $arrColumns = !is_numeric($ids) ? array("$t.alias=?") : array("$t.id=?");

        if (!static::isPreviewMode($options)) {
            $time = Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "')";
        }

        return static::findOneBy($arrColumns, $ids, $options);
    }

}
