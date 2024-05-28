<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;

$wam = Factory::getApplication()->getDocument()->getWebAssetManager();

// using jQuery to replace vanilla javascript
//JHtml::_('jquery.framework');
//jQuery(function($) {
//    $("#{$prefix}_{$item->id}_button").parent().on("click", function() {
//        $(this).has("iframe").length ? $(this).off("click") : $(this).append('<iframe id="{$prefix}_{$item->id}_video" width="$width" height="$height" src="//player.vimeo.com/video/$match[5]?autoplay=1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
//    });
//});

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$prefix = $displayData['prefix'];
$item = $displayData['item'];
$width = $displayData['width'];
$height = $displayData['height'];

$show_errors = $displayData['show_errors'];
$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $item->video[0], $match)) {

    $poster_src = '';
    if ($item->video_poster && $item->video_poster != 'default') {
        $poster_src = $item->video_poster;
    }

    if ($poster_src) {

        echo '<svg id="' . $prefix . '_' . $item->id . '_button" class="playbutton" viewBox="0 0 58 58"><circle class="back" cx="29" cy="29" r="25"/><path class="arrow" d=" M 24.55 36.481 L 24.524 28.996 L 24.499 21.51 C 24.494 20.202 25.454 19.698 26.515 20.45 L 26.515 20.45 L 31.602 24.057 L 36.689 27.664 C 37.776 28.435 37.771 29.756 36.678 30.519 L 36.678 30.519 L 31.636 34.038 L 26.594 37.556 C 25.526 38.302 24.555 37.791 24.55 36.481 L 24.55 36.481 Z "/></svg>';

        $wam->addInlineStyle('.latestnews-item.id-' . $item->id . ' .video { background: transparent url("' . $poster_src . '") no-repeat center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; }');

        $inlineJS = <<< JS
            document.addEventListener('DOMContentLoaded', function() {
                var pb_pa = document.getElementById('{$prefix}_{$item->id}_button').parentNode;
                pb_pa.addEventListener('click', function() {
                    if (document.getElementById('#{$prefix}_{$item->id}_video')) {
                        pb_pa.removeEventListener('click');
                        return;
                    } else {
                        var vf = document.createElement('iframe');
                        vf.setAttribute('id', '{$prefix}_{$item->id}_video');
                        vf.setAttribute('width', '$width');
                        vf.setAttribute('height', '$height');
                        vf.setAttribute('src', '//player.vimeo.com/video/$match[5]?autoplay=1&muted=1');
                        vf.setAttribute('frameborder', '0');
                        vf.setAttribute('webkitallowfullscreen', 'true');
                        vf.setAttribute('mozallowfullscreen', 'true');
                        vf.setAttribute('allowfullscreen', 'true');
                        pb_pa.appendChild(vf);
                    }
                });
            });
JS;
        $wam->addInlineScript($inlineJS);
    } else {
        echo '<iframe id="' . $prefix . '_' . $item->id . '_video" width="' . $width . '" height="' . $height . '" src="//player.vimeo.com/video/' . $match[5] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
} else {
    if ($show_errors) {
        echo '<div class="' . SYWUtilities::getBootstrapProperty('alert alert-warning', $bootstrap_version) . '">' . Text::_('COM_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTFETCHVIDEO') . '</div>';
    }
}