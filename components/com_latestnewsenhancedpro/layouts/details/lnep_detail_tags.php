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
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Utilities as SYWUtilities;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$bootstrap_version = $params->get('bootstrap_version', '');
if (empty($bootstrap_version)) {
	$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
	$bootstrap_version = $config_params->get('bootstrap_version', 'joomla');
}
if ($bootstrap_version === 'joomla') {
	$bootstrap_version = 5; //version_compare(JVERSION, '4.0.0', 'lt') ? 2 : 5;
} else {
	$bootstrap_version = intval($bootstrap_version);
}

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$linked = $displayData['linked'];
$selected = $displayData['selected'];
$datasource = $displayData['datasource'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-tags' : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_tags' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($datasource == 'k2') {

	if ($linked) {
		require_once (JPATH_SITE.'/components/com_k2/helpers/route.php');
	}

	if ($params->get('distinct_tags', 0)) {  // tags as distinct entities

		echo '<span class="detail_multi_data">';

		foreach ($item->tags as $i => $tag) {

			if (Factory::getLanguage()->hasKey($tag->name)) {
				$tag->name = Text::_($tag->name);
			}

			echo '<span class="distinct distinct_tag_'.$tag->id.'">';

			if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() != 'rtl') {
				$icon_tag = empty($params->get('icon_tags', '')) ? 'tag2' : $params->get('icon_tags', '');
				echo '<i class="detail_icon ' . $icon_tag . '"></i>';
			}

			if (!empty($params->get('prepend_tags', '')) && Factory::getDocument()->getDirection() != 'rtl') {
				echo '<span class="detail_label">' . $params->get('prepend_tags', '') . '</span>';
			}

			$tag_class_attribute = '';
			if (trim($params->get('tag_classes', ''))) {
				$tag_class_attribute = ' '.trim($params->get('tag_classes'));
			}

			if ($selected) {
				if ($linked) {
					if (Helper::isTagSelected($tag->id, $params->get('k2tags', array()))) {
						echo '<a href="'.Route::_(K2HelperRoute::getTagRoute($tag->name)).'" class="detail_data'.$tag_class_attribute.'">'.$tag->name.'</a>';
					}
				} else {
					if (Helper::isTagSelected($tag->id, $params->get('k2tags', array()))) {
						echo '<span class="detail_data'.$tag_class_attribute.'">'.$tag->name.'</span>';
					}
				}
			} else {
				if ($linked) {
					echo '<a href="'.Route::_(K2HelperRoute::getTagRoute($tag->name)).'" class="detail_data'.$tag_class_attribute.'">'.$tag->name.'</a>';
				} else {
					echo '<span class="detail_data'.$tag_class_attribute.'">'.$tag->name.'</span>';
				}
			}

			if (!empty($params->get('prepend_tags', '')) && Factory::getDocument()->getDirection() == 'rtl') {
				echo '<span class="detail_label">' . $params->get('prepend_tags', '') . '</span>';
			}

			if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() == 'rtl') {
				$icon_tag = empty($params->get('icon_tags', '')) ? 'tag2' : $params->get('icon_tags', '');
				echo '<i class="detail_icon ' . $icon_tag . '"></i>';
			}

			echo '</span>';

			if ($i < count($item->tags) - 1) {
				echo '<span class="delimiter"> </span>';
			}
		}

		echo '</span>';

	} else { // tags as list of items

		echo '<span class="detail_data">';

		foreach ($item->tags as $i => $tag) {

			if (Factory::getLanguage()->hasKey($tag->name)) {
				$tag->name = Text::_($tag->name);
			}

			if ($selected) {
				if ($linked) {
					if (Helper::isTagSelected($tag->id, $params->get('k2tags', array()))) {
						echo '<a href="'.Route::_(K2HelperRoute::getTagRoute($tag->name)).'">'.$tag->name.'</a>';
					}
				} else {
					if (Helper::isTagSelected($tag->id, $params->get('k2tags', array()))) {
						echo $tag->name;
					}
				}
			} else {
				if ($linked) {
					echo '<a href="'.Route::_(K2HelperRoute::getTagRoute($tag->name)).'">'.$tag->name.'</a>';
				} else {
					echo $tag->name;
				}
			}

			if ($i < count($item->tags) - 1) {
				echo Text::_('COM_LATESTNEWSENHANCEDPRO_TAGSSEPARATOR');
			}
		}

		echo '</span>';
	}

} else if ($datasource == 'articles') {

	if ($params->get('distinct_tags', 0)) {  // tags as distinct entities

		echo '<span class="detail_multi_data">';

		foreach ($item->tags as $i => $tag) {

			if (Factory::getLanguage()->hasKey($tag->title)) {
				$tag->title = Text::_($tag->title);
			}

			echo '<span class="distinct distinct_tag_'.$tag->tag_id.'">';

			if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() != 'rtl') {
				$icon_tag = empty($params->get('icon_tags', '')) ? 'tag2' : $params->get('icon_tags', '');
				echo '<i class="detail_icon ' . $icon_tag . '"></i>';
			}

			if (!empty($params->get('prepend_tags', '')) && Factory::getDocument()->getDirection() != 'rtl') {
				echo '<span class="detail_label">' . $params->get('prepend_tags', '') . '</span>';
			}

			$tag_class_attribute = '';
			if ($params->get('bootstrap_tags', 0)) { // in fact, using classes set per tag in the console
				$tagParams = new Registry($tag->params);
				$tag_class_attribute = ' '.$tagParams->get('tag_link_class', SYWUtilities::getBootstrapProperty('label', $bootstrap_version) . ' ' . SYWUtilities::getBootstrapProperty('label-info', $bootstrap_version));
			} else if (trim($params->get('tag_classes', ''))) {
				$tag_class_attribute = ' '.trim($params->get('tag_classes'));
			}

			if ($selected) {
				if ($linked) {
					if (Helper::isTagSelected($tag->tag_id, $params->get('tags', array()), $params->get('include_tag_children', 0))) {
						echo '<a href="'.Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)).'" class="detail_data'.$tag_class_attribute.'">'.$tag->title.'</a>';
					}
				} else {
					if (Helper::isTagSelected($tag->tag_id, $params->get('tags', array()), $params->get('include_tag_children', 0))) {
						echo '<span class="detail_data'.$tag_class_attribute.'">'.$tag->title.'</span>';
					}
				}
			} else {
				if ($linked) {
					echo '<a href="'.Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)).'" class="detail_data'.$tag_class_attribute.'">'.$tag->title.'</a>';
				} else {
					echo '<span class="detail_data'.$tag_class_attribute.'">'.$tag->title.'</span>';
				}
			}

			if (!empty($params->get('prepend_tags', '')) && Factory::getDocument()->getDirection() == 'rtl') {
				echo '<span class="detail_label">' . $params->get('prepend_tags', '') . '</span>';
			}

			if ($params->get('show_icon_tags', 0) && Factory::getDocument()->getDirection() == 'rtl') {
				$icon_tag = empty($params->get('icon_tags', '')) ? 'tag2' : $params->get('icon_tags', '');
				echo '<i class="detail_icon ' . $icon_tag . '"></i>';
			}

			echo '</span>';

			if ($i < count($item->tags) - 1) {
				echo '<span class="delimiter"> </span>';
			}
		}

		echo '</span>';

	} else {  // tags as list of items

		echo '<span class="detail_data">';

		foreach ($item->tags as $i => $tag) {

			if (Factory::getLanguage()->hasKey($tag->title)) {
				$tag->title = Text::_($tag->title);
			}

			if ($selected) {
				if ($linked) {
					if (Helper::isTagSelected($tag->tag_id, $params->get('tags', array()), $params->get('include_tag_children', 0))) {
						echo '<a href="'.Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)).'">'.$tag->title.'</a>';
					}
				} else {
					if (Helper::isTagSelected($tag->tag_id, $params->get('tags', array()), $params->get('include_tag_children', 0))) {
						echo $tag->title;
					}
				}
			} else {
				if ($linked) {
					echo '<a href="'.Route::_(TagsRouteHelper::getTagRoute($tag->id . ':' . $tag->alias)).'">'.$tag->title.'</a>';
				} else {
					echo $tag->title;
				}
			}

			if ($i < count($item->tags) - 1) {
				echo Text::_('COM_LATESTNEWSENHANCEDPRO_TAGSSEPARATOR');
			}
		}

		echo '</span>';
	}
}

if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<i class="detail_icon ' . $icon . '"></i>';
}

echo '</span>';
