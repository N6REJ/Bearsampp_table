<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';

$icon_default = 'star-outline';
if (!empty($item->vote)) {
	if ($item->vote == 5) {
		$icon_default = 'SYWicon-star';
	} else {
		$icon_default = 'SYWicon-star-half';
	}
}

$icon = empty($icon) ? $icon_default : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_rating' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

echo '<span class="detail_data">';

if (!empty($item->vote)) {
	if ($params->get('show_rating') == 'text') {
		echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_RATING', $item->vote).' ';
		echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_FROMUSERS', $item->vote_count);
		// echo $item->vote.'/5 '.JText::sprintf('COM_LATESTNEWSENHANCEDPRO_FROMUSERS', $item->vote_count);
	} else { // use stars

		$whole = intval($item->vote);

		$stars = '';
		for ($i = 0; $i < $whole; $i++) {
			$stars .= '<i class="SYWicon-star" aria-hidden="true"></i>';
		}

		if ($whole < 5) {

			// get fraction

			$fraction = $item->vote - $whole;
			if ($fraction > .4) {
				$stars .= '<i class="SYWicon-star-half" aria-hidden="true"></i>';
			} else {
				$stars .= '<i class="SYWicon-star-outline" aria-hidden="true"></i>';
			}

			for ($i = $whole + 1; $i < 5; $i++) {
				$stars .= '<i class="SYWicon-star-outline" aria-hidden="true"></i>';
			}
		}

		echo $stars;
	}
} else {
	echo Text::_('COM_LATESTNEWSENHANCEDPRO_NORATING');
}

echo '</span>';

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
