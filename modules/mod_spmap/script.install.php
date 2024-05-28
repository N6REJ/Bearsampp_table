<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/script.install.helper.php';

class Mod_SPMapInstallerScript extends Mod_SPMapInstallerScriptHelper
{
	public $name = 'SPMAP';
	public $alias = 'spmap';
	public $extension_type = 'module';
	public $show_message = false;
}
