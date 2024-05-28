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

if (!$provider = $params->get('provider', 'YouTube'))
{
	return;
}

$file = __DIR__ . '/providers/' . strtolower($provider) . '.php';

if(!file_exists($file))
{
	return;
}

// Display selected widget
require $file;