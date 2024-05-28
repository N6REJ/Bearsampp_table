<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

use SYW\Plugin\Content\ArticleDetailsProfiles\Helper;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
$item_params = $displayData['item_params'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_ARTICLEDETAILS_MYDETAILS_PREPEND_URL') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-network': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// check the list of available icons at http://www.simplifyyourweb.com/documentation/common-features/icon-picker-common

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_mydetails_url' . $extraclasses . '">'; // convention: detail_[information detail type]

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl')
{
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl')
{
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl')
{
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

echo '<span class="detail_data">';

	echo '<a href="https://simplifyyourweb.com" target="_blank" alt="Simplify Your Web" class="hasTooltip" title="Simplify Your Web">' . Text::_('PLG_ARTICLEDETAILS_MYDETAILS_URLTEXT') . '</a>';

echo '</span>';

if ($postinfo && Factory::getDocument()->getDirection() != 'rtl')
{
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

if ($label && Factory::getDocument()->getDirection() == 'rtl')
{
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl')
{
	echo '<i class="' . $icon . '"></i>';
}

echo '</span>';