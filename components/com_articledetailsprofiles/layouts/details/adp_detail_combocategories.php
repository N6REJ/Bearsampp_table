<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$view = $displayData['view'];

$params = $displayData['params'];

$item = $displayData['item'];
$item_params = $displayData['item_params'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_COMBOCATEGORIES') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-folder-open' : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_categories' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

echo '<span class="detail_data">';

if (($item_params->get('show_parent_category') || $params->get('force_show', 0)) && $item->parent_id != 1) { // do not show any parent info if the parent is root
    if ($item_params->get('link_parent_category') && !Factory::getApplication()->input->getBool('print')) {
        if ($view == 'article') {
            if (!empty($item->parent_slug)) {
                echo '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->parent_slug)) . '">' . $item->parent_title . '</a>';
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_COMBOCATEGORIESSEPARATOR');
            } else {
                echo $item->parent_title;
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_COMBOCATEGORIESSEPARATOR');
            }
        } else {
            // No linking if the parent category is the one the view is in

            $cat_link = Route::_(RouteHelper::getCategoryRoute($item->parent_id));
            $current_link = Uri::current();

            if (substr( $current_link, strlen( $current_link ) - strlen( $cat_link ) ) != $cat_link) { // the current links does not end with the parent category link
                echo '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->parent_id)) . '">' . $item->parent_title . '</a>';
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_COMBOCATEGORIESSEPARATOR');
            } else {
                echo $item->parent_title;
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_COMBOCATEGORIESSEPARATOR');
            }
        }
    } else {
        echo $item->parent_title;
        echo Text::_('PLG_CONTENT_ARTICLEDETAILS_COMBOCATEGORIESSEPARATOR');
    }
}

if ($item_params->get('link_category') && !Factory::getApplication()->input->getBool('print')) {
    if ($view == 'article') {
        if (!empty($item->catslug)) {
            echo '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->catslug)) . '">' . $item->category_title . '</a>';
        } else {
            echo $item->category_title;
        }
    } else {
        // No linking if the category is the one the view is in

        $cat_link = Route::_(RouteHelper::getCategoryRoute($item->catid));
        $current_link = Uri::current();

        if (substr( $current_link, strlen( $current_link ) - strlen( $cat_link ) ) != $cat_link) { // the current links does not end with the category link
            echo '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->catid)) . '">' . $item->category_title . '</a>'; // keep linking in category view because of sub-categories
        } else {
            echo $item->category_title;
        }
    }
} else {
    echo $item->category_title;
}

echo '</span>';

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
