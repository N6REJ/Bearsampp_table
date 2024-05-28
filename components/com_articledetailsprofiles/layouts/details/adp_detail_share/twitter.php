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
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$classes = trim($params->get('share_classes', ''));
$classes = empty($classes) ? '' : ' '.$classes;

$item = $displayData['item'];

$title = htmlspecialchars($item->title);

$root_path = rtrim(Uri::root(), "/");

$url = str_replace(array("tmpl=component", "print=1"), "", $item->link);
$url = rtrim($url, "?&amp;");

$base_path = Uri::base(true);

// remove base path from item link if it is already there
if ($base_path && strpos($url, $base_path) === 0) {
	$url = substr($url, strlen($base_path));
}

echo '<a class="hasTooltip inline_svg twitter' . $classes . '" href="https://twitter.com/intent/tweet?text=' . urlencode($title) . '&amp;url=' . urlencode($root_path . $url) . '" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "X-Twitter") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "X-Twitter") . '" target="_blank">';
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z');
		echo SYWUtilities::getInlineSVG('twitter', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
