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

if (!isset($value['value']) && !is_array($value['value']))
{
	return;
}

// Prepare the value
$value = array_values($value['value']);
$value = array_filter($value, function($item) {
	return !empty($item['question']) && !empty($item['answer']);
});

$payload = [
	'value' => $value,
	'css_class' => ' template_' . $fieldParams->get('template'),
	'keep_one_question_open' => $fieldParams->get('keep_one_question_open', '0') === '1',
	'columns' => (int) $fieldParams->get('columns', 1),
	'item_gap' => $fieldParams->get('item_gap_control.item_gap', 20),
	'column_gap' => $fieldParams->get('column_gap_control.column_gap', 20),
	'item_background_color' => $fieldParams->get('background_color'),
	'item_border_radius' => $fieldParams->get('border_radius_control.item_border_radius'),
	'item_padding' => $fieldParams->get('padding_control.item_padding'),
	'question_font_size' => $fieldParams->get('question_font_size_control.question_font_size'),
	'question_text_color' => $fieldParams->get('question_text_color'),
	'answer_font_size' => $fieldParams->get('answer_font_size_control.answer_font_size'),
	'answer_text_color' => $fieldParams->get('answer_text_color'),
	'answer_padding' => $fieldParams->get('answer_padding_control.answer_padding'),
	'generate_faq' => $fieldParams->get('generate_faq', '0') === '1',
	'separator' => $fieldParams->get('separator', '0') === '1',
	'separator_color' => $fieldParams->get('separator_color'),
	'initial_state' => $fieldParams->get('initial_state', 'first-open')
];

// Set custom layout
if ($field->params->get('acf_layout_override'))
{
	$payload['layout'] = $field->params->get('acf_layout_override');
}

$show_toggle_icon = $fieldParams->get('show_toggle_icon', '1') === '1';
$payload['show_toggle_icon'] = $show_toggle_icon;
if ($show_toggle_icon)
{
	$payload['icon'] = $fieldParams->get('icon', 'arrow');
	$payload['icon_position'] = $fieldParams->get('icon_position', 'right');
}

echo \NRFramework\Widgets\Helper::render('FAQ', $payload);