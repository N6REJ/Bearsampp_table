<?php
/**
* J51_Numbers
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');

// Define some variables
$j51_items    				   = $params->get( 'j51_items' );
$j51_margin_x		   		   = htmlspecialchars($params->get( 'j51_margin_x', '' ), ENT_COMPAT, 'UTF-8');
$j51_margin_y		   		   = htmlspecialchars($params->get( 'j51_margin_y', '' ), ENT_COMPAT, 'UTF-8');
$j51_color			   		   = htmlspecialchars($params->get( 'j51_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_bg_color		   		   = htmlspecialchars($params->get( 'j51_bg_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_size			   		   = htmlspecialchars($params->get( 'j51_size', '' ), ENT_COMPAT, 'UTF-8');
$j51_layout			   		   = htmlspecialchars($params->get( 'j51_layout', '' ), ENT_COMPAT, 'UTF-8');
$j51_align			   		   = htmlspecialchars($params->get( 'j51_align', '' ), ENT_COMPAT, 'UTF-8');
$j51_columns		   		   = htmlspecialchars($params->get( 'j51_columns', '' ), ENT_COMPAT, 'UTF-8');
$j51_title_tag		   		   = htmlspecialchars($params->get( 'j51_title_tag', 'h3'), ENT_COMPAT, 'UTF-8');
$j51_override_style	   		   = htmlspecialchars($params->get( 'j51_override_style', 0 ), ENT_COMPAT, 'UTF-8');
$j51_number_color	   		   = htmlspecialchars($params->get( 'j51_number_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_title_color	   		   = htmlspecialchars($params->get( 'j51_title_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_caption_color	   		   = htmlspecialchars($params->get( 'j51_caption_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_animation_length  		   = htmlspecialchars($params->get( 'j51_animation_length', 2000 ), ENT_COMPAT, 'UTF-8');
$j51_interval_length   		   = htmlspecialchars($params->get( 'j51_interval_length', '' ), ENT_COMPAT, 'UTF-8');
$j51_moduleid       		   = $module->id;
$id                            = 1;
$delay 						   = 0;
$dataArray                     = array();

require JModuleHelper::getLayoutPath('mod_j51numbers', $params->get('layout', 'default'));
?>