<?php

namespace DanielGausi\CalendarEditorBundle\Services;

use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\StringUtil;
use function DanielGausi\CalendarEditorBundle\EventIsNotElapsed;
use function DanielGausi\CalendarEditorBundle\EventIsNotElapsed2;
use function DanielGausi\CalendarEditorBundle\MidnightTime;
use function DanielGausi\CalendarEditorBundle\UserIsAdmin;
use function DanielGausi\CalendarEditorBundle\UserIsAuthorizedUser;

class CheckAuthService
{
    public function isUserAuthorized($calendar, FrontendUser $user): bool
    {
        if (!$calendar->AllowEdit) {
            return false;
        }

        if (!$calendar->caledit_loginRequired) {
            // if no Login is required, consider the User as "authorized"
            return true;
        }

        if (FE_USER_LOGGED_IN) {
            // Admins are authorized as well ;-)
            if ($this->isUserAdmin($calendar, $user)) {
                return true;
            }

            // Get Groups which are allowed to edit events in this calendar
            $groups = StringUtil::deserialize($calendar->caledit_groups);
            if (is_array($groups) && (count($groups) > 0)
                && (count(array_intersect($groups, $user->groups)) > 0)) {
                return true;
            }
        }

        return false;
    }

    public function isUserAdmin($calendar, FrontendUser $user): bool
    {
        if (!$calendar->AllowEdit) {
            return false;
        }

        if (FE_USER_LOGGED_IN) {
            // Get Admin-Groups which are allowed to edit events in this calendar
            // (Admins are allowed to edit events even if the "only owner"-setting is checked)
            // (Admins are allowed to add events on elapsed days)
            $admin_groups = StringUtil::deserialize($calendar->caledit_adminGroup);
            if (is_array($admin_groups) && (count($admin_groups) > 0)
                && (count(array_intersect($admin_groups, $user->groups)) > 0)) {
                return true;
            }
        }

        return false;
    }

    public function isUserAuthorizedElapsedEvents($calendar, FrontendUser $user): bool
    {
        if (!$calendar->AllowEdit) {
            return false;
        }

        // User is authorized to edit/add elapsed Events if
        // 1.) the User is an Admin for the Calendar or
        // 2.) The User is an Authorized User and the CalendarSetting "only Future" is False
        return ($this->isUserAdmin($calendar, $user)) || ($this->isUserAuthorized($calendar, $user) && (!$calendar->caledit_onlyFuture));
    }

    public function areEditLinksAllowed($calendar, array $event, int $userID, bool $isUserAdmin, bool $isUserMember): bool
    {
        if ($calendar->AllowEdit !== '1') {
            return false;
        }

        if ($isUserAdmin && (!$event['disable_editing'])) {
            return true;
        }

        return
            (
                // Allow only if the editing is NOT disabled in the backend for this event
                (!$event['disable_editing'])
                // Allow only if the User belongs to an authorized Member group
                && ($isUserMember)
                // Allow only if FE User is logged in or the calendar does not requie login
                && (FE_USER_LOGGED_IN || !$calendar->caledit_loginRequired)
                // Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime,
                // && ((!$objCalendar->caledit_onlyFuture) ||  ($currentTime <= $aEvent['startTime']) )

                && ((!$calendar->caledit_onlyFuture) || ($this->isEventNotElapsed($event['startTime'], $event['endTime'])))
                // Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
                && ((!$calendar->caledit_onlyUser) || ($event['fe_user'] == $userID))
            );
    }

    public function EditLinksAreAllowed2($calendar, $event, FrontendUser $user, bool $isUserAdmin, bool $isUserMember): bool
    {
        if (!$calendar->AllowEdit) {
            return false;
        }

        if ($isUserAdmin && (!$event->disable_editing)) {
            return TRUE;
        }

        return
            (
                // Allow only if if the editing is NOT disabled in the backend for this event
                (!$event->disable_editing)
                // Allow only if the User belongs to an authorized Member group
                && ($isUserMember)
                // Allow only if FE User is logged in or the calendar does not requie login
                && (FE_USER_LOGGED_IN || !$calendar->caledit_loginRequired)
                // Allow only if CalendarEditing is not restricted to future events -OR- EventTime is later then CurrentTime,
                //&& ((!$objCalendar->caledit_onlyFuture) ||  (time() <= $objEvent->startTime) )
                && ((!$calendar->caledit_onlyFuture) || (EventIsNotElapsed2($event)))

                // Allow only if CalendarEditing is not restricted to the Owner -OR- The Owner is currently logged in
                && ((!$calendar->caledit_onlyUser) || ($event->fe_user == $user->id))
            );

    }

    public function getMidnightTime(): int
    {
        return mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    }

    public
    function isEventNotElapsed(int $startTime, int $endTime = 0): bool
    {
        if ($endTime === 0) {
            return $this->getMidnightTime() <= $startTime;
        }

        return $this->getMidnightTime() <= $endTime;
    }

    public
    function isDateNotElapsed(int $startDate, int $endDate = null): bool
    {
        if ($endDate === null) {
            return $this->getMidnightTime() <= $startDate;
        }

        return $this->getMidnightTime() <= $endDate;
    }
}