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
 * @copyright  Leo Feyer 2005-2010
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Config
 * @license    LGPL
 * @filesource
 */


/**
 * This is the tinyMCE (rich text editor) configuration file. Please visit
 * http://tinymce.moxiecode.com for more information.
 */


if ($GLOBALS['TL_CONFIG']['useRTE']): ?>

<script src="<?php echo TL_ASSETS_URL; ?>assets/tinymce4/js/tinymce.gzip.js"></script>
<script>tinymce.init({
	selector:'textarea#ctrl_details, textarea#ctrl_teaser',
	menubar : false,
	language : "<?php echo $this->language; ?>",
	plugins : 'link, table, paste',	
	toolbar1: "link unlink | undo redo | bold italic underline | bullist numlist | removeformat",	
	resize: true,
	paste_word_valid_elements: "b,strong,i,em,h1,h2,p"

});</script>

<?php endif; ?>