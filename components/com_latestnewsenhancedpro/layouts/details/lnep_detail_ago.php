<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
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
$icon = empty($icon) ? 'SYWicon-calendar': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

$additional_class = '';
if (empty($item->date)) {
	$additional_class = ' nodate';
}

echo '<span class="detail detail_ago' . $additional_class . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($item->date) {
	$now = new Date();
	$item_date = new Date($item->date);

	$date_in_future = false;
	if ($item_date->toUnix() > $now->toUnix()) {
		$date_in_future = true;
	}

	echo '<span class="detail_data">';

	if ($item->nbr_years > 0) {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INYEARSMONTHSDAYSONLY', $item->nbr_years, $item->nbr_months, $item->nbr_days);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_YEARSMONTHSDAYSAGO', $item->nbr_years, $item->nbr_months, $item->nbr_days);
		}
	} else if ($item->nbr_months > 0) {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INMONTHSDAYSONLY', $item->nbr_months, $item->nbr_days);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_MONTHSDAYSAGO', $item->nbr_months, $item->nbr_days);
		}
	} else if ($item->nbr_days == 0) {
		echo Text::_('COM_LATESTNEWSENHANCEDPRO_TODAY');
	} else if ($item->nbr_days == 1) {
	    if ($date_in_future) {
			echo Text::_('COM_LATESTNEWSENHANCEDPRO_TOMORROW');
		} else {
			echo Text::_('COM_LATESTNEWSENHANCEDPRO_YESTERDAY');
		}
	} else {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INDAYSONLY', $item->nbr_days);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_DAYSAGO', $item->nbr_days);
		}
	}

	echo '</span>';
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
