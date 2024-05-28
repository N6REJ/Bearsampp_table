<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$field_id = $displayData['field_id'];
$field_type = $displayData['field_type'];
$field_values = $displayData['field_values'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-calendar': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

$additional_class = '';
if (empty($item->date)) {
	$additional_class = ' nodate';
}

echo '<span class="detail detail_k2field_'.$field_type.' detail_k2field_'.$field_id.$additional_class.$extraclasses.'">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($field_values) {

	$offset = true; // default
	if (defined('K2_JVERSION')) {
		$offset = (K2_JVERSION != '15') ? null : 0;
	}

	echo '<span class="detail_data">'.HTMLHelper::_('date', $field_values, $params->get('d_format', 'd F Y'), $offset).'</span>';
} else {
	echo '<span class="detail_data">-</span>';
}

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
