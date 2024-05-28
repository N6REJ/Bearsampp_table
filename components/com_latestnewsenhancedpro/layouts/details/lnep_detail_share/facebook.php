<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 * @license		SVG icon licensed under the Creative Commons Attribution 4.0 International license https://fontawesome.com/license
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Utilities as SYWUtilities;

$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$classes = trim($params->get('share_classes', ''));
$classes = empty($classes) ? '' : ' '.$classes;

$item = $displayData['item'];

$title = htmlspecialchars($item->title);

$root_path = '';
$url = $item->link;

if ($item->isinternal) {

	//$root_path = rtrim(Uri::root(), "/");

	if ($item->link == 'inline') {
		$url = Uri::current();
	} else {
		$root_path = Uri::root();
	}

	$base_path = Uri::base(true);

	// remove base path from item link if it is already there
	if ($base_path && strpos($url, $base_path) === 0) {
		$url = substr($url, strlen($base_path));
	}

	$url = ltrim($url, "/");
}

$url = str_replace(array("tmpl=component", "print=1"), "", $url);
$url = rtrim($url, "?&amp;");

if (SYWUtilities::isMobile()) {
	echo '<a class="hasTooltip inline_svg facebook' . $classes . '" href="fb-messenger://share?link=' . urlencode($root_path . $url) . '" aria-label="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "Facebook") . '" title="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "Facebook") . '">';
} else {
	echo '<a class="hasTooltip inline_svg facebook' . $classes . '" href="https://www.facebook.com/sharer.php?u=' . urlencode($root_path . $url) . '&amp;t='.urlencode($title).'" aria-label="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "Facebook") . '" title="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "Facebook") . '" target="_blank">';
}
	echo '<span class="svg_container">';

		$path_attributes = array('fill' => 'currentColor', 'd' => 'm 371.14,288 14.22,-92.66 h -88.91 v -60.13 c 0,-25.35 12.42,-50.06 52.24,-50.06 h 40.42 V 6.26 C 389.11,6.26 352.43,0 317.36,0 244.14,0 196.28,44.38 196.28,124.72 v 70.62 H 114.89 V 288 h 81.39 V 512 H 296.45 V 288 Z');
		echo SYWUtilities::getInlineSVG('facebook', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
