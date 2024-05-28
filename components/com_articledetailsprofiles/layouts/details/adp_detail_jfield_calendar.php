<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$field_id = $displayData['field_id'];
$field_type = $displayData['field_type'];
$field_label = $displayData['field_label'];
$field_values = $displayData['field_values']; // array
$field_options = $displayData['field_options'];
$field_params = $displayData['field_params'];

$field_data = implode(', ', $field_values);

if ($field_data) {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_'.$field_id);

    if (substr_count($default_label, 'ARTICLEDETAILS') > 0) {
        $default_label = '';
    }

	$label = isset($displayData['label']) ? $displayData['label'] : '';
	$label = empty($label) ? $default_label : $label;

	$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

	$show_icon = $displayData['show_icon'];
	$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
	$icon = empty($icon) ? 'SYWicon-calendar': $icon;
	$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

	$date_format = Text::_('PLG_CONTENT_ARTICLEDETAILS_FORMAT_DATE');

	if (empty($date_format)) {
	    $date_format = $params->get('d_format', 'd F Y');
	}

	// using 'echo' to prevent spaces if using direct html tags

	echo '<span class="detail detail_jfield_' . $field_type . '  detail_jfield_' . $field_id . $extraclasses . '">';

	if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="' . $icon . '"></i>';
	}

	if ($label && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<span class="detail_post">' . $postinfo . '</span>';
	}

	echo '<span class="detail_data">' . HTMLHelper::_('date', $field_data, $date_format) . '</span>';

	if ($postinfo && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<span class="detail_post">' . $postinfo . '</span>';
	}

	if ($label && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="' . $icon . '"></i>';
	}

	echo '</span>';
}
