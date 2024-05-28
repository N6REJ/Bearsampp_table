<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$value = $field->value)
{
	return;
}

if (is_string($value) && !$value = json_decode($value, true))
{
	return;
}

$countdown_action = $fieldParams->get('action', 'keep');
$countdown_type = $fieldParams->get('countdown_type', 'static');

// Evergreen provides extra actions, allow to override the action on item editing page
if ($countdown_type === 'evergreen')
{
	$countdown_action = 'restart';
}

$preset_source = $fieldParams->get('preset_source', 'preset');
$preset = $fieldParams->get('preset', '1');

// Determine theme
$theme = $preset_source === 'custom' ? 'custom' : ($preset === '8' ? 'oneline' : 'default');

$payload = [
	// Field values
	'countdown_type' => $countdown_type,
	'value' => isset($value['value']) ? $value['value'] : null,
	'timezone' => $fieldParams->get('timezone', 'server'),
	'dynamic_days' => isset($value['dynamic_days']) ? $value['dynamic_days'] : null,
	'dynamic_hours' => isset($value['dynamic_hours']) ? $value['dynamic_hours'] : null,
	'dynamic_minutes' => isset($value['dynamic_minutes']) ? $value['dynamic_minutes'] : null,
	'dynamic_seconds' => isset($value['dynamic_seconds']) ? $value['dynamic_seconds'] : null,

	// Countdown End Action
	'finish_text' => $fieldParams->get('finish_text', ''),
	'redirect_url' => $fieldParams->get('redirect_url', ''),
	'countdown_action' => $countdown_action,

	// Preset
	'theme' => $theme,
	'format' => $fieldParams->get('format', ''),

	// Unit Display
	'days' => $fieldParams->get('days') === '1',
	'days_label' => $fieldParams->get('days_label'),
	'hours' => $fieldParams->get('hours') === '1',
	'hours_label' => $fieldParams->get('hours_label'),
	'minutes' => $fieldParams->get('minutes') === '1',
	'minutes_label' => $fieldParams->get('minutes_label'),
	'seconds' => $fieldParams->get('seconds') === '1',
	'seconds_label' => $fieldParams->get('seconds_label'),
	'separator' => $fieldParams->get('separator') === '1',
	'double_zeroes_format' => $fieldParams->get('double_zeroes_format') === '1',

	// Unit Item
	'item_size' => $fieldParams->get('item_size_responsive.item_size'),
	'item_padding' => $fieldParams->get('item_padding_control.item_padding'),
	'gap' => $fieldParams->get('item_gap.gap'),
	'item_border_style' => $fieldParams->get('border.style'),
	'item_border_width' => $fieldParams->get('border.width'),
	'item_border_color' => $fieldParams->get('border.color'),
	'item_background_color' => $fieldParams->get('item_background_color'),
	'item_border_radius' => $fieldParams->get('item_border_radius_control.item_border_radius'),

	// Unit Digits Container
	'digits_wrapper_min_width' => $fieldParams->get('digits_wrapper_custom_width') === '1' ? $fieldParams->get('digits_wrapper_min_width') : null,
	'digits_wrapper_padding' => $fieldParams->get('digits_wrapper_padding_control.digits_wrapper_padding'),
	'digits_wrapper_border_radius' => $fieldParams->get('digits_wrapper_border_radius_control.digits_wrapper_border_radius'),
	'digits_wrapper_background_color' => $fieldParams->get('digits_wrapper_background_color'),
	
	// Unit Digit
	'digits_font_size' => $fieldParams->get('digits_font_size_control.digits_font_size'),
	'digits_font_weight' => $fieldParams->get('digits_font_weight'),
	'digit_min_width' => $fieldParams->get('digits_custom_width') === '1' ? $fieldParams->get('digits_min_width') : null,
	'digits_padding' => $fieldParams->get('digits_padding_control.digits_padding'),
	'digit_border_radius' => $fieldParams->get('digits_border_radius_control.digits_border_radius'),
	'digits_gap' => $fieldParams->get('digits_gap_control.digits_gap'),
	'digit_background_color' => $fieldParams->get('digit_background_color'),
	'digit_text_color' => $fieldParams->get('digit_text_color'),

	// Unit Label
	'label_font_size' => $fieldParams->get('label_font_size_control.label_font_size'),
	'label_font_weight' => $fieldParams->get('label_font_weight'),
	'unit_label_margin_top' => $fieldParams->get('unit_label_margin_top'),
	'unit_label_text_color' => $fieldParams->get('unit_label_text_color'),
];

// Set custom layout
if ($field->params->get('acf_layout_override'))
{
	$payload['layout'] = $field->params->get('acf_layout_override');
}

echo \NRFramework\Widgets\Helper::render('Countdown', $payload);