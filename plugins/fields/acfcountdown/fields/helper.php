<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$presets = [
	1 => [
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => 25, 'mobile' => '']
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 15, 'tablet' => 13, 'mobile' => '']
		],
		'gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 10, 'tablet' => 0, 'mobile' => '']
		],
		'minutes_label' => [
			'type' => 'text',
			'value' => Text::_('ACF_COUNTDOWN_MINS')
		],
		'seconds_label' => [
			'type' => 'text',
			'value' => Text::_('ACF_COUNTDOWN_SECS')
		],
		'digits_wrapper_custom_width' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'digits_wrapper_min_width' => [
			'type' => 'number',
			'value' => 50
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 400
		],
		'unit_label_margin_top' => [
			'type' => 'number',
			'value' => 9
		]
	],
	2 => [
		'item_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => 74
		],
		'[border][style]' => [
			'type' => 'list',
			'value' => 'solid'
		],
		'[border][width]' => [
			'type' => 'number',
			'value' => 2
		],
		'[border][color]' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'item_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 9]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => '', 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 12, 'tablet' => '', 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		]
	],
	3 => [
		'days_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('NR_DAYS'))
		],
		'hours_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('NR_HOURS'))
		],
		'minutes_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('ACF_COUNTDOWN_MINS'))
		],
		'seconds_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('ACF_COUNTDOWN_SECS'))
		],
		'item_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => 84
		],
		'[border][style]' => [
			'type' => 'list',
			'value' => 'solid'
		],
		'[border][width]' => [
			'type' => 'number',
			'value' => 2
		],
		'[border][color]' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'item_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 100]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => '', 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 12, 'tablet' => '', 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		]
	],
	4 => [
		'item_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => 74
		],
		'item_background_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'item_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 9]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => '', 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#fff'
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 12, 'tablet' => '', 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#d2d5ee'
		]
	],
	5 => [
		'days' => [
			'type' => 'nrtoggle',
			'value' => false
		],
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 5]
		],
		'digits_gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 3]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => '', 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#fff'
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 15, 'tablet' => 13, 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 400
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'unit_label_margin_top' => [
			'type' => 'number',
			'value' => 8
		],
		'digits_custom_width' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'digits_min_width' => [
			'type' => 'number',
			'value' => 39
		],
		'digits_padding' => [
			'responsive' => true,
			'dimensions' => true,
			'type' => 'number',
			'value' => ['desktop' => ['top' => 9, 'right' => 5, 'bottom' => 9, 'left' => 5]]
		],
		'digit_background_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'digits_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 9]
		]
	],
	6 => [
		'minutes_label' => [
			'type' => 'text',
			'value' => Text::_('ACF_COUNTDOWN_MINS')
		],
		'seconds_label' => [
			'type' => 'text',
			'value' => Text::_('ACF_COUNTDOWN_SECS')
		],
		'digits_wrapper_custom_width' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'digits_wrapper_min_width' => [
			'type' => 'number',
			'value' => 59
		],
		'digits_wrapper_padding' => [
			'responsive' => true,
			'dimensions' => true,
			'type' => 'number',
			'value' => ['desktop' => 9]
		],
		'digits_wrapper_background_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#fff'
		],
		'digits_wrapper_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 5]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => 25, 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 15, 'tablet' => 13, 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 400
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'unit_label_margin_top' => [
			'type' => 'number',
			'value' => 8
		]
	],
	7 => [
		'days_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('NR_DAYS'))
		],
		'hours_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('NR_HOURS'))
		],
		'minutes_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('ACF_COUNTDOWN_MINS'))
		],
		'seconds_label' => [
			'type' => 'text',
			'value' => strtoupper(Text::_('ACF_COUNTDOWN_SECS'))
		],
		'separator' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'item_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => 90
		],
		'item_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 7]
		],
		'item_background_color' => [
			'type' => 'text',
			'value' => '#f6f6f6'
		],
		'gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 5]
		],
		'digits_gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 3]
		],
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 30, 'tablet' => 25, 'mobile' => '']
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		],
		'digit_text_color' => [
			'type' => 'text',
			'value' => '#fff'
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 12, 'tablet' => '', 'mobile' => '']
		],
		'label_font_weight' => [
			'type' => 'list',
			'value' => 600
		],
		'unit_label_text_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'unit_label_margin_top' => [
			'type' => 'number',
			'value' => 9
		],
		'digits_custom_width' => [
			'type' => 'nrtoggle',
			'value' => true
		],
		'digits_min_width' => [
			'type' => 'number',
			'value' => 30
		],
		'digits_padding' => [
			'responsive' => true,
			'dimensions' => true,
			'type' => 'number',
			'value' => ['desktop' => 5]
		],
		'digit_background_color' => [
			'type' => 'text',
			'value' => '#41495b'
		],
		'digits_border_radius' => [
			'responsive' => true,
			'dimensions' => true,
			'border_radius' => true,
			'type' => 'number',
			'value' => ['desktop' => 7]
		]
	],
	8 => [
		'digits_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 16, 'tablet' => '', 'mobile' => '']
		],
		'label_font_size' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ['desktop' => 16, 'tablet' => '', 'mobile' => '']
		],
		'gap' => [
			'responsive' => true,
			'type' => 'number',
			'value' => ''
		],
		'days_label' => [
			'type' => 'text',
			'value' => ' ' . strtolower(Text::_('NR_DAYS'))
		],
		'hours_label' => [
			'type' => 'text',
			'value' => ' ' . strtolower(Text::_('NR_HOURS'))
		],
		'minutes_label' => [
			'type' => 'text',
			'value' => ' ' . strtolower(Text::_('NR_MINUTES'))
		],
		'seconds_label' => [
			'type' => 'text',
			'value' => ' ' . strtolower(Text::_('NR_SECONDS'))
		],
		'digits_font_weight' => [
			'type' => 'list',
			'value' => 500
		]
	]
];