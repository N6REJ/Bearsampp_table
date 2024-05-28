<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$field_id = $displayData['field_id'];
$field_type = $displayData['field_type'];
$field_label = $displayData['field_label'];
$field_values = $displayData['field_values']; // array
$field_options = $displayData['field_options'];
$field_params = $displayData['field_params'];

$field_data = $field_values[0]; // only one value

$link_type = '';
$base = '';
$attributes = '';

switch (explode(":", $field_data)[0])
{
    case 'http': case 'https':
        $link_type = 'http';
        break;
    case 'ftp': case 'ftps': case 'file':
        break;
    case 'mailto':
        $link_type = 'mailto';
        break;
    default:
        $link_type = 'intern';
        $base = Uri::base();
}

$url_label = $field_data;
// if ($field_options->showlabel) {
//     $url_label = $field_label;
// }

//if (in_array('http', $field_params->schemes) || in_array('https', $field_params->schemes)) {
if ($link_type === 'http' || $link_type === 'intern') {

    if (!Uri::isInternal($base.$field_data)) {
        $attributes = ' rel="nofollow noopener noreferrer" target="_blank"';
    }

    if (!$params->get('protocol', 1) /* && !$field_options->showlabel */) {
        $url_label = Helper::remove_protocol($field_data);
    }
} elseif ($link_type === 'mailto') {
    $url_label = str_replace('mailto:', '', $url_label);
}

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? ((strpos($field_data, 'mailto') === 0) ? 'SYWicon-mail' : 'SYWicon-link') : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_jfield_'.$field_type.' detail_jfield_'.$field_id.$extraclasses.'">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

echo '<span class="detail_data">';

echo '<a href="'.htmlspecialchars($field_data).'"'.$attributes.'>'.htmlspecialchars($url_label).'</a>';

echo '</span>';

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
