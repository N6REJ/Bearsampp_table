<?php
/**
* J51_ImageHover
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

// Define variables
$imagehover_images    = $params->get('imagehover_images');
$j51_imghvr           = $params->get('j51_imghvr', 'imghvr-fade-in-up');
$j51_image_width      = $params->get('j51_image_width', '25%');
$j51_image_width_tabl = $params->get('j51_image_width_tabl', '33.333%');
$j51_image_width_tabp = $params->get('j51_image_width_tabp', '33.333%');
$j51_image_width_mobl = $params->get('j51_image_width_mobl', '50%');
$j51_image_width_mobp = $params->get('j51_image_width_mobp', '100%');
$j51_image_margin_x   = $params->get('j51_image_margin_x', '10');
$j51_image_margin_y   = $params->get('j51_image_margin_y', '10');
$j51_image_padding_x  = $params->get('j51_image_padding_x', '20');
$j51_image_padding_y  = $params->get('j51_image_padding_y', '20');
$j51_overlay_color    = $params->get('j51_overlay_color', '');
$j51_title_color      = $params->get('j51_title_color', '#ffffff');
$j51_text_color       = $params->get('j51_text_color', '#ffffff');
$j51_text_align       = $params->get('j51_text_align', 'center');
$j51_text_vert_align  = $params->get('j51_text_vert_align', 'center');
$j51_title            = $params->get('j51_title');
$j51_image            = $params->get('j51_image');
$j51_image_alt        = $params->get('j51_image_alt');
$j51_target_url       = $params->get('j51_target_url');
$j51_target           = $params->get('j51_target');
$j51_layout_type      = $params->get('j51_layout_type');
$j51_trans_speed      = $params->get('j51_trans_speed', 1000);
$j51_autoplay         = $params->get('j51_autoplay', 'true');
$j51_autoplay_delay   = $params->get('j51_autoplay_delay', 3000);
$svg_code       	  = $params->get( 'svg_code', '');
$j51_moduleid         = $module->id;

if($j51_layout_type === 'static')  {
    $j51_layout_type = 'default';
}

require JModuleHelper::getLayoutPath('mod_j51imagehover', $params->get('j51_layout_type', 'default'));
