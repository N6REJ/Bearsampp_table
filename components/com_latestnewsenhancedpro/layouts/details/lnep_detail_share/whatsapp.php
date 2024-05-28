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

$root_path = '';
$url = $item->link;

if ($item->isinternal) {

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
	echo '<a class="hasTooltip inline_svg whatsapp' . $classes . '" href="whatsapp://send?text=' . urlencode($root_path . $url) . '" data-share-network="whatsapp" aria-label="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "WhatsApp").'" title="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "WhatsApp").'">';
} else {
	echo '<a class="hasTooltip inline_svg whatsapp' . $classes . '" href="https://wa.me/?text=' . urlencode($root_path . $url) . '" rel="nofollow" data-share-network="whatsapp" aria-label="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "WhatsApp").'" title="' . Text::sprintf("COM_LATESTNEWSENHANCEDPRO_SHAREWITH", "WhatsApp").'" target="_blank" >';
}
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'M 412.9,97.1 C 371,55.1 315.2,32 255.9,32 c -122.4,0 -222,99.6 -222,222 0,39.1 10.2,77.3 29.6,111 L 32,480 149.7,449.1 c 32.4,17.7 68.9,27 106.1,27 h 0.1 c 122.3,0 224.1,-99.6 224.1,-222 0,-59.3 -25.2,-115 -67.1,-157 z m -157,341.6 c -33.2,0 -65.7,-8.9 -94,-25.7 l -6.7,-4 -69.8,18.3 18.6,-68.1 -4.4,-7 C 81.1,322.8 71.4,288.9 71.4,254 71.4,152.3 154.2,69.5 256,69.5 c 49.3,0 95.6,19.2 130.4,54.1 34.8,34.9 56.2,81.2 56.1,130.5 0,101.8 -84.9,184.6 -186.6,184.6 z M 357.1,300.5 c -5.5,-2.8 -32.8,-16.2 -37.9,-18 -5.1,-1.9 -8.8,-2.8 -12.5,2.8 -3.7,5.6 -14.3,18 -17.6,21.8 -3.2,3.7 -6.5,4.2 -12,1.4 -32.6,-16.3 -54,-29.1 -75.5,-66 -5.7,-9.8 5.7,-9.1 16.3,-30.3 1.8,-3.7 0.9,-6.9 -0.5,-9.7 -1.4,-2.8 -12.5,-30.1 -17.1,-41.2 -4.5,-10.8 -9.1,-9.3 -12.5,-9.5 -3.2,-0.2 -6.9,-0.2 -10.6,-0.2 -3.7,0 -9.7,1.4 -14.8,6.9 -5.1,5.6 -19.4,19 -19.4,46.3 0,27.3 19.9,53.7 22.6,57.4 2.8,3.7 39.1,59.7 94.8,83.8 35.2,15.2 49,16.5 66.6,13.9 10.7,-1.6 32.8,-13.4 37.4,-26.4 4.6,-13 4.6,-24.1 3.2,-26.4 -1.3,-2.5 -5,-3.9 -10.5,-6.6 z');
		echo SYWUtilities::getInlineSVG('whatsapp', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
