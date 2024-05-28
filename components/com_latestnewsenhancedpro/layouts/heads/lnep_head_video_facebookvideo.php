<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
//use SYW\Library\Utilities as SYWUtilities;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$prefix = $displayData['prefix'];
$item = $displayData['item'];
$width = $displayData['width'];
$height = $displayData['height'];

$show_errors = $displayData['show_errors'];
$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$allowfullscreen = 'true';
$autoplay = 'false';
$showtext = 'false';
$showcaptions = 'true';
$languageTag = str_replace('-', '_', Factory::getLanguage()->getTag());

echo '<div id="fb-root"></div>
<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/' . $languageTag . '/sdk.js#xfbml=1&version=v2.6";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, "script", "facebook-jssdk"));
</script>

<!-- Embedded video player code -->
<div id="' . $prefix . '_' . $item->id . '_video" class="fb-video"
	data-href="' . $item->video[0] . '"
	data-width="' . $width . '"
	data-show-text="' . $showtext . '"
	data-show-captions="' . $showcaptions . '"
	data-allowfullscreen="' . $allowfullscreen . '"
	data-autoplay="' . $autoplay . '">
</div>';

//     if ($show_errors) {
//         echo '<div class="' . SYWUtilities::getBootstrapProperty('alert alert-warning', $bootstrap_version) . '">' . JText::_('COM_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTFETCHVIDEO') . '</div>';
//     }
