<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\Helper;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$link = $displayData['link']; // a, b or c

$url_target = $displayData['url_target'];
$url_text = $displayData['url_text'];
$url_link = $displayData['url_link'];

$globalparams = ComponentHelper::getParams('com_content');

$target = $globalparams->get('target'.$link, 0);

if (!empty($url_target)) {
    $target = $url_target;
}

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_LINK') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-link': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_link detail_link' . $link . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

if (!Factory::getApplication()->input->getBool('print')) {
    echo Helper::getATagLinks($url_link, $url_text, $target, false, '600', '500', 'detail_data');
} else {
    echo '<span class="detail_data">';
}

if (!empty($url_text)) {
    echo $url_text;
} else {
	if (!$params->get('protocol', 1)) {
	    echo Helper::remove_protocol($url_link);
	} else {
	    echo $url_link;
	}
}

if (!Factory::getApplication()->input->getBool('print')) {
    echo '</a>';
} else {
    echo '</span>';
}

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
