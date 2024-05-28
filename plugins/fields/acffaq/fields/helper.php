<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

$presets = [
	1 => [
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'show_toggle_icon' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'icon' => [
			'type' => 'radio',
			'value' => 'arrow'
		],
		'icon_position' => [
			'type' => 'radio',
			'value' => 'right'
		],
	],
	2 => [
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'show_toggle_icon' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'icon' => [
			'type' => 'radio',
			'value' => 'plus_minus'
		],
		'icon_position' => [
			'type' => 'radio',
			'value' => 'left'
		],
	],
	3 => [
		'initial_state' => [
			'type' => 'list',
			'value' => 'all-open'
		],
		'keep_one_question_open' => [
			'type' => 'nrtoggle',
			'value' => false
		],
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'show_toggle_icon' => [
			'type' => 'nrtoggle',
			'value' => false
		]
	],
	4 => [
		'separator' => [
			'type' => 'nrtoggle',
			'value' => false
		],
		'background_color' => [
			'type' => 'text',
			'value' => '#fff'
		],
		'item_padding' => [
			'type' => 'number',
			'responsive' => true,
			'dimensions' => true,
			'value' => 20
		],
		'item_gap' => [
			'type' => 'number',
			'responsive' => true,
			'value' => 14
		],
		'show_toggle_icon' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'icon' => [
			'type' => 'radio',
			'value' => 'arrow'
		],
		'icon_position' => [
			'type' => 'radio',
			'value' => 'right'
		],
	]
];