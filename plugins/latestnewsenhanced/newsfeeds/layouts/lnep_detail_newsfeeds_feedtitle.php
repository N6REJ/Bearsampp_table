<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

$lang = Factory::getLanguage();
$lang->load('plg_latestnewsenhanced_newsfeeds', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-rss': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_newsfeeds_feedtitle' . $extraclasses . '">'; // convention: detail_[information detail type]

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
    echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
    echo '<span class="detail_label">' . $label . '</span>';
}

if ($item->feedurl) {
	echo '<span class="detail_data"><a href="' . $item->feedurl . '" target="_blank">' . $item->feedtitle . '</a></span>';
} else {
	echo '<span class="detail_data">' . $item->feedtitle . '</span>';
}

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
    echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
    echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
