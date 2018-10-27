<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');


/**
 * Miscellaneous
 */

$GLOBALS['TL_LANG']['MSC']['caledit_addLabel'] = '[+]';
$GLOBALS['TL_LANG']['MSC']['caledit_addTitle'] = 'Add an event at this day.';

$GLOBALS['TL_LANG']['MSC']['caledit_editLabel'] = '[edit]';
$GLOBALS['TL_LANG']['MSC']['caledit_editTitle'] = 'Edit this event.';
$GLOBALS['TL_LANG']['MSC']['caledit_cloneLabel'] = '[duplicate]';
$GLOBALS['TL_LANG']['MSC']['caledit_cloneTitle'] = 'Duplicate this event.';
$GLOBALS['TL_LANG']['MSC']['caledit_deleteLabel'] = '[delete]';
$GLOBALS['TL_LANG']['MSC']['caledit_deleteTitle'] = 'Delete this event.';
$GLOBALS['TL_LANG']['MSC']['caledit_viewLabel'] = '[view]';

$GLOBALS['TL_LANG']['MSC']['caledit_startdate'] = 'Start date';
$GLOBALS['TL_LANG']['MSC']['caledit_enddate'] = 'End date';
$GLOBALS['TL_LANG']['MSC']['caledit_starttime'] = 'Start time';
$GLOBALS['TL_LANG']['MSC']['caledit_endtime'] = 'End time';
$GLOBALS['TL_LANG']['MSC']['caledit_title'] = 'Title';
$GLOBALS['TL_LANG']['MSC']['caledit_teaser'] = 'Teaser';
$GLOBALS['TL_LANG']['MSC']['caledit_location'] = 'Location';
$GLOBALS['TL_LANG']['MSC']['caledit_details'] = 'Details';

$GLOBALS['TL_LANG']['MSC']['caledit_css'] = 'CSS class';
$GLOBALS['TL_LANG']['MSC']['caledit_pid'] = 'Calendar';
$GLOBALS['TL_LANG']['MSC']['caledit_published'] = 'Publish event';
$GLOBALS['TL_LANG']['MSC']['caledit_saveAs'] = 'Save event as a copy';
$GLOBALS['TL_LANG']['MSC']['caledit_saveData'] = 'Save event';
$GLOBALS['TL_LANG']['MSC']['caledit_deleteData'] = 'Delete event';
$GLOBALS['TL_LANG']['MSC']['caledit_deleteHint'] = 'Note: A deleted event cannot be restored.';

$GLOBALS['TL_LANG']['MSC']['caledit_unpublishedEvent'] = 'unpublished event';
$GLOBALS['TL_LANG']['MSC']['caledit_publishedEvent']   = 'published event';
$GLOBALS['TL_LANG']['MSC']['caledit_deleteEvent']      = 'Delete this event';
$GLOBALS['TL_LANG']['MSC']['caledit_newEvent'] = 'New event';

$GLOBALS['TL_LANG']['MSC']['caledit_currentActionEdit'] = 'Currently editing';
$GLOBALS['TL_LANG']['MSC']['caledit_currentActionDelete'] = 'Currently deleting';
$GLOBALS['TL_LANG']['MSC']['caledit_currentActionClone'] = 'Currently duplicating';

$GLOBALS['TL_LANG']['MSC']['caledit_InsertEventData'] = 'Insert event data';
$GLOBALS['TL_LANG']['MSC']['caledit_ConfirmEventDelete'] = 'Confirm deletion of this event';
$GLOBALS['TL_LANG']['MSC']['caledit_InsertMoreDates'] = 'Insert additional dates';

$GLOBALS['TL_LANG']['MSC']['caledit_JumpWhatsNext'] = 'After saving';
$GLOBALS['TL_LANG']['MSC']['caledit_JumpToNew']     = 'Create a new event';
$GLOBALS['TL_LANG']['MSC']['caledit_JumpToView']    = 'Show this event on the website';
$GLOBALS['TL_LANG']['MSC']['caledit_JumpToEdit']    = 'Edit this event again';
$GLOBALS['TL_LANG']['MSC']['caledit_JumpToClone']   = 'Duplicate this event';

$GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectNew'] = 'New Event on %s';
$GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectEdit'] = 'An Event was edited on %s';
$GLOBALS['TL_LANG']['MSC']['caledit_MailSubjectDelete'] = 'An Event was deleted on %s';
$GLOBALS['TL_LANG']['MSC']['caledit_MailEventWasCloned'] = 'The event was duplicated. New dates:';
$GLOBALS['TL_LANG']['MSC']['caledit_MailEventdata'] = 'Event Data:';
$GLOBALS['TL_LANG']['MSC']['caledit_MailUser'] = 'User: %s';
$GLOBALS['TL_LANG']['MSC']['caledit_MailUnregisteredUser'] = 'User: (Unregistered User)';
$GLOBALS['TL_LANG']['MSC']['caledit_BEUserHint'] = 'The user is not allowed to publish this event. You should login in the Backend and publish it now.';

$GLOBALS['TL_LANG']['MSC']['caledit_UnauthorizedUser'] = 'Unauthorized user. Editing is not allowed.';
$GLOBALS['TL_LANG']['MSC']['caledit_NoEditAllowed'] = 'Editing events in this calendar is not allowed.';
$GLOBALS['TL_LANG']['MSC']['caledit_NoPublishAllowed'] = 'You are note allowed to publish events.';
$GLOBALS['TL_LANG']['MSC']['caledit_NoPast'] = 'This event has elapsed. Editing is not allowed.' ;
$GLOBALS['TL_LANG']['MSC']['caledit_DisabledEvent'] = 'This event was locked by a backend-user. Editing is not allowed.' ;
$GLOBALS['TL_LANG']['MSC']['caledit_OnlyUser'] = 'This event was created by someone else. Editing is not allowed.';
$GLOBALS['TL_LANG']['MSC']['caledit_unexpected'] = 'An unexpected error occured. Editing is not possible.';
$GLOBALS['TL_LANG']['MSC']['caledit_NoDelete'] = 'You are not allowed to delete events.';
$GLOBALS['TL_LANG']['MSC']['caledit_formErrorElapsedDate'] = 'Please enter a date in the future.';

$GLOBALS['TL_LANG']['MSC']['caledit_MultipleContentElements'] = "There are content elements for this event, which can't be viewed, modified or duplicated with this formular. Please use the backend to modify these elements.";
$GLOBALS['TL_LANG']['MSC']['caledit_ContentElementWithImage'] = "There is an image attached to this event, which can't be modified with this formular. Please use the backend for this.";
$GLOBALS['TL_LANG']['MSC']['caledit_error'] = "An error occured.";
$GLOBALS['TL_LANG']['MSC']['caledit_CloneWarning'] = "You are about to duplicate an event. Be sure that all information is correct, as can't edit them at once after this.";
$GLOBALS['TL_LANG']['MSC']['caledit_deleteWarning'] = "You are about to delete an event. This can not be reversed.";



?>