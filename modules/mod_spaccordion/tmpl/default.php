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

if (!$value = $params->get('value'))
{
	return;
}

$value = array_values(json_decode(json_encode($value), true));

$arrayParams = $params->toArray();

$payload = [
	'value' => $value,
	'density' => $params->get('density', 'default'),
	'font_size' => isset($arrayParams['font_size']) ? $arrayParams['font_size'] : 16,
	'gap' => $params->get('gap', 'none'),
	'panel_background_color' => $params->get('panel_background_color', '#fff'),
	'text_color' => $params->get('text_color', '#333'),
	'border_color' => $params->get('border_color', '#ddd'),
	'rounded_corners' => $params->get('rounded_corners', 'small'),
	'show_icon' => $params->get('show_icon', 'left'),
	'icon' => $params->get('icon', 'arrow'),
	'initial_state' => $params->get('initial_state', 'collapsed'),
	'only_one_panel_expanded' => $params->get('only_one_panel_expanded', '0') === '1',
];



echo \NRFramework\Widgets\Helper::render('Accordion', $payload);