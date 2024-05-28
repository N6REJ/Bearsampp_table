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

if (SYWUtilities::isMobile()) {
	echo '<a class="hasTooltip inline_svg telegram' . $classes . '" href="tg://msg_url?url=' . urlencode($root_path . $url) . '&amp;text=' . urlencode($title) . '" rel="nofollow" data-share-network="telegram" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Telegram") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Telegram") . '">';
} else {
	echo '<a class="hasTooltip inline_svg telegram' . $classes . '" href="https://telegram.me/share/url?url=' . urlencode($root_path . $url) . '&amp;text=' . urlencode($title) . '" rel="nofollow" data-share-network="telegram" aria-label="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Telegram") . '" title="' . Text::sprintf("PLG_CONTENT_ARTICLEDETAILS_SHAREWITH", "Telegram") . '" target="_blank">';
}
	echo '<span class="svg_container">';
		$path_attributes = array('fill' => 'currentColor', 'd' => 'm 478.7,98.6 -67.6,318.8 c -5.1,22.5 -18.4,28.1 -37.3,17.5 l -103,-75.9 -49.7,47.8 c -5.5,5.5 -10.1,10.1 -20.7,10.1 L 207.8,312 398.7,139.5 c 8.3,-7.4 -1.8,-11.5 -12.9,-4.1 L 149.8,284 48.2,252.2 C 26.1,245.3 25.7,230.1 52.8,219.5 L 450.2,66.4 c 18.4,-6.9 34.5,4.1 28.5,32.2 z');
		echo SYWUtilities::getInlineSVG('telegram', ['width' => '14', 'aria-hidden' => 'true', 'focusable' => 'false', 'role' => 'img', 'viewBox' => '0 0 512 512'], $path_attributes);
	echo '</span>';
echo '</a>';
