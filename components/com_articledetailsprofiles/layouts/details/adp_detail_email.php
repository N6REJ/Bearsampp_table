<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$title = htmlspecialchars($item->title);

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_EMAIL') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-email': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_email' . $extraclasses . '">';

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

$root_path = rtrim(Uri::root(), "/");

$url = str_replace(array("tmpl=component", "print=1"), "", $item->link);
$url = rtrim($url, "?&amp;");

$base_path = Uri::base(true);

// remove base path from item link if it is already there
if ($base_path && strpos($url, $base_path) === 0) {
    $url = substr($url, strlen($base_path));
}

$attribs = array(
    'title'   => Text::_('JGLOBAL_EMAIL'),
    'class' => 'hasTooltip'
);

$text = '<i class="SYWicon-email"></i><span>' . Text::_('JGLOBAL_EMAIL') . '</span>';

echo HTMLHelper::_('link', 'mailto:?subject=' . urlencode($title) . '&amp;body=' . urlencode($root_path . $url), $text, $attribs);

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
