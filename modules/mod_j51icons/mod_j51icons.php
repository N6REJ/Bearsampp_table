<?php
/**
* J51_Icons
* Version		: 1.0
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

$baseurl 				        = JURI::base();
$j51_items    			    = $params->get( 'j51_items' );
$j51_icon_margin_x		  = htmlspecialchars($params->get( 'j51_icon_margin_x', '0' ), ENT_COMPAT, 'UTF-8');
$j51_icon_margin_y		  = htmlspecialchars($params->get( 'j51_icon_margin_y', '0' ), ENT_COMPAT, 'UTF-8');
$j51_icon_color			    = htmlspecialchars($params->get( 'j51_icon_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_bg_color		  = htmlspecialchars($params->get( 'j51_icon_bg_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_set			      = htmlspecialchars($params->get( 'j51_icon_set', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_size			    = htmlspecialchars($params->get( 'j51_icon_size', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_layout		    = htmlspecialchars($params->get( 'j51_icon_layout', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_align			    = htmlspecialchars($params->get( 'j51_icon_align', '' ), ENT_COMPAT, 'UTF-8');
$j51_title_tag			    = htmlspecialchars($params->get( 'j51_title_tag', 'h3' ), ENT_COMPAT, 'UTF-8');
$j51_caption_tag		    = htmlspecialchars($params->get( 'j51_caption_tag', 'p' ), ENT_COMPAT, 'UTF-8');
$j51_bg_style			      = htmlspecialchars($params->get( 'j51_bg_style', 'boxed' ), ENT_COMPAT, 'UTF-8');
$j51_bg_color			      = htmlspecialchars($params->get( 'j51_bg_color', '#fff' ), ENT_COMPAT, 'UTF-8');
$j51_icon_border_color	= htmlspecialchars($params->get( 'j51_icon_border_color', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_border_size	  = htmlspecialchars($params->get( 'j51_icon_border_size', '2' ), ENT_COMPAT, 'UTF-8');
$j51_icon_style			    = htmlspecialchars($params->get( 'j51_icon_style', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_columns		    = htmlspecialchars($params->get( 'j51_icon_columns', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_columns_tabl  = htmlspecialchars($params->get( 'j51_icon_columns_tabl', '33.333%'), ENT_COMPAT, 'UTF-8');
$j51_icon_columns_tabp  = htmlspecialchars($params->get( 'j51_icon_columns_tabp', '33.333%'), ENT_COMPAT, 'UTF-8');
$j51_icon_columns_mobl  = htmlspecialchars($params->get( 'j51_icon_columns_mobl', '50%'), ENT_COMPAT, 'UTF-8');
$j51_icon_columns_mobp  = htmlspecialchars($params->get( 'j51_icon_columns_mobp', '100%'), ENT_COMPAT, 'UTF-8');
$j51_icon_animate_class	= htmlspecialchars($params->get( 'j51_icon_animate_class', '' ), ENT_COMPAT, 'UTF-8');
$j51_icon_max_width	    = htmlspecialchars($params->get( 'j51_icon_max_width', '' ), ENT_COMPAT, 'UTF-8');

$j51_moduleid       	  = $module->id;

$j51_circle_size		= $j51_icon_size * 2;

require JModuleHelper::getLayoutPath('mod_j51icons', $params->get('layout', 'default'));

?>