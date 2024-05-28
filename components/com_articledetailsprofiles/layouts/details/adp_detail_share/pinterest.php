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
use Joomla\Registry\Registry;
use SYW\Library\Utilities as SYWUtilities;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$classes = trim($params->get('share_classes', ''));
$classes = empty($classes) ? '' : ' '.$classes;

$item = $displayData['item'];

$image_url = '';
if (isset($item->images)) {
	$registry = new Registry();
	$registry->loadString($item->images);
	$images_array = $registry->toArray();

	if (trim($images_array['image_intro']) != '') {
		$image_url = trim($images_array['image_intro']);
	} else if (trim($images_array['image_fulltext']) != '') {
		$image_url = trim($images_array['image_fulltext']);
	}
}

$title = htmlspecialchars($item->title);

$root_path = rtrim(Uri::root(), "/");

$url = str_replace(array("tmpl=component", "print=1"), "", $item->link);
$url = rtrim($url, "?&amp;");

$base_path = Uri::base(true);

// remove base path from item link if it is already there
if ($base_path && strpos($url, $base_path) === 0) {
	$url = substr($url, strlen($base_path));
}

echo '<a class="hasTooltip inline_svg pinterest' . $classes . '" href="https://pinterest.com/pin/create/button/?url=' . urlencode($root_path . $url) . '&amp;description=' . urlencode($title) . ($image_url ? '&amp;media=' . urlencode($root_path.'/'.$image_url) : '') . '" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Pinterest") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Pinterest") . '" target="_blank">';
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'M 268,6.5 C 165.4,6.5 64,74.9 64,185.6 64,256 103.6,296 127.6,296 c 9.9,0 15.6,-27.6 15.6,-35.4 0,-9.3 -23.7,-29.1 -23.7,-67.8 0,-80.4 61.2,-137.4 140.4,-137.4 68.1,0 118.5,38.7 118.5,109.8 0,53.1 -21.3,152.7 -90.3,152.7 -24.9,0 -46.2,-18 -46.2,-43.8 0,-37.8 26.4,-74.4 26.4,-113.4 0,-66.2 -93.9,-54.2 -93.9,25.8 0,16.8 2.1,35.4 9.6,50.7 -13.8,59.4 -42,147.9 -42,209.1 0,18.9 2.7,37.5 4.5,56.4 3.4,3.8 1.7,3.4 6.9,1.5 50.4,-69 48.6,-82.5 71.4,-172.8 12.3,23.4 44.1,36 69.3,36 C 400.3,367.4 448,263.9 448,170.6 448,71.3 362.2,6.5 268,6.5 Z');
		echo SYWUtilities::getInlineSVG('pinterest', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
