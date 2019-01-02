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

A simple form for the Contao Frontend. The Frontend-User can
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

## Usage

Just an overview here.

Preliminaries:
* create 3 pages for Calendar/Eventlist, Eventreader and EventEditor
* specify Member groups that are allowed to edit events in the Frontend
* create a Calendar (or open the settings for an existing one), enable Frontend editing and select the redirect page for editing and specify authorized member groups

### The Event Editor module
Now we can create a new Module of type "Event Editor". 

The redirect page defines the page, where the user is redirected to after the formular is submitted succesfully. The FE user may overwrite this by a selection of some other pages (show the new event in event reader, show it in event editor again, create a new event or duplicate the just created event).

The Backend user can specify some more settings here, which should be self-explaining. For example, a list of calendars must be set where this module has access to, just as in the event reader module as well.

There you can also set some more mandatory fields, which the FE user must fill in the form.

To keep it simple for the FE user, the field "CSS value" can be renamed into something the FE user may understand more easily. This depends on how CSS values are used in the calendar on your website. You can also add a list of predefined CSS values/labels.

For entering dates more comfortable, a jQuery Datepicker can be added to the form.

This module should then be added to the article on the editor page you selected in the calendar(s) before.

### Provide "edit links" to the Frontend user

If a user is authorized to edit events, he or she should be provided with some links to click on to edit these events. To update an existing website you should

* Change the templates of Calendar modules to "cal_default_edit" (edit this templates to see how it works). 
* If you want to use the "add event" links, you have to replace the Calendar module with the Calendar FE module. This module adds also "add event" links for every day, which are used in the cal_default_edit template.
* Change the templates of Eventlist modules to one of the "\_edit" templates (edit these templates to see how it works). 
* On the page with an event reader, you may also add a module of type "Event reader: Edit link"

Note that an "edit link" is only added to the template, if the user is actually authorized to edit this event. If the current FE user is not authorized to edit an event, he or she should not see the link. If the user enters such an unauthorized edit-link in the address bar of the browser, the editing form should show an error message.

### Captchas

This module supports editing of events even for not registered users. In this case, a captcha is included to the formular. With the most recent changes to Contao 4.6, this does not work any more as it did in Contao 4.4. 

Now it works like this:

* By default the captcha is included (for non registered users), but extended with the built-in honeypot system from Contao. Therefore it is *not* shown to most users, but your calendar should still be protected against spam bots. If the user has disabled Javascript for your site, the captcha is shown.
* If the honeypot does not work for you, and you get a lot of spam entries, you can try to use the new editor template `eventEdit_ForceCaptcha.html5`. That way the captcha is always shown, but it may lead to a duplicate captcha field on the form in case the user answers it wrong, or Javascript is disabled.
* You can modify the captcha field by editing the template `form_captcha_calendar-editor.html5` so it matches your modified editor template.
* The Delete-Event template always present a captcha, even to registered users to prevent unintended deleting of events. On this form, it may also happen that the captcha field is duplicated.

## Donation

If you like this extension and think it's worth a little donation: You can support me via Paypal.Me:

[Donation for CalendarEditor](https://paypal.me/CalendarEditor/10)

Thank You!
