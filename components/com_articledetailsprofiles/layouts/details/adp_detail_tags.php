<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper as TagsRouteHelper;
use Joomla\Registry\Registry;
use SYW\Library\Utilities as SYWUtilities;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$bootstrap_version = $params->get('bootstrap_version', '');

if (empty($bootstrap_version)) {
	$config_params = ComponentHelper::getParams('com_articledetailsprofiles');
	$bootstrap_version = $config_params->get('bootstrap_version', 'joomla');
}

if ($bootstrap_version === 'joomla') {
	$bootstrap_version = 5;
} else {
	$bootstrap_version = intval($bootstrap_version);
}

$item = $displayData['item'];

$tags = $displayData['tags'];

$linked = $displayData['linked'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_TAGS') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-tags' : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_tags' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

if ($params->get('distinct_tags', 0)) {  // tags as distinct entities
    echo '<span class="detail_multi_data">';

    $label_tag = empty($params->get('prepend_tags', '')) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_TAG') : $params->get('prepend_tags', '');
    $icon_tag = empty($params->get('icon_tags', '')) ? 'SYWicon-tag2' : SYWUtilities::getIconFullname($params->get('icon_tags', ''));

    foreach ($tags as $i => $tag) {
    	if (Factory::getLanguage()->hasKey($tag->title)) {
    		$tag->title = Text::_($tag->title);
    	}

        echo '<span class="distinct distinct_tag tag_' . $tag->id . '">';

        if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() != 'rtl') {
            echo '<i class="' . $icon_tag . '"></i>';
        }

        if (!empty($label_tag) && Factory::getDocument()->getDirection() != 'rtl') {
            echo '<span class="detail_label">' . $label_tag . '</span>';
        }

        echo '<span class="detail_data">';

        $tag_class_attribute = '';

        if ($params->get('bootstrap_tags', 0)) { // in fact, get classes for tags from the console
            $tagParams = new Registry($tag->params);
            $tag_class_attribute = ' ' . $tagParams->get('tag_link_class', SYWUtilities::getBootstrapProperty('label label-info', $bootstrap_version));
        } else if (trim($params->get('tag_classes', ''))) {
            $tag_class_attribute = ' ' . trim($params->get('tag_classes'));
        }

        if ($linked && !Factory::getApplication()->input->getBool('print')) {
        	echo '<a href="' . Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)) . '" class="detail_data' . $tag_class_attribute . '">' . $tag->title . '</a>';
        } else {
            echo '<span class="detail_data' . $tag_class_attribute . '">' . $tag->title . '</span>';
        }

        echo '</span>';

        if (!empty($label_tag) && Factory::getDocument()->getDirection() == 'rtl') {
            echo '<span class="detail_label">' . $label_tag . '</span>';
        }

        if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() == 'rtl') {
            echo '<i class="' . $icon_tag . '"></i>';
        }

        echo '</span>';

        if ($i < count($tags) - 1) {
            echo '<span class="delimiter">&nbsp;</span>';
        }
    }

    echo '</span>';

} else {  // tags as list of items

    echo '<span class="detail_data">';

    foreach ($tags as $i => $tag) {
    	if (Factory::getLanguage()->hasKey($tag->title)) {
    		$tag->title = Text::_($tag->title);
    	}

        if ($linked && !Factory::getApplication()->input->getBool('print')) {
        	echo '<a href="' . Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)) . '">';
            echo $tag->title;
            echo '</a>';
        } else {
            echo $tag->title;
        }

        if ($i < count($tags) - 1) {
            echo Text::_('PLG_CONTENT_ARTICLEDETAILS_TAGSSEPARATOR');
        }
    }

    echo '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

if ($label && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

echo '</span>';
