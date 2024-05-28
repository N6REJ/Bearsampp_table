<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-description': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';
$is_leading = $displayData['is_leading'];

$heading = $params->get('title_tag', 4);
if ($is_leading) {
    $heading = $params->get('leading_title_tag', 4);
}

$heading_tag = 'span';
if ($heading > 0) {
    $heading_tag = 'h' . $heading;
}

$title_class = trim($params->get('title_class', ''));
if ($is_leading) {
    $title_class = trim($params->get('leading_title_class', ''));
}

$title_class_attribute = $title_class ? ' class="' . $title_class . '"' : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_title' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

echo '<span class="detail_data">';

$follow = $params->get('follow', true);
$popup_width = $params->get('popup_x', 600);
$popup_height = $params->get('popup_y', 500);
$tooltip = $params->get('readmore_tooltip', 1);

$link_title = false;
$what_to_link = $params->get('what_to_link', '');
if (is_array($what_to_link)) {
    foreach ($what_to_link as $choice) {
        if ($choice === 'title') {
            $link_title = true;
            break;
        }
    }
}

$bootstrap_version = $params->get('bootstrap_version', '');
if (empty($bootstrap_version)) {
	$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
	$bootstrap_version = $config_params->get('bootstrap_version', 'joomla');
}
if ($bootstrap_version === 'joomla') {
	$bootstrap_version = 5;
} else {
	$bootstrap_version = intval($bootstrap_version);
}

if ($item->link && $link_title && $item->cropped) {
    echo Helper::getATag($item, $follow, $tooltip, $popup_width, $popup_height, '', '', true, $bootstrap_version). '<' . $heading_tag . $title_class_attribute . '>' . $item->title . '</' . $heading_tag . '></a>';
} else {
    echo '<' . $heading_tag . $title_class_attribute . '>' . $item->title . '</' . $heading_tag . '>';
}

echo '</span>';

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
