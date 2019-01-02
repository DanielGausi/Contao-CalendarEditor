<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


/**
 * This is the tinyMCE (rich text editor) configuration file. Please visit
 * http://tinymce.moxiecode.com for more information.
 */


if ($GLOBALS['TL_CONFIG']['useRTE']): ?>

<script src="<?php echo TL_ASSETS_URL; ?>assets/tinymce4/js/tinymce.min.js"></script>
<script>tinymce.init({
	selector:'textarea#ctrl_details, textarea#ctrl_teaser',
	language : "<?php echo $this->language; ?>",
	plugins : 'link, table, paste, charmap',
	menu : { 
        edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},        
        format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
        table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
        tools  : {title : 'Tools' , items : 'spellchecker code'}
    },
	toolbar1: "link unlink | undo redo | styleselect | bold italic underline | removeformat | image",
	toolbar2: "alignleft aligncenter alignright | outdent indent | bullist numlist | table | charmap",
	
	resize: true,
	paste_word_valid_elements: "b,strong,i,em,h1,h2,p"

});</script>

<?php endif; ?>