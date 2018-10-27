# Contao-CalendarEditor

This is a revision of the module for Contao 3. Essentially, everything should run as it did in Contao 3. The only change is the direct integration of the extension "CalendarField", which allows the use of a jQuery calendar for date fields.

## Features

New settings in the Contao Calender: Enable Frontend editing
* option: "Only future events", which disables editing of elapsed events
* option: Login required (highly recommended on productive sites)
* Define member groups for editing: "Members" and "Admins", which can edit all events (unless they are locked in the Backend, see below)
* option: Allow editing only for owner: Only the (Frontend) creator of an event is able to edit it later (Frontend-Admins can edit events from other users/admins anyway)

New setting in the Contao Events
* disable Frontend editing (lock single events for Frontend editing, even for Frontend Admins)

### The Event-Editor module

A simple formular for the Contao Frontend. The Frontend-User can
* add new events
* edit existing events
* optional: "Save as" while editing existing events to create a copy of the event
* delete existing events (optional)
* clone existing events (optional), to create up to 10 copies of an event on different dates
* optional: Send a notification email, when a FE user creates or edits an event
* optional: Allow FE users to publish events on their own (otherwise a BE user has to publish them)

As the goal is to keep it simple, only the following fields can be edited
* Start/End date (optional with jQuery Picker)
* Start/End time
* Title
* Location
* Teaser (TinyMCE available)
* Details (TinyMCE available)
* CSS value: the FE user can select from a list of human readable predefined values, like "Red Color" and "Blue Color", which are translated into css classes like "red" and "blue".
* destination Calendar (allowed Calendars need to be specified in the Backend)

The field "details" is a little bit tricky here. It is just one single text field, but the Contao model is way more complex. When creating a new event, a new Content-Element of type "Text" is created. When an existing event is edited, the first Content-Element of type "Text" is presented for editing. If this element contains further information like headline or attached image, or if there are more Content-Elements (created in the Backend) a warning is presented to the Frontend-User.

### The Hidden-Eventlist module

An Eventlist that shows only the *hidden* Events, provided with an "edit-link" to open the event in the Frontend editor

### The Event Editlink module

Should be placed on the site with the Eventreader module. Provides a proper edit-link to open the event in the Frontend editor. Optional: create also links to duplicate or delete the event.

### The Calendar FE edit module

Similar to the regular Contao Calendar, but with edit-Links and "add event"-Links for every day. As a bonus, a "Holiday Calendar" can be specified. Events from this calendar are shown in the day-header of the calender module, to mark some days as "holiday", or whatever.
