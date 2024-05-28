<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

\JLoader::register('K2Model', JPATH_ADMINISTRATOR.'/components/com_k2/models/model.php');

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$linked = $displayData['linked'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-comment': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_k2commentscount' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

echo '<span class="detail_data">';

K2Model::addIncludePath(JPATH_SITE.'/components/com_k2/models');
$model = K2Model::getInstance('Item', 'K2Model');

$comments_count = $model->countItemComments($item->id);

if ($linked && isset($item->link) && !empty($item->link) && $item->authorized) {
	$temp_item = clone $item;
	$temp_item->linktitle = Text::_('COM_LATESTNEWSENHANCEDPRO_GOTOCOMMENTS');

	$follow = $params->get('follow', true);
	$popup_width = $params->get('popup_x', 600);
	$popup_height = $params->get('popup_y', 500);
	$tooltip = $params->get('readmore_tooltip', 1);

	echo Helper::getATag($temp_item, $follow, $tooltip, $popup_width, $popup_height, '', '#itemCommentsAnchor', false);
	if ($comments_count > 0) {
		echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_COMMENTS', $comments_count);
	} else {
		echo Text::_('COM_LATESTNEWSENHANCEDPRO_NOCOMMENTS');
	}
	echo '</a>';
} else {
	if ($comments_count > 0) {
		echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_COMMENTS', $comments_count);
	} else {
		echo Text::_('COM_LATESTNEWSENHANCEDPRO_NOCOMMENTS');
	}
}

echo '</span>';

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
