<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @copyright  Daniel Gaussmann 2011-2018
 * @author     Daniel Gaussmann <mail@gausi.de>
 * @package    CalendarEditor
 * @license    GNU/LGPL
 */


/**
 * Add palettes to tl_module
 */

 $GLOBALS['TL_DCA']['tl_module']['palettes']['calendarEdit']        =  '{title_legend},name,headline,type;'; // $GLOBALS['TL_DCA']['tl_module']['palettes']['calendar'].';{edit_legend},caledit_add_jumpTo; {edit_holidays},cal_holidayCalendar' ; // .'{expert_legend:hide},guests,cssID,space';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventEditor']         = '{title_legend},name,headline,type;{redirect_legend},jumpTo;{config_legend},cal_calendar,caledit_mandatoryfields, caledit_allowPublish,caledit_allowDelete,caledit_allowClone,caledit_sendMail;{template_legend}, caledit_template,caledit_delete_template, caledit_clone_template, caledit_tinMCEtemplate, caledit_alternateCSSLabel,caledit_usePredefinedCss;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventReaderEditLink'] = '{title_legend},name,headline,type;{config_legend},cal_calendar,caledit_showDeleteLink,caledit_showCloneLink';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventHiddenList']     = $GLOBALS['TL_DCA']['tl_module']['palettes']['eventlist'];

 $GLOBALS['TL_DCA']['tl_module']['subpalettes']['caledit_usePredefinedCss'] = 'caledit_cssValues';
 $GLOBALS['TL_DCA']['tl_module']['subpalettes']['caledit_sendMail']         = 'caledit_mailRecipient';
 $GLOBALS['TL_DCA']['tl_module']['subpalettes']['cal_holidayCalendar'] = 'cal_holidayCalendar';

 $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'caledit_usePredefinedCss';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'caledit_sendMail';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'cal_holidayCalendar';


$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_allowPublish'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_allowPublish'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_allowDelete'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_allowDelete'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_allowClone'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_allowClone'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_sendMail'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_sendMail'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr m12 w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_mailRecipient'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_mailRecipient'],
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'					  => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_mandatoryfields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_mandatoryfields'],
	'inputType'               => 'checkbox',
    'options'                 => array('starttime','location','teaser', 'details', 'css'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields'],
	'eval'                    => array('multiple'=>true, 'tl_class'=>'w100'),
	'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_add_jumpTo'] = array
(
 	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_add_jumpTo'],
 	'inputType'               => 'pageTree',
 	'eval'                    => array('fieldType'=>'radio'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_template'],
	'default'                 => 'eventEdit_default',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getEventEditTemplates'),
	'eval'                    => array ('tl_class'=>'w50'),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_clone_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_clone_template'],
	'default'                 => 'eventEdit_duplicate',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getEventEditTemplates'),
	'eval'                    => array ('tl_class'=>'w50'),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_delete_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_delete_template'],
	'default'                 => 'eventEdit_delete',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getEventEditTemplates'),
	'eval'                    => array ('tl_class'=>'w50'),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_tinMCEtemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_tinMCEtemplate'],
	'default'                 => 'tinyFrontendMinimal',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getConfigFiles'),
	'eval'			  		  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_alternateCSSLabel'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_alternateCSSLabel'],
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>64, 'tl_class'=>'w100'),
	'sql'					  => "varchar(64) NOT NULL default ''"	
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_usePredefinedCss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_usePredefinedCss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_cssValues'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_cssValues'],
	'inputType'               => 'multitextWizard',	
	'eval'                    => array
      (
        'style'=>'width:100%;',
        'columns' => array
          (
            array
            (
              'name' => 'label', 
              'label' => &$GLOBALS['TL_LANG']['tl_module']['css_label'],
              'mandatory' => true,
              'width' => '100px' 
            ),
            array
            (
              'name' => 'value', 
              'label' => &$GLOBALS['TL_LANG']['tl_module']['css_value'],
              'mandatory' => true,
              'width' => '50px',
              'rgxp' => 'alpha', 
            )
          )
       ),
	 'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_showDeleteLink'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_showDeleteLink'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_showCloneLink'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_showCloneLink'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_module']['fields']['cal_holidayCalendar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_holidayCalendar'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_eventeditor', 'getCalendars'),
	'eval'                    => array('mandatory'=>false, 'multiple'=>true),
	'sql'                     => "blob NULL"
);


class tl_module_eventeditor extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	/**
	 * Return all event templates as array
	 * @param object
	 * @return array
	 */
	public function getEventEditTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('eventEdit_', $dc->activeRecord->pid);
	}	
	
	public function getCalendars()
	{
		if (!$this->User->isAdmin && !is_array($this->User->calendars))
		{
			return array();
		}

		$arrCalendars = array();
		$objCalendars = $this->Database->execute("SELECT id, title FROM tl_calendar ORDER BY title");

		while ($objCalendars->next())
		{
			if ($this->User->hasAccess($objCalendars->id, 'calendars'))
			{
				$arrCalendars[$objCalendars->id] = $objCalendars->title;
			}
		}

		return $arrCalendars;
	}

    /**
     * Return a list of tinyMCE config files in this system.
     * copied from "FormRTE", @copyright  Andreas Schempp 2009
     */
    public function getConfigFiles()
	{
		$arrConfigs = array();
		$arrFiles = scan(TL_ROOT . '/system/config/');

		foreach( $arrFiles as $file ) {
			if (substr($file, 0, 4) == 'tiny') {
				$arrConfigs[] = basename($file, '.php');
			}
		}
		return $arrConfigs;
	}
}

