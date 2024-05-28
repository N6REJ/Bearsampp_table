<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/script.install.helper.php';

class PlgFieldsACFUploadInstallerScript extends PlgFieldsACFUploadInstallerScriptHelper
{
	public $alias = 'acfupload';
	public $extension_type = 'plugin';
	public $plugin_folder = "fields";
	public $show_message = false;
}