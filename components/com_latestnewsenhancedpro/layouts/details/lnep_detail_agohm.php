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
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INYEARSMONTHSDAYSHOURSMINUTES', $item->nbr_years, $item->nbr_months, $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_YEARSMONTHSDAYSHOURSMINUTESAGO', $item->nbr_years, $item->nbr_months, $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		}
	} elseif ($item->nbr_months > 0) {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INMONTHSDAYSHOURSMINUTES', $item->nbr_months, $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_MONTHSDAYSHOURSMINUTESAGO', $item->nbr_months, $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		}
	} else if ($item->nbr_days > 0) {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INDAYSHOURSMINUTES', $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_DAYSHOURSMINUTESAGO', $item->nbr_days, $item->nbr_hours, $item->nbr_minutes);
		}
	} else if ($item->nbr_hours > 0) {
	    if ($date_in_future) {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INHOURSMINUTES', $item->nbr_hours, $item->nbr_minutes);
		} else {
			echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_HOURSMINUTESAGO', $item->nbr_hours, $item->nbr_minutes);
		}
	} else {
		if ($item->nbr_minutes == 1) {
		    if ($date_in_future) {
				echo Text::_('COM_LATESTNEWSENHANCEDPRO_INAMINUTE');
			} else {
				echo Text::_('COM_LATESTNEWSENHANCEDPRO_MINUTEAGO');
			}
		} else {
		    if ($date_in_future) {
				echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_INMINUTES', $item->nbr_minutes);
			} else {
				echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_MINUTESAGO', $item->nbr_minutes);
			}
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
