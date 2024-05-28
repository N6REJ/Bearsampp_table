<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.5.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$map_value = $field->value)
{
	return;
}

// Setup Variables
$coords = $map_value;

if ((is_string($map_value) && $map_value = json_decode($map_value, true)) || is_array($map_value))
{
	$coords = $map_value['coordinates'];
	
}
$coords = explode(',', $coords);

if (!isset($coords[1]))
{
	return;
}

\JHtml::_('behavior.core');

$width = $fieldParams->get('width', '400px');
$height = $fieldParams->get('height', '350px');
$zoom = $fieldParams->get('zoom', 4);
$extra_atts[] = 'data-marker-image="' . JURI::root() . 'media/plg_fields_acfosm/img/marker.png"';



// Add Media Files
JHtml::stylesheet('https://www.tassos.gr/media/products/advanced-custom-fields/ol.css');
JHtml::script('https://www.tassos.gr/media/products/advanced-custom-fields/ol.js');
JHtml::script('plg_fields_acfosm/acf_osm_map.js', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_fields_acfosm/acf_osm_map_loader.js', ['relative' => true, 'version' => 'auto']);

$buffer = '<div class="osm nr-address-component"><div class="osm_map_item" data-zoom="' . $zoom . '" data-lat="' . trim($coords[0]) . '" data-long="' . trim($coords[1]) . '" ' . implode(' ', $extra_atts) . ' style="width:' . $width . ';height:' . $height . ';max-width:100%;"></div>';



$buffer .= '</div>';

echo $buffer;