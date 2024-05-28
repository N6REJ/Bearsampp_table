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

echo '<a class="hasTooltip inline_svg linkedin' . $classes . '" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=' . urlencode($root_path . $url) . '&amp;title=' . urlencode($title) . '" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "LinkedIn") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "LinkedIn") . '" target="_blank">';
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'M 132.28,448 H 39.4 V 148.9 h 92.88 z M 85.79,108.1 C 56.09,108.1 32,83.5 32,53.8 a 53.79,53.79 0 0 1 107.58,0 c 0,29.7 -24.1,54.3 -53.79,54.3 z M 479.9,448 H 387.22 V 302.4 c 0,-34.7 -0.7,-79.2 -48.29,-79.2 -48.29,0 -55.69,37.7 -55.69,76.7 V 448 H 190.46 V 148.9 h 89.08 v 40.8 h 1.3 c 12.4,-23.5 42.69,-48.3 87.88,-48.3 94,0 111.28,61.9 111.28,142.3 V 448 Z');
		echo SYWUtilities::getInlineSVG('linkedin', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
