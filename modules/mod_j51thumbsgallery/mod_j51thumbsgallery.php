<?php
/**
* @title	    J51 Thumbs Gallery
* @version		1.1
* @website		http://www.joomla51.com
* @copyright	Copyright (C) 2012 Joomla51.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

// Define variables
$fade_opacity		= $params->get('fade_opacity');
$bordersize			= $params->get('bordersize');		
$bordercolor		= $params->get('bordercolor');
$outlinecolor		= $params->get('outlinecolor');
$width				= $params->get('thumbwidth');
$height				= $params->get('thumbheight');
$margin				= $params->get('margin');
$show_jquery	    = $params->get('show_jquery');
$alignment	        = $params->get('alignment');

require JModuleHelper::getLayoutPath('mod_j51thumbsgallery', $params->get('layout', 'default'));

?>