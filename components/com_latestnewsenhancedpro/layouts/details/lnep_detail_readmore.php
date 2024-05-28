<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
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
$icon = empty($icon) ? 'SYWicon-more': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_readmore' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

echo '<span class="detail_data">';

$readmore_text = Text::_('COM_LATESTNEWSENHANCEDPRO_READMORE_LABEL'); // default
if (!$item->authorized) {
	$readmore_text = Text::_('COM_LATESTNEWSENHANCEDPRO_UNAUTHORIZEDREADMORE_LABEL');
}

// parameters are different in module and views

$link_label_item = $item->authorized ? trim($params->get('link', '')) . trim($params->get('link_lbl', '')) : trim($params->get('unauthorized_link', '')) . trim($params->get('unauthorized_link_lbl', ''));
if (empty($link_label_item)) {
	//if (strpos($item->linktitle, rtrim($item->title, '.')) === false) {
		$link_label_item = $item->linktitle; // use the label from links a, b or c, if they exist
	//}
}

//if (!empty($link_label_item)) {
	$readmore_text = $link_label_item;
//}

$follow = $params->get('follow', true);
$popup_width = $params->get('popup_x', 600);
$popup_height = $params->get('popup_y', 500);
$tooltip = $params->get('readmore_tooltip', 1);

$bootstrap_version = $params->get('bootstrap_version', '');
if (empty($bootstrap_version)) {
	$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
	$bootstrap_version = $config_params->get('bootstrap_version', 'joomla');
}
if ($bootstrap_version === 'joomla') {
	$bootstrap_version = 5; //version_compare(JVERSION, '4.0.0', 'lt') ? 2 : 5;
} else {
	$bootstrap_version = intval($bootstrap_version);
}

echo Helper::getATag($item, $follow, $tooltip, $popup_width, $popup_height, '', '', true, $bootstrap_version).$readmore_text.'</a>';

echo '</span>';

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
