<?php

namespace DanielGausi\CalendarEditorBundle\Modules;

use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use DanielGausi\CalendarEditorBundle\Models\CalendarEventsModelEdit;
use DanielGausi\CalendarEditorBundle\Models\CalendarModelEdit;
use DanielGausi\CalendarEditorBundle\Services\CheckAuthService;

class ModuleEventEditor extends \Events
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'eventEdit_default';
    protected string $errorString = '';
    protected array $allowedCalendars = [];

    /**
     * generate Module
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### EVENT EDITOR ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected(StringUtil::deserialize($this->cal_calendar));

        // Return if there are no calendars
        if (!is_array($this->cal_calendar) || count($this->cal_calendar) < 1) {
            return '';
        }

        return parent::generate();
    }


    /**
     * Returns an Event-URL for a given Event-Editor and a given Event
     **/
    public function getEditorFrontendURLForEvent($event): ?string
    {
        return $this->generateEventUrl($event);
    }

    public function addTinyMCE($str): void
    {
        if (!empty($str)) {
            $this->rteFields = 'ctrl_details,ctrl_teaser,teaser';
            // Fallback to English if the user language is not supported
            $this->language = 'en';

            $strFile = sprintf('%s/vendor/danielgausi/contao-calendareditor-bundle/src/Resources/contao/tinyMCE/%s.php', TL_ROOT, $str);
            if (file_exists(TL_ROOT . '/assets/tinymce4/js/langs/' . $GLOBALS['TL_LANGUAGE'] . '.js')) {
                $this->language = $GLOBALS['TL_LANGUAGE'];
            }

            if (!file_exists($strFile)) {
                echo(sprintf('Cannot find rich text editor configuration file "%s"', $strFile));
            } else {
                ob_start();
                include($strFile);
                $GLOBALS['TL_HEAD'][] = ob_get_contents();
                ob_end_clean();
            }
        }
    }

    /**
     * Get the calendars the user is allowed to edit
     * These calendars will appear in the selection-field in the edit-form (if there is not only one)
     */
    public function getCalendars($user): array
    {
        /** @var CheckAuthService $checkAuthService */
        // get all the calendars supported by this module
        $calendarModels = CalendarModelEdit::findByIds($this->cal_calendar);
        // Check these calendars, whether the current user is allowed to edit them
        $calendars = [];

        if (null === $calendarModels) {
            // return the empty array
            return $calendars;
        } else {
            // fill the Allowed-Calendars-Array with proper calendars
            foreach ($calendarModels as $calendarModel) {
                if (System::getContainer()->get('caledit.service.auth')->isUserAuthorized($calendarModel, $user)) {
                    $calendars[] = $calendarModel;
                }
            }
        }
        return $calendars;
    }

    /**
     * Check user rights for editing on different stages of the formular
     * The first step is always to get an Calendar-object frome the array of calendars by the
     * current events Pid (= the ID of the calendar)
     **/
    public function getCalendarObjectFromPID($pid)
    {
        foreach ($this->allowedCalendars as $objCalendar) {
            if ($pid == $objCalendar->id) {
                return $objCalendar;
            }
        }
    }

    public function UserIsToAddCalendar($user, $pid)
    {
        $objCalendar = $this->getCalendarObjectFromPID($pid);
        if (NULL === $objCalendar) {
            return false;
        } else {
            /** @var CheckAuthService $checkAuthService */
            return System::getContainer()->get('caledit.service.auth')->isUserAuthorized($objCalendar, $user);
        }
    }

    public function checkValidDate($calendarID, $objStart, $objEnd)
    {
        /** @var CheckAuthService $checkAuthService */
        $checkAuthService = System::getContainer()->get('caledit.service.auth');

        $objCalendar = $this->getCalendarObjectFromPID($calendarID);
        if (NULL === $objCalendar) {
            return false;
        }
        $tmpStartDate = strtotime($objStart->__get('value'));
        $tmpEndDate = strtotime($objEnd->__get('value'));
        if ($tmpEndDate === false) $tmpEndDate = null;

        if ((!$objCalendar->caledit_onlyFuture) || $checkAuthService->isUserAdmin($objCalendar, $this->User)) {
            // elapsed events can be edited, or user is an admin
            return true;
        } else {
            // editing elapsed events is denied and user is not an admin
            //$isValid = ($newDate >= time());
            $isValid = $checkAuthService->isDateNotElapsed($tmpStartDate, $tmpEndDate);
            if (!$isValid) {
                if (!$tmpEndDate && ($checkAuthService->getMidnightTime() > $tmpStartDate)) {
                    $objStart->addError($GLOBALS['TL_LANG']['MSC']['caledit_formErrorElapsedDate']);
                }
                if ($tmpEndDate && ($checkAuthService->getMidnightTime() > $tmpEndDate)) {
                    $objEnd->addError($GLOBALS['TL_LANG']['MSC']['caledit_formErrorElapsedDate']);
                }
            }
            return $isValid;
        }
    }

    public function allDatesAllowed($calendarID)
    {
        $objCalendar = $this->getCalendarObjectFromPID($calendarID);
        if (NULL === $objCalendar) {
            return false;
        }

        if ((!$objCalendar->caledit_onlyFuture) || (System::getContainer()->get('caledit.service.auth')->isUserAdmin($objCalendar, $this->User))) {
            // elapsed events can be edited, or user is an admin
            return true;
        } else {
            return false;
        }
    }


    /**
     * check, whether the user is allowed to edit the specified Event
     * This is called when the user has general access to at least one calendar
     * But: We need to check whether he is allowed to edit this special event
     *       - is he in the group/admingroup in the event's calendar?
     *       - is he the owner of the event or !caledit_onlyUser
     * used in the compile-method at the beginning
     */
    public function checkUserEditRights($user, $eventID, $currentObjectData): bool
    {
        /** @var CheckAuthService $checkAuthService */
        $checkAuthService = System::getContainer()->get('caledit.service.auth');

        // if no event is specified: ok, FE user can add new events :D
        if (!$eventID) {
            return true;
        }
        $objCalendar = $this->getCalendarObjectFromPID($currentObjectData->pid);
        if (NULL === $objCalendar) {
            $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_unexpected'] . $currentObjectData->pid;
            return false; // Event not found or something else is wrong
        }

        if (!$objCalendar->AllowEdit) {
            $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'] . '(checkUserEditRights)';
            return false;
        }

        // check calendar settings 
        if ($checkAuthService->isUserAuthorized($objCalendar, $user)) {
            // if the editing is disabled in the BE: Deny editing in the FE
            if ($currentObjectData->disable_editing) {
                $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_DisabledEvent'];
                return false;
            }

            $userIsAdmin = $checkAuthService->isUserAdmin($objCalendar, $user);
            //if (!$userIsAdmin && ($CurrentObjectData->startTime <= time()) && ($objCalendar->caledit_onlyFuture)){
            if (!$userIsAdmin
                && (!$checkAuthService->isDateNotElapsed($currentObjectData->startTime, $currentObjectData->endTime))
                //($CurrentObjectData->startTime <= time())
                && ($objCalendar->caledit_onlyFuture)) {
                $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_NoPast'];
                return false;
            }

            $result = ((!$objCalendar->caledit_onlyUser) || ((FE_USER_LOGGED_IN) && ($userIsAdmin || ($user->id == $currentObjectData->fe_user))));
            if (!$result) {
                $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_OnlyUser'];
            }

            return $result;
        } else {
            $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_UnauthorizedUser'];
            return false; // user is not allowed to edit events here
        }
    }

    public function generateRedirect($userSetting, $DBid)
    {
        switch ($userSetting) {
            case "":
                $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
                // Get current "jumpTo" page
                $objPage = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
                    ->limit(1)
                    ->execute($this->jumpTo);

                if ($objPage->numRows) {
                    $jt = $this->generateFrontendUrl($objPage->row());
                }
                $this->redirect($jt, 301);
                break;

            case "new":
                $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
                $this->redirect($jt, 301);
                break;

            case "view":
                $currentEventObject = CalendarEventsModelEdit::findByIdOrAlias($DBid);

                if ($currentEventObject->published) {
                    $jt = $this->generateEventUrl($currentEventObject);
                    $this->redirect($jt, 301);
                } else {
                    // event is not published, so show it in the editor again
                    $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
                    $jt .= '?edit=' . $DBid;
                    $this->redirect($jt, 301);
                }
                break;

            case "edit":
                $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
                $jt .= '?edit=' . $DBid;
                $this->redirect($jt, 301);
                break;

            case "clone":
                $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
                $jt .= '?clone=' . $DBid;
                $this->redirect($jt, 301);
                break;
        }
    }


    public function getContentElements($eventID, &$contentID, &$contentData)
    {
        // get Content Elements
        $objElement = \ContentModel::findPublishedByPidAndTable($eventID, 'tl_calendar_events');

        // analyse content elements:
        // we will use the first element of type "text", discard the others (but set a warning in the template)
        $this->Template->ContentWarning = '';
        $this->Template->ImageWarning = '';
        if ($objElement !== null) {
            $ContentCount = 0;
            $TextFound = false;
            while ($objElement->next()) {
                $ContentCount++;
                if (($objElement->type == 'text') and (!$TextFound)) {
                    $contentData['text'] = $objElement->text;
                    $contentID = $objElement->id;
                    $TextFound = true;
                    if ($objElement->addImage) {
                        // we cannot modify "add image" with this module.
                        // note: A "headline" will be deleted without warning.
                        $this->Template->ImageWarning = $GLOBALS['TL_LANG']['MSC']['caledit_ContentElementWithImage'];
                    }
                }
            }
            if ($ContentCount > 1) {
                $this->Template->ContentWarning = $GLOBALS['TL_LANG']['MSC']['caledit_MultipleContentElements'];
            }
        }
    }


    public function getEventInformation($currentEventObject, &$NewEventData)
    {
        // Fill fields with data from $currentEventObject
        $NewEventData['startDate'] = $currentEventObject->startDate;
        $NewEventData['endDate'] = $currentEventObject->endDate;
        if ($currentEventObject->addTime) {
            $NewEventData['startTime'] = $currentEventObject->startTime;
            $NewEventData['endTime'] = $currentEventObject->endTime;
            if ($NewEventData['startTime'] == $NewEventData['endTime']) {
                $NewEventData['endTime'] = '';
            }
        } else {
            $NewEventData['startTime'] = '';
            $NewEventData['endTime'] = '';
        }
        $NewEventData['title'] = $currentEventObject->title;
        $NewEventData['teaser'] = $currentEventObject->teaser;
        $NewEventData['location'] = $currentEventObject->location;
        $NewEventData['cssClass'] = $currentEventObject->cssClass;
        $NewEventData['pid'] = $currentEventObject->pid;
        $NewEventData['published'] = $currentEventObject->published;
        $NewEventData['alias'] = $currentEventObject->alias;

        $this->Template->CurrentTitle = $currentEventObject->title;
        $this->Template->CurrentDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $currentEventObject->startDate);
        $this->Template->CurrentPublished = $currentEventObject->published;

        if ($currentEventObject->published) {
            $this->Template->CurrentEventLink = $this->generateEventUrl($currentEventObject);
            $this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_publishedEvent'];
        } else {
            $this->Template->CurrentEventLink = '';
            $this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_unpublishedEvent'];
        }
    }

    public function addDatePicker(&$field)
    {
        $field['inputType'] = 'calendarfield';
        if (strlen($this->caledit_dateIncludeCSSTheme) > 0) {
            $field['eval']['dateIncludeCSS'] = '1';
            $field['eval']['dateIncludeCSSTheme'] = $this->caledit_dateIncludeCSSTheme;
        } else {
            $field['eval']['dateIncludeCSS'] = '0';
            $field['eval']['dateIncludeCSSTheme'] = '';
        }
        $field['eval']['dateDirection'] = $this->caledit_dateDirection;
        if ($this->caledit_dateImage) {
            $field['eval']['dateImage'] = '1';
        }
        if ($this->caledit_dateImageSRC) {
            $field['eval']['dateImageSRC'] = $this->caledit_dateImageSRC;
        }
    }

    public function AliasExists($suggestedAlias)
    {
        $objAlias = $this->Database->prepare("SELECT id FROM tl_calendar_events WHERE alias=?")
            ->execute($suggestedAlias);
        if ($objAlias->numRows) {
            return true;
        } else {
            return false;
        }
    }

    public function generateAlias($varValue)
    {
        // maximum length of alias in the DB: 128 chars
        // we use only 110 chars here, as we may add "-<ID>" in case of a collision
        $varValue = substr(standardize($varValue), 0, 110);

        if ($this->AliasExists($varValue)) {
            // alias already exists, we have to modify it.
            // 1st try: Add the ID of the event (which is currently not in the DB, therefore +1 at the end)
            $maxI = $this->Database->prepare("SELECT MAX(id) as id FROM tl_calendar_events")
                ->limit(1)
                ->execute();
            $newID = $maxI->id + 1;

            $varValue .= '-' . $newID;
            // if even this modified alias exists: use random alias, with ID as prefix
            // we do not increase the ID here, nor do we add another random number,
            // as there may be some issues with the maximum length of the alias (?)
            while ($this->AliasExists($varValue)) {
                $randID = mt_rand();
                $varValue = $newID . '-' . $randID;
            }
        }
        return $varValue;
    }

    public function saveToDB($eventData, $oldId, array $contentData, $oldContentId)
    {
        if ($oldId === '') {
            // create new alias
            $eventData['alias'] = $this->generateAlias($eventData['title']);
        }

        // important (otherwise details/teaser will be mixed up in calendars or event lists)
        $eventData['source'] = 'default';

        // needed later!
        $startDate = new \Date($eventData['startDate'], $GLOBALS['TL_CONFIG']['dateFormat']);

        $eventData['tstamp'] = $startDate->tstamp;

        // Dealing with empty enddates, Start/endtimes ...
        if (trim($eventData['endDate']) != '') {
            // an enddate is given
            $endDateStr = $eventData['endDate'];
            $endDate = new \Date($eventData['endDate'], $GLOBALS['TL_CONFIG']['dateFormat']);
            $eventData['endDate'] = $endDate->tstamp;
        } else {
            // needed later
            $endDateStr = $eventData['startDate'];
            // $endDate = $startDate;
            // no enddate is given. => Set it to NULL
            $eventData['endDate'] = NULL;
        }

        $startTimeStr = $eventData['startTime'];
        if (trim($eventData['startTime']) == '') {
            // Dont add time
            $useTime = false;
            $eventData['addTime'] = '';
            $eventData['startTime'] = $startDate->tstamp;
        } else {
            // Add time to the event
            $useTime = true;
            $eventData['addTime'] = '1';
            $startTime = new \Date($eventData['startDate'] . ' ' . $eventData['startTime'], $GLOBALS['TL_CONFIG']['dateFormat'] . ' ' . $GLOBALS['TL_CONFIG']['timeFormat']);
            $eventData['startTime'] = $startTime->tstamp;
        }

        $eventData['startDate'] = $startDate->tstamp;

        if (trim($eventData['endTime']) == '') {
            // if no endtime is given: set endtime = starttime
            $dateString = $endDateStr . ' ' . $startTimeStr;
        } else {
            if (!$useTime) {
                $eventData['endTime'] = strtotime($endDateStr . ' ' . $eventData['endTime']);
            }
            $dateString = $endDateStr . ' ' . $eventData['endTime'];
        }
        $endTime = new \Date($dateString, $GLOBALS['TL_CONFIG']['datimFormat']);
        $eventData['endTime'] = $endTime->tstamp;


        // here: CALL Hooks with $eventData
        if (array_key_exists('prepareCalendarEditData', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['prepareCalendarEditData'])) {
            foreach ($GLOBALS['TL_HOOKS']['prepareCalendarEditData'] as $key => $callback) {
                $this->import($callback[0]);
                $eventData = $this->{$callback[0]}->{$callback[1]}($eventData);
            }
        }

        if ($oldId === '') {
            // create new entry
            $new_cid = $this->Database->prepare('INSERT INTO tl_calendar_events %s')->set($eventData)->execute()->insertId;
            $contentData['pid'] = $new_cid;
            $returnID = $new_cid;
        } else {
            // update existing entry
            $this->Database->prepare("UPDATE tl_calendar_events %s WHERE id=?")->set($eventData)->execute($oldId);
            $contentData['pid'] = $oldId;
            $returnID = $oldId;
        }

        $contentData['ptable'] = 'tl_calendar_events';
        $contentData['type'] = 'text';
        // set the headline in the Content Element to ""
        $contentData['headline'] = 'a:2:{s:4:"unit";s:2:"h1";s:5:"value";s:0:"";}';

        if (isset($contentData['text'])) {
            // content 'text' is set, so we need to write something into the Database
            if ($oldContentId === '') {
                // create new entry
                $contentData['tstamp'] = time();
                $this->Database->prepare('INSERT INTO tl_content %s')->set($contentData)->execute();
            } else {
                // update existing entry
                $this->Database->prepare("UPDATE tl_content %s WHERE id=?")->set($contentData)->execute($oldContentId);
            }
        } else {
            // content is empty, so we need to delete the existing content element
            if ($oldContentId) {
                $this->Database->prepare("DELETE FROM tl_content WHERE id=?")->execute($oldContentId);
            }
        }
        $this->import('Calendar');
        $this->Calendar->generateFeed($eventData['pid']);

        return $returnID;
    }


    protected function handleEdit($editID, $currentEventObject)
    {
        $this->strTemplate = $this->caledit_template;

        $this->Template = new \FrontendTemplate($this->strTemplate);

        // 1. Get Data from post/get
        $newDate = $this->Input->get('add');

        $NewEventData = [];
        $NewContentData = [];
        $NewEventData['startDate'] = $newDate;

        $published = $currentEventObject?->published;

        if ($editID) {
            // get a proper Content-Element
            $this->getContentElements($editID, $contentID, $NewContentData);
            // get the rest of the event data
            $this->getEventInformation($currentEventObject, $NewEventData);

            if ($this->caledit_allowDelete) {
                // add a "Delete this event"-Link
                $del = str_replace('?edit=', '?delete=', $this->Environment->request);
                $this->Template->deleteRef = $del;
                $this->Template->deleteLabel = $GLOBALS['TL_LANG']['MSC']['caledit_deleteLabel'];
                $this->Template->deleteTitle = $GLOBALS['TL_LANG']['MSC']['caledit_deleteTitle'];
            }

            if ($this->caledit_allowClone) {
                $cln = str_replace('?edit=', '?clone=', $this->Environment->request);
                $this->Template->cloneRef = $cln;
                $this->Template->cloneLabel = $GLOBALS['TL_LANG']['MSC']['caledit_cloneLabel'];
                $this->Template->cloneTitle = $GLOBALS['TL_LANG']['MSC']['caledit_cloneTitle'];
            }

            $this->Template->CurrentPublished = $published;

            if ($published && !$this->caledit_allowPublish) {
                // editing a published event with no publish-rights
                // will hide the event again
                $published = '';
            }
        } else {
            $this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_newEvent'];
        }

        $saveAs = '0';
        $jumpToSelection = '';

        // after this: Overwrite it with the post data
        if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
            $NewEventData['startDate'] = $this->Input->post('startDate');
            $NewEventData['endDate'] = $this->Input->post('endDate');
            $NewEventData['startTime'] = $this->Input->post('startTime');
            $NewEventData['endTime'] = $this->Input->post('endTime');
            $NewEventData['title'] = $this->Input->post('title');
            $NewEventData['location'] = $this->Input->post('location');
            $NewEventData['teaser'] = $this->Input->postHtml('teaser', true);
            $NewContentData['text'] = $this->Input->postHtml('details', true);
            $NewEventData['cssClass'] = $this->Input->post('cssClass');
            $NewEventData['pid'] = $this->Input->post('pid');
            $NewEventData['published'] = $this->Input->post('published');
            $saveAs = $this->Input->post('saveAs') ?? 0;
            $jumpToSelection = $this->Input->post('jumpToSelection');

            if ($published && !$this->caledit_allowPublish) {
                // this should never happen, except the FE user is manipulating
                // the POST-Data with some evil HackerToolz ;-)
                $fatalError = $GLOBALS['TL_LANG']['MSC']['caledit_NoPublishAllowed'] . ' (POST data invalid)';
                $this->Template->FatalError = $fatalError;
                return;
            }

            if (empty($NewEventData['pid'])) {
                // set default value
                $NewEventData['pid'] = $this->allowedCalendars[0]->id; //['id'];
            };

            if (!$this->UserIsToAddCalendar($this->User, $NewEventData['pid'])) {
                // this should never happen, except the FE user is manipulating
                // the POST with some evil HackerToolz. ;-)
                $fatalError = $GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'] . ' (POST data invalid)';
                $this->Template->FatalError = $fatalError;
                return;
            }
        }

        $mandfields = deserialize($this->caledit_mandatoryfields);
        $mandTeaser = (is_array($mandfields) && array_intersect(array('teaser'), $mandfields));
        $mandLocation = (is_array($mandfields) && array_intersect(array('location'), $mandfields));
        $mandDetails = (is_array($mandfields) && array_intersect(array('details'), $mandfields));
        $mandStarttime = (is_array($mandfields) && array_intersect(array('starttime'), $mandfields));
        $mandCss = (is_array($mandfields) && array_intersect(array('css'), $mandfields));
        // fill template with fields ...
        $fields = array();
        $fields['startDate'] = array(
            'name' => 'startDate',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_startdate'],
            'inputType' => 'text', // or: 'calendarfield' (see below),
            'value' => $NewEventData['startDate'],
            'eval' => array('rgxp' => 'date',
                'mandatory' => true,
                'decodeEntities' => true)
        );

        $fields['endDate'] = array(
            'name' => 'endDate',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_enddate'],
            'inputType' => 'text',
            'value' => $NewEventData['endDate'] ?? null,
            'eval' => array('rgxp' => 'date', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true)
        );

        if ($this->caledit_useDatePicker) {
            $this->addDatePicker($fields['startDate']);
            $this->addDatePicker($fields['endDate']);
        }

        $fields['startTime'] = [
            'name' => 'startTime',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_starttime'],
            'inputType' => 'text',
            'value' => $NewEventData['startTime'] ?? '',
            'eval' => ['rgxp' => 'time', 'mandatory' => $mandStarttime, 'maxlength' => 128, 'decodeEntities' => true]
        ];

        $fields['endTime'] = [
            'name' => 'endTime',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_endtime'],
            'inputType' => 'text',
            'value' => $NewEventData['endTime'] ?? '',
            'eval' => ['rgxp' => 'time', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true]
        ];

        $fields['title'] = [
            'name' => 'title',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_title'],
            'inputType' => 'text',
            'value' => $NewEventData['title'] ?? '',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true]
        ];

        $fields['location'] = [
            'name' => 'location',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_location'],
            'inputType' => 'text',
            'value' => $NewEventData['location'] ?? '',
            'eval' => ['mandatory' => $mandLocation, 'maxlength' => 255, 'decodeEntities' => true]
        ];

        $fields['teaser'] = [
            'name' => 'teaser',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_teaser'],
            'inputType' => 'textarea',
            'value' => $NewEventData['teaser'] ?? '',
            'eval' => ['mandatory' => $mandTeaser, 'rte' => 'tinyMCE', 'allowHtml' => true]
        ];

        $fields['details'] = [
            'name' => 'details',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_details'],
            'inputType' => 'textarea',
            'value' => $NewContentData['text'] ?? '',
            'eval' => ['mandatory' => $mandDetails, 'rte' => 'tinyMCE', 'allowHtml' => true]
        ];

        if (count($this->allowedCalendars) > 1) {
            // Show allowed Calendars in a select-field
            $pref = [];
            $popt = [];
            foreach ($this->allowedCalendars as $cal) {
                $popt[] = $cal->id;
                $pref[$cal->id] = $cal->title;
            }
            $fields['pid'] = [
                'name' => 'pid',
                'label' => $GLOBALS['TL_LANG']['MSC']['caledit_pid'],
                'inputType' => 'select',
                'options' => $popt,
                'value' => $NewEventData['pid'] ?? $cal->id,
                'reference' => $pref,
                'eval' => ['mandatory' => true]
            ];
        }

        $xx = $this->caledit_alternateCSSLabel;
        $cssLabel = (empty($xx)) ? $GLOBALS['TL_LANG']['MSC']['caledit_css'] : $this->caledit_alternateCSSLabel;

        if ($this->caledit_usePredefinedCss) {
            $cssValues = StringUtil::deserialize($this->caledit_cssValues);

            $ref = [];
            $opt = [];


            foreach ($cssValues as $cssv) {
                $opt[] = $cssv['value'];
                $ref[$cssv['value']] = $cssv['label'];
            }


            $fields['cssClass'] = [
                'name' => 'cssClass',
                'label' => $cssLabel,
                'inputType' => 'select',
                'options' => $opt,
                'value' => $NewEventData['cssClass'] ?? '',
                'reference' => $ref,
                'eval' => ['mandatory' => $mandCss, 'includeBlankOption' => true, 'maxlength' => 128, 'decodeEntities' => true]
            ];
        } else {
            $fields['cssClass'] = [
                'name' => 'cssClass',
                'label' => $cssLabel,
                'inputType' => 'text',
                'value' => $NewEventData['cssClass'] ?? '',
                'eval' => ['mandatory' => $mandCss, 'maxlength' => 128, 'decodeEntities' => true]
            ];
        }

        if ($this->caledit_allowPublish) {
            $fields['published'] = [
                'name' => 'published',
                'label' => '', // $GLOBALS['TL_LANG']['MSC']['caledit_published'],
                'inputType' => 'checkbox',
                'value' => $NewEventData['published'] ?? ''
            ];
            $fields['published']['options']['1'] = $GLOBALS['TL_LANG']['MSC']['caledit_published'];
        }

        if ($editID) {
            // create a checkbox "save as copy"
            $fields['saveAs'] = [
                'name' => 'saveAs',
                'label' => '', // $GLOBALS['TL_LANG']['MSC']['caledit_saveAs']
                'inputType' => 'checkbox',
                'value' => $saveAs
            ];
            $fields['saveAs']['options']['1'] = $GLOBALS['TL_LANG']['MSC']['caledit_saveAs'];
        }

        if (!FE_USER_LOGGED_IN) {
            $fields['captcha'] = [
                'name' => 'captcha',
                'inputType' => 'captcha',
                'eval' => ['mandatory' => true, 'customTpl' => 'form_captcha_calendar-editor']
            ];
        }

        // create jump-to-selection
        $JumpOpts = ['new', 'view', 'edit', 'clone'];
        $JumpRefs = [
            'new' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToNew'],
            'view' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToView'],
            'edit' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToEdit'],
            'clone' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToClone']
        ];
        $fields['jumpToSelection'] = [
            'name' => 'jumpToSelection',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpWhatsNext'],
            'inputType' => 'select',
            'options' => $JumpOpts,
            'value' => $jumpToSelection,
            'reference' => $JumpRefs,
            'eval' => ['mandatory' => false, 'includeBlankOption' => true, 'maxlength' => 128, 'decodeEntities' => true]
        ];

        // here: CALL Hooks with $NewEventData, $currentEventObject, $fields
        if (array_key_exists('buildCalendarEditForm', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['buildCalendarEditForm'])) {
            foreach ($GLOBALS['TL_HOOKS']['buildCalendarEditForm'] as $key => $callback) {
                $this->import($callback[0]);
                $arrResult = $this->{$callback[0]}->{$callback[1]}($NewEventData, $fields, $currentEventObject, $editID);
                if (is_array($arrResult) && count($arrResult) > 1) {
                    $NewEventData = $arrResult['NewEventData'];
                    $fields = $arrResult['fields'];
                }
            }
        }

        $arrWidgets = [];
        // Initialize widgets
        $doNotSubmit = false;
        foreach ($fields as $field) {
            $strClass = $GLOBALS['TL_FFL'][$field['inputType']];

            $field['eval']['required'] = $field['eval']['mandatory'] ?? false;

            // from http://pastebin.com/HcjkHLQK
            // via https://github.com/contao/core/issues/5086
            // Convert date formats into timestamps (check the eval setting first -> #3063)
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $rgxp = $field['eval']['rgxp'] ?? '';
                if (($rgxp == 'date' || $rgxp == 'time' || $rgxp == 'datim') && $field['value'] != '') {
                    $objDate = new \Date(Input::post($field['name']), $GLOBALS['TL_CONFIG'][$rgxp . 'Format']);
                    $field['value'] = $objDate->tstamp;
                }
            }

            $objWidget = new $strClass($this->prepareForWidget($field, $field['name'], $field['value']));
            // Validate widget
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $objWidget->validate();
                if ($objWidget->hasErrors()) {
                    $doNotSubmit = true;
                }
            }
            $arrWidgets[$field['name']] = $objWidget;
        }
        $arrWidgets['startDate']->parse();
        $arrWidgets['endDate']->parse();


        // Check, whether the user is allowed to edit past events
        // or the date is in the future
        //$tmpStartDate = strtotime($arrWidgets['startDate']->__get('value'));
        //$tmpEndDate = strtotime($arrWidgets['endDate']->__get('value'));

        $validDate = $this->checkValidDate($NewEventData['pid'] ?? 0, $arrWidgets['startDate'], $arrWidgets['endDate']);
        if (!$validDate) {
            // modification of the widget is done in checkValidDate
            $doNotSubmit = true;
        }

        $this->Template->submit = $GLOBALS['TL_LANG']['MSC']['caledit_saveData'];
        $this->Template->calendars = $this->allowedCalendars;

        if ((!$doNotSubmit) && ($this->Input->post('FORM_SUBMIT') == 'caledit_submit')) {
            // everything seems to be ok, so we can add the POST Data
            // into the Database
            if (!FE_USER_LOGGED_IN) {
                $NewEventData['fe_user'] = ''; // no user
            } else {
                $NewEventData['fe_user'] = $this->User->id; // set the FE_user here
            }

            if (is_null($NewEventData['published'])) {
                $NewEventData['published'] = '';
            }

            if (is_null($NewEventData['location'])) {
                $NewEventData['location'] = '';
            }

            if ($saveAs === 0) {
                $DBid = $this->saveToDB($NewEventData, '', $NewContentData, '');
            } else {
                $DBid = $this->saveToDB($NewEventData, $editID, $NewContentData, $contentID);
            }

            // Send Notification EMail
            if ($this->caledit_sendMail) {
                if ($saveAs) {
                    $this->SendNotificationMail($NewEventData, '', $this->User->username, '');
                } else {
                    $this->SendNotificationMail($NewEventData, $editID, $this->User->username, '');
                }
            }

            $this->generateRedirect($jumpToSelection, $DBid);
        } else {
            // Do NOT Submit
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $this->Template->InfoClass = 'tl_error';
                if ($this->Template->InfoMessage == '') {
                    $this->Template->InfoMessage = $GLOBALS['TL_LANG']['MSC']['caledit_error'];
                } // else: keep the InfoMesage as set before
            }
            $this->Template->fields = $arrWidgets;
        }
    }

    protected function HandleDelete($currentEventObject)
    {
        $this->strTemplate = $this->caledit_delete_template;
        $this->Template = new \FrontendTemplate($this->strTemplate);

        if (!$this->caledit_allowDelete) {
            $this->Template->FatalError = $GLOBALS['TL_LANG']['MSC']['caledit_NoDelete'];
            return;
        }

        // add a "Edit this event"-Link
        $del = str_replace('?delete=', '?edit=', $this->Environment->request);
        $this->Template->editRef = $del;
        $this->Template->editLabel = $GLOBALS['TL_LANG']['MSC']['caledit_editLabel'];
        $this->Template->editTitle = $GLOBALS['TL_LANG']['MSC']['caledit_editTitle'];

        if ($this->caledit_allowClone) {
            $cln = str_replace('?delete=', '?clone=', $this->Environment->request);
            $this->Template->cloneRef = $cln;
            $this->Template->cloneLabel = $GLOBALS['TL_LANG']['MSC']['caledit_cloneLabel'];
            $this->Template->cloneTitle = $GLOBALS['TL_LANG']['MSC']['caledit_cloneTitle'];
        }

        // Fill fields with data from $currentEventObject
        $startDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $currentEventObject->startDate);

        $pid = $currentEventObject->pid;
        $id = $currentEventObject->id;
        $published = $currentEventObject->published;

        $this->Template->CurrentEventLink = $this->generateEventUrl($currentEventObject);

        $this->Template->CurrentTitle = $currentEventObject->title;
        $this->Template->CurrentDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $currentEventObject->startDate);

        if ($published == '') {
            $this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_unpublishedEvent'];
        } else {
            $this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_publishedEvent'];
        }
        $this->Template->CurrentPublished = $published;

        // create captcha field
        $captchaField = [
            'name' => 'captcha',
            'inputType' => 'captcha',
            'eval' => ['mandatory' => true, 'customTpl' => 'form_captcha_calendar-editor']
        ];

        $arrWidgets = array();
        // Initialize widgets
        $doNotSubmit = false;
        $strClass = $GLOBALS['TL_FFL'][$captchaField['inputType']];

        $captchaField['eval']['required'] = $captchaField['eval']['mandatory'];
        $objWidget = new $strClass($this->prepareForWidget($captchaField, $captchaField['name']));
        // Validate widget
        if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
            $objWidget->validate();
            if ($objWidget->hasErrors()) {
                $doNotSubmit = true;
            }
        }
        $arrWidgets[$captchaField['name']] = $objWidget;

        $this->Template->deleteHint = $GLOBALS['TL_LANG']['MSC']['caledit_deleteHint'];
        $this->Template->submit = $GLOBALS['TL_LANG']['MSC']['caledit_deleteData'];

        $this->Template->deleteWarning = $GLOBALS['TL_LANG']['MSC']['caledit_deleteWarning'];


        if ((!$doNotSubmit) && ($this->Input->post('FORM_SUBMIT') == 'caledit_submit')) {
            // everything seems to be ok, so we can delete this event

            // for notification e-mail
            $oldEventData = array(
                'startDate' => $startDate,
                'title' => $currentEventObject->title,
                'published' => $published);

            // Delete all content elements
            $objDelete = $this->Database->prepare("DELETE FROM tl_content WHERE ptable='tl_calendar_events' AND pid=?")->execute($id);
            // Delete event itself
            $objDelete = $this->Database->prepare("DELETE FROM tl_calendar_events WHERE id=?")->execute($id);

            $this->import('Calendar');
            $this->Calendar->generateFeed($pid);

            // Send Notification EMail
            if ($this->caledit_sendMail) {
                $this->SendNotificationMail($oldEventData, -1, $this->User->username, '');
            }

            $this->generateRedirect('', ''); // jump to the default page
        } else {
            // Do NOT Submit
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $this->Template->InfoClass = 'tl_error';
                $this->Template->InfoMessage = $GLOBALS['TL_LANG']['MSC']['caledit_error'];
            }
            $this->Template->fields = $arrWidgets;
        }
        $this->Template->fields = $arrWidgets;
    }

    protected function HandleClone($currentEventObject)
    {
        $this->strTemplate = $this->caledit_clone_template;
        $this->Template = new \FrontendTemplate($this->strTemplate);

        $pid = $currentEventObject->pid;
        $currentID = $currentEventObject->id;
        $currentEventData = array();
        $currentContentData = array();
        $contentID = '';

        // add a "Edit this event"-Link
        $del = str_replace('?clone=', '?edit=', $this->Environment->request);
        $this->Template->editRef = $del;
        $this->Template->editLabel = $GLOBALS['TL_LANG']['MSC']['caledit_editLabel'];
        $this->Template->editTitle = $GLOBALS['TL_LANG']['MSC']['caledit_editTitle'];

        if ($this->caledit_allowDelete) {
            // add a "Delete this event"-Link
            $del = str_replace('?clone=', '?delete=', $this->Environment->request);
            $this->Template->deleteRef = $del;
            $this->Template->deleteLabel = $GLOBALS['TL_LANG']['MSC']['caledit_deleteLabel'];
            $this->Template->deleteTitle = $GLOBALS['TL_LANG']['MSC']['caledit_deleteTitle'];
        }

        // get a proper Content-Element
        $this->getContentElements($currentID, $contentID, $currentContentData);
        // get all the data from the current event...
        $this->getEventInformation($currentEventObject, $currentEventData);

        $this->Template->CloneWarning = $GLOBALS['TL_LANG']['MSC']['caledit_CloneWarning'];

        // publishing information
        $published = $currentEventObject->published;
        $this->Template->CurrentPublished = $published;

        if ($published && !$this->caledit_allowPublish) {
            // cloning a published event without publish-rights will result in a lot of unpublished events
            $published = '';
        }

        // current event stored - prepare the formular
        $newDates = array();
        $fields = array();
        $jumpToSelection = '';

        if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
            for ($i = 1; $i <= 10; $i++) {
                $newDates['start' . $i] = $this->Input->post('start' . $i);
                $newDates['end' . $i] = $this->Input->post('end' . $i);
            }
            $jumpToSelection = $this->Input->post('jumpToSelection');
        } else {
            for ($i = 1; $i <= 10; $i++) {
                $newDates['start' . $i] = '';
                $newDates['end' . $i] = '';
            }
        }

        // create fields
        for ($i = 1; $i <= 10; $i++) {
            // start dates
            $fields['start' . $i] = array(
                'name' => 'start' . $i,
                'label' => $GLOBALS['TL_LANG']['MSC']['caledit_startdate'],
                'inputType' => 'text',
                'value' => $newDates['start' . $i],
                'eval' => array('rgxp' => 'date', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true)
            );
            // end dates
            $fields['end' . $i] = array(
                'name' => 'end' . $i,
                'label' => $GLOBALS['TL_LANG']['MSC']['caledit_enddate'],
                'inputType' => 'text',
                'value' => $newDates['end' . $i],
                'eval' => array('rgxp' => 'date', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true)
            );

            if ($this->caledit_useDatePicker) {
                $this->addDatePicker($fields['start' . $i]);
                $this->addDatePicker($fields['end' . $i]);
            }
        }

        if (!FE_USER_LOGGED_IN) {
            $fields['captcha'] = array(
                'name' => 'captcha',
                'inputType' => 'captcha',
                'eval' => array('mandatory' => true, 'customTpl' => 'form_captcha_calendar-editor')
            );
        }

        // create jump-to-selection
        $JumpOpts = array('new', 'view', 'edit', 'clone');
        $JumpRefs = array(
            'new' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToNew'],
            'view' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToView'],
            'edit' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToEdit'],
            'clone' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpToClone']
        );
        $fields['jumpToSelection'] = array(
            'name' => 'jumpToSelection',
            'label' => $GLOBALS['TL_LANG']['MSC']['caledit_JumpWhatsNext'],
            'inputType' => 'select',
            'options' => $JumpOpts,
            'value' => $jumpToSelection,
            'reference' => $JumpRefs,
            'eval' => array('mandatory' => false, 'includeBlankOption' => true, 'maxlength' => 128, 'decodeEntities' => true)
        );

        // here: CALL Hooks with $NewEventData, $currentEventObject, $fields
        if (array_key_exists('buildCalendarCloneForm', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['buildCalendarCloneForm'])) {
            foreach ($GLOBALS['TL_HOOKS']['buildCalendarCloneForm'] as $key => $callback) {
                $this->import($callback[0]);
                $arrResult = $this->{$callback[0]}->{$callback[1]}($newDates, $fields, $currentEventObject, $currentID);
                if (is_array($arrResult) && count($arrResult) > 1) {
                    $newDates = $arrResult['newDates'];
                    $fields = $arrResult['fields'];
                }
            }
        }

        // Initialize widgets
        $arrWidgets = array();
        $doNotSubmit = false;
        foreach ($fields as $field) {
            $strClass = $GLOBALS['TL_FFL'][$field['inputType']];
            $field['eval']['required'] = $field['eval']['mandatory'];

            // from http://pastebin.com/HcjkHLQK
            // via https://github.com/contao/core/issues/5086
            // Convert date formats into timestamps (check the eval setting first -> #3063)
            if (Input::post('FORM_SUBMIT') === 'caledit_submit') {
                $rgxp = $field['eval']['rgxp'] ?? '';
                if (($rgxp == 'date' || $rgxp == 'time' || $rgxp == 'datim') && $field['value'] != '') {
                    $objDate = new \Date(Input::post($field['name']), $GLOBALS['TL_CONFIG'][$rgxp . 'Format']);
                    $field['value'] = $objDate->tstamp;
                }
            }

            $objWidget = new $strClass($this->prepareForWidget($field, $field['name'], $field['value']));
            // Validate widget
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $objWidget->validate();
                if ($objWidget->hasErrors()) {
                    $doNotSubmit = true;
                }
            }
            $arrWidgets[$field['name']] = $objWidget;
        }

        // Contao 4.4+: The CalendarFields need to be parsed to activate JS
        for ($i = 1; $i <= 10; $i++) {
            $arrWidgets['start' . $i]->parse();
            $arrWidgets['end' . $i]->parse();
        }

        $allDatesAllowed = $this->allDatesAllowed($currentEventData['pid']);
        for ($i = 1; $i <= 10; $i++) {
            // check the 10 startdates
            $newDate = strtotime($arrWidgets['start' . $i]->__get('value'));

            if ((!$allDatesAllowed) and ($newDate) and ($newDate < time())) {
                $arrWidgets['start' . $i]->addError($GLOBALS['TL_LANG']['MSC']['caledit_formErrorElapsedDate']);
                $doNotSubmit = true;
            }
        }

        $this->Template->submit = $GLOBALS['TL_LANG']['MSC']['caledit_saveData'];

        if ((!$doNotSubmit) && ($this->Input->post('FORM_SUBMIT') == 'caledit_submit')) {
            // everything seems to be ok, so we can add the POST Data
            // into the Database
            if (!FE_USER_LOGGED_IN) {
                $currentEventData['fe_user'] = ''; // no user
            } else {
                $currentEventData['fe_user'] = $this->User->id; // set the FE_user here
            }

            // for the notification E-Mail
            $originalStart = $currentEventData['startDate'];
            $originalEnd = $currentEventData['endDate'];
            $newDatesMail = '';

            // overwrite User
            if (!FE_USER_LOGGED_IN) {
                $currentEventData['fe_user'] = ''; // no user
            } else {
                $currentEventData['fe_user'] = $this->User->id; // set the FE_user here
            }
            // Set Publish-Value
            $currentEventData['published'] = $published;
            if (is_null($currentEventData['published'])) {
                $currentEventData['published'] = '';
            }

            // convert the existing timestamps into Strings, so that PutinDB can use them again
            if ($currentEventData['startTime']) {
                $currentEventData['startTime'] = date($GLOBALS['TL_CONFIG']['timeFormat'], $currentEventData['startTime']);
            }
            if ($currentEventData['endTime']) {
                $currentEventData['endTime'] = date($GLOBALS['TL_CONFIG']['timeFormat'], $currentEventData['endTime']);
            }

            for ($i = 1; $i <= 10; $i++) {
                if ($newDates['start' . $i]) {
                    $currentEventData['startDate'] = $newDates['start' . $i];
                    $currentEventData['endDate'] = $newDates['end' . $i];

                    $newDatesMail .= $currentEventData['startDate'];
                    if ($currentEventData['endDate']) {
                        $newDatesMail .= "-" . $currentEventData['endDate'] . " \n";
                    } else {
                        $newDatesMail .= " \n";
                    }
                    $DBid = $this->saveToDB($currentEventData, '', $currentContentData, '');
                }
            }

            // restore values
            $currentEventData['startDate'] = $originalStart;
            $currentEventData['endDate'] = $originalEnd;
            // Send Notification EMail
            if ($this->caledit_sendMail) {
                $this->SendNotificationMail($currentEventData, $currentID, $this->User->username, $newDatesMail);
            }

            // after this: jump to "jumpTo-Page"
            $this->generateRedirect($jumpToSelection, $DBid);
        } else {
            // Do NOT Submit
            if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
                $this->Template->InfoClass = 'tl_error';
                $this->Template->InfoMessage = $GLOBALS['TL_LANG']['MSC']['caledit_error'];
            }
            $this->Template->fields = $arrWidgets;
        }
        return;
    }


    protected function SendNotificationMail($NewEventData, $editID, $User, $cloneDates)
    {
        $Notification = new \Contao\Email();
        $Notification->from = $GLOBALS['TL_ADMIN_EMAIL'];

        $host = $this->Environment->host;

        if ($editID) {
            if ($editID == -1) {
                $Notification->subject = sprintf($GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectDelete'], $host);
            } else {
                $Notification->subject = sprintf($GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectEdit'], $host);
            }
        } else {
            $Notification->subject = sprintf($GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectNew'], $host);
        }

        $arrRecipients = trimsplit(',', $this->caledit_mailRecipient);
        $mText = $GLOBALS['TL_LANG']['MSC']['caledit_MailEventdata'] . " \n\n";
        if (!FE_USER_LOGGED_IN) {
            $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_MailUnregisteredUser'] . " \n";
        } else {
            $mText .= sprintf($GLOBALS['TL_LANG']['MSC']['caledit_MailUser'], $User) . " \n";
        }
        $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_startdate'] . ': ' . $NewEventData['startDate'] . " \n";
        $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_enddate'] . ': ' . $NewEventData['endDate'] . "\n";
        $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_starttime'] . ': ' . $NewEventData['startTime'] . "\n";
        $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_endtime'] . ': ' . $NewEventData['endTime'] . "\n";
        $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_title'] . ': ' . $NewEventData['title'] . "\n";
        if ($NewEventData['published']) {
            $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_publishedEvent'];
        } else {
            $mText .= $GLOBALS['TL_LANG']['MSC']['caledit_unpublishedEvent'];
        }

        if ($cloneDates) {
            $mText .= "\n\n" . $GLOBALS['TL_LANG']['MSC']['caledit_MailEventWasCloned'] . "\n" . $cloneDates;
        }

        if (!$this->caledit_allowPublish) {
            $mText .= "\n\n" . $GLOBALS['TL_LANG']['MSC']['caledit_BEUserHint'];
        }
        $Notification->text = $mText;

        foreach ($arrRecipients as $rec) {
            $Notification->sendTo($rec);
        }
    }


    /**
     * Generate module
     */
    protected function compile()
    {
        // Add TinyMCE-Stuff to header
        $this->addTinyMCE($this->caledit_tinMCEtemplate);
        // Check for "add" or "edit"

        $editID = $this->Input->get('edit');

        $deleteID = $this->Input->get('delete');
        if ($deleteID) {
            $editID = $deleteID;
        }

        $cloneID = $this->Input->get('clone');
        if ($cloneID) {
            $editID = $cloneID;
        }

        $fatalError = False;

        $this->import('FrontendUser', 'User');
        $this->allowedCalendars = $this->getCalendars($this->User);
        if (count($this->allowedCalendars) == 0) {
            $fatalError = True;
            $this->errorString = $GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'];
        } else {
            $currentEventObject = CalendarEventsModelEdit::findByIdOrAlias($editID);

            $AuthorizedUser = (bool)$this->checkUserEditRights($this->User, $editID, $currentEventObject);
            if (!$AuthorizedUser) {
                // a proper ErrorString is set in checkUserEditRights
                $fatalError = True;
            }
        }

        // Fatal error, editing not allowed, abort.
        if ($fatalError) {
            $this->strTemplate = $this->caledit_template;
            $this->Template = new \FrontendTemplate($this->strTemplate);
            $this->Template->FatalError = $this->errorString;
            return;
        }

        // ok, the user is an authorized user
        if ($deleteID) {
            $this->HandleDelete($currentEventObject);
            return;
        }

        if ($cloneID) {
            $this->HandleClone($currentEventObject);
            return;
        }

        $this->handleEdit($editID, $currentEventObject);
        return;

    }
}

?>