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

//$title = htmlspecialchars($item->title);

$root_path = rtrim(Uri::root(), "/");

$url = str_replace(array("tmpl=component", "print=1"), "", $item->link);
$url = rtrim($url, "?&amp;");

$base_path = Uri::base(true);

// remove base path from item link if it is already there
if ($base_path && strpos($url, $base_path) === 0) {
	$url = substr($url, strlen($base_path));
}

echo '<a class="hasTooltip inline_svg mix' . $classes . '" href="https://mix.com/add?url=' . urlencode($root_path . $url) . '" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Mix") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Mix") . '" target="_blank">';
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'm 30,64 v 348.9 c 0,56.2 88,58.1 88,0 V 174.3 c 7.9,-52.9 88,-50.4 88,6.5 v 175.3 c 0,57.9 96,58 96,0 V 240 c 5.3,-54.7 88,-52.5 88,4.3 v 23.8 c 0,59.9 88,56.6 88,0 V 64 Z');
		echo SYWUtilities::getInlineSVG('mix', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
