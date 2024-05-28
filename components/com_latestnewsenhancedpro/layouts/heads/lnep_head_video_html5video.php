<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Utilities as SYWUtilities;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$prefix = $displayData['prefix'];
$item = $displayData['item'];
$width = $displayData['width'];
$height = $displayData['height'];

$show_errors = $displayData['show_errors'];
$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$poster_attribute = '';
$preload = 'auto'; // 'auto' if no poster, 'none' if poster, better for IE9/10 support (which shows only when no video is available)
if ($item->video_poster && $item->video_poster != 'default') {
    $poster_attribute = ' poster="' . $item->video_poster . '"';
    $preload = 'none';
}

$attributes = array_filter(array(
    //'loop',
    //'autoplay',
    'muted',
    'controls'
));

$allowed_extensions = array('mp4', 'webm','ogg');
$correct_videos = array();

foreach ($item->video as $video) {
    if (!$pathinfo = pathinfo($video)) {
        continue;
    }

    if ($allowed_extensions && !in_array($pathinfo['extension'], $allowed_extensions)) {
        continue;
    }

    // Add root path to local source
    if (strpos($video, 'http') === false) {
        $video = Uri::root() . ltrim($video, '/');
    }

    $correct_videos[] = array('ext' => $pathinfo['extension'], 'file' => $video);
}

if (count($correct_videos) > 0) {
    echo '<video id="' . $prefix . '_' . $item->id . '_video" width="' . $width . '" height="' . $height . '" preload="' . $preload . '"' . $poster_attribute . ' controlsList="nodownload" ' . implode(' ', $attributes) . ' style="max-width:100%; height: auto;">';

    foreach ($correct_videos as $video) {
        echo '<source src="' . $video['file'] . '" type="video/' . $video['ext'] . '">';
    }

    echo Text::_('COM_LATESTNEWSENHANCEDPRO_WARNING_UNSUPPORTEDVIDEOTAG');

    echo '</video>';
} else if ($show_errors) {
    echo '<div class="' . SYWUtilities::getBootstrapProperty('alert alert-warning', $bootstrap_version) . '">' . Text::_('COM_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTFETCHVIDEO') . '</div>';
}
