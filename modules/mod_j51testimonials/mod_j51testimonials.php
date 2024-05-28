<?php
/**
* J51_Testimonials
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

$doc 						= JFactory::getDocument();
$j51_moduleid       		  = $module->id;
$j51_items    				    = $params->get( 'j51items' );
$j51_num_blocks				    = $params->get( 'j51_num_blocks' );
$j51_blocks_per_slide		  = $params->get( 'j51_blocks_per_slide' );
$j51_transition_interval	= $params->get( 'j51_transition_interval' );
$j51_transition_duration	= $params->get( 'j51_transition_duration' );
$j51_transition_type		  = $params->get( 'j51_transition_type', 'slide' );
$j51_transition_in			  = $params->get( 'j51_transition_in' );
$j51_transition_out			  = $params->get( 'j51_transition_out' );
$j51_autoplay				      = $params->get( 'j51_autoplay' );
$j51_image_width_tabl		  = $params->get( 'j51_image_width_tabl' );
$j51_image_width_tabp		  = $params->get( 'j51_image_width_tabp' );
$j51_image_width_mobl		  = $params->get( 'j51_image_width_mobl' );
$j51_image_width_mobp		  = $params->get( 'j51_image_width_mobp' );
$j51_text_prev				    = $params->get( 'j51_text_prev' );
$j51_text_next				    = $params->get( 'j51_text_next' );
$j51_horiz_padding			  = $params->get( 'j51_horiz_padding' );
$j51_vert_padding			    = $params->get( 'j51_vert_padding' );
$j51_quote_bg			        = $params->get( 'j51_quote_bg', '#fff' );
$j51_quote_color			    = $params->get( 'j51_quote_color', '' );

require JModuleHelper::getLayoutPath('mod_j51testimonials', $params->get('layout', 'default'));
?>