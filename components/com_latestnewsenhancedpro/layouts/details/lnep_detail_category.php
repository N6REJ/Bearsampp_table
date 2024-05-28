<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];

$linked = $displayData['linked'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

if (!$linked) {
	$icon_default = 'SYWicon-folder';
} else {
	$icon_default = 'SYWicon-folder-open';
}

$icon = empty($icon) ? $icon_default : $icon;

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_category' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if (!$linked) {
	echo '<span class="detail_data">' . $item->category_title . '</span>';
} else {
    
    if ($item->category_authorized) {
        $cat_label = trim($params->get('cat_link_lbl', '')) !== '' ? $params->get('cat_link_lbl', '') : $item->category_title;
    } else {
        $cat_label = trim($params->get('unauthorized_cat_link_lbl', '')) !== '' ? $params->get('unauthorized_cat_link_lbl', '') : $item->category_title;
    }
    
	if (isset($item->catlink) && !empty($item->catlink)) { // includes link to view
		echo '<a class="detail_data" href="' . $item->catlink . '">' . $cat_label . '</a>';
	} else if ($params->get('link_cat_to', 'none') == 'self') { // link to self (view only)
		echo '<a href="" onclick="document.adminForm.category.value=' . $item->catid . '; document.adminForm.submit(); return false;">' . $cat_label . '</a>';
	} else {
		echo '<span class="detail_data">' . $item->category_title . '</span>';
	}
}

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
