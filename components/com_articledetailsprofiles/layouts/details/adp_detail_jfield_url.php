<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\Helper;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$field_id = $displayData['field_id'];
$field_type = $displayData['field_type'];
$field_label = $displayData['field_label'];
$field_values = $displayData['field_values']; // array
$field_options = $displayData['field_options'];
$field_params = $displayData['field_params'];

$field_data = implode(', ', $field_values);

if ($field_data) {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_'.$field_id);

    if (substr_count($default_label, 'ARTICLEDETAILS') > 0) {
        $default_label = '';
    }

	$link_type = '';
	$base = '';

	switch (explode(":", $field_data)[0])
	{
    	case 'http': case 'https': $link_type = 'http'; break;
    	case 'ftp': case 'ftps': case 'file': break;
    	case 'mailto': $link_type = 'mailto'; break;
    	default:
       		$link_type = 'intern';
        	$base = Uri::base();
	}

	$url_label = $field_data;

	if ($field_options->showlabel) {
    	$url_label = $field_label;
	}

	$attributes = '';

	if ($link_type === 'http' || $link_type === 'intern') {
    	if (!Uri::isInternal($base.$field_data)) {
        	$attributes = ' rel="nofollow noopener noreferrer" target="_blank"';
    	}

    	if (!$params->get('protocol', 1) && !$field_options->showlabel) {
    	    $url_label = Helper::remove_protocol($field_data);
    	}
	}

	$label = isset($displayData['label']) ? $displayData['label'] : '';
	$label = empty($label) ? $default_label : $label;

	$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

	$show_icon = $displayData['show_icon'];
	$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
	$icon = empty($icon) ? ((strpos($field_data, 'mailto') === 0) ? 'SYWicon-mail' : 'SYWicon-link') : $icon;
	$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

	// using 'echo' to prevent spaces if using direct html tags

	echo '<span class="detail detail_jfield_' . $field_type . '  detail_jfield_' . $field_id . $extraclasses . '">';

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
	echo '<a href="' . htmlspecialchars($field_data) . '"' . $attributes . '>' . htmlspecialchars($url_label) . '</a>';
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
}
