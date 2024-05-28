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

$date = $displayData['date'];
$date_type = $displayData['date_type'];

$item = $displayData['item'];

$label = isset($displayData['label']) ? $displayData['label'] : '';

$default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_PUBLISHEDTIME');

if ($date_type == 'createdtime') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_CREATEDTIME');
} else if ($date_type == 'modifiedtime') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_MODIFIEDTIME');
} else if ($date_type == 'finishedtime') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_FINISHEDTIME');
}

$label = empty($label) ? $default_label : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-clock': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_time' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

echo '<span class="detail_data">';

$time_format = Text::_('PLG_CONTENT_ARTICLEDETAILS_FORMAT_TIME');

if (empty($time_format)) {
    $time_format = $params->get('t_format', 'H:i');
}

echo HTMLHelper::_('date', $date, $time_format);

echo '</span>';

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
