<?php
/**
* J51_Rotate
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* Licensed under the GPL v2&
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;

JLoader::register('modj51Rotate', __DIR__ . '/helper.php');

// Define variables
$rotate_items         = $params->get('rotate_items');
$j51_columns          = $params->get('j51_columns', '4');
$j51_columns_tabl     = $params->get('j51_columns_tabl', '3');
$j51_columns_tabp     = $params->get('j51_columns_tabp', '3');
$j51_columns_mobl     = $params->get('j51_columns_mobl', '2');
$j51_columns_mobp     = $params->get('j51_columns_mobp', '1');
$j51_image_margin_x   = $params->get('j51_image_margin_x', '10');
$j51_image_margin_y   = $params->get('j51_image_margin_y', '10');
$j51_trans_speed      = $params->get('j51_trans_speed', 1000);
$j51_axis             = $params->get('j51_axis', 'true');
$j51_autoplay         = $params->get('j51_autoplay', 'true');
$j51_autoplay_delay   = $params->get('j51_autoplay_delay', 3000);
$j51_slideby       	  = $params->get( 'j51_slideby', 'page');
$j51_gutter_size      = $params->get( 'j51_gutter_size', '20');
$j51_random           = $params->get( 'j51_random', false);
$j51_moduleid         = $module->id;

require JModuleHelper::getLayoutPath('mod_j51rotate', 'default');
