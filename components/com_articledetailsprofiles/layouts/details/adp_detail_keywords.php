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
use SYW\Library\Utilities as SYWUtilities;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$keywords = $displayData['keywords'];
$link = $displayData['link'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_KEYWORDS') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-tag': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_keywords' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

if ($params->get('distinct_keywords', 0)) { // keywords as distinct entities
    echo '<span class="detail_multi_data">';

    $label_keyword = empty($params->get('prepend_keywords', '')) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_KEYWORD') : $params->get('prepend_keywords', '');
    $icon_keyword = empty($params->get('icon_keywords', '')) ? 'SYWicon-tag' : SYWUtilities::getIconFullname($params->get('icon_keywords', ''));

    foreach ($keywords as $i => $keyword) {
        if (!empty($keyword)) {
            echo '<span class="distinct">';

            if ($params->get('show_icon_keywords', 0) && Factory::getDocument()->getDirection() != 'rtl') {
                echo '<i class="' . $icon_keyword . '"></i>';
            }

            if (!empty($label_keyword) && Factory::getDocument()->getDirection() != 'rtl') {
                echo '<span class="detail_label">' . $label_keyword . '</span>';
            }

            if ($link == 'search' && !Factory::getApplication()->input->getBool('print')) {
                // Find the menu item for the search
                $menu  = Factory::getApplication()->getMenu();
                $items = $menu->getItems('link', 'index.php?option=com_search&view=search');
                $searchUriAddition = '';

                if (isset($items[0])) {
                    $searchUriAddition = '&Itemid=' . $items[0]->id;
                }

                echo '<a class="detail_data" href="' . Route::_(Uri::base() . 'index.php?option=com_search&searchword=' . $keyword . '&searchphrase=all' . $searchUriAddition) . '">' . $keyword . '</a>';
            } else if ($link == 'finder' && !Factory::getApplication()->input->getBool('print')) {
                // Find the menu item for the smart search
                $menu  = Factory::getApplication()->getMenu();
                $items = $menu->getItems('link', 'index.php?option=com_finder&view=search');
                $searchUriAddition = '';

                if (isset($items[0])) {
                    $searchUriAddition = '&Itemid=' . $items[0]->id;
                }

                echo '<a class="detail_data" href="' . Route::_(Uri::base() . 'index.php?option=com_finder&q=' . $keyword . $searchUriAddition) . '">' . $keyword . '</a>';
            } else {
                echo '<span class="detail_data">' . $keyword . '</span>';
            }

            if (!empty($label_keyword) && Factory::getDocument()->getDirection() == 'rtl') {
                echo '<span class="detail_label">' . $label_keyword . '</span>';
            }

            if ($params->get('show_icon_keywords', 0) && Factory::getDocument()->getDirection() == 'rtl') {
                echo '<i class="' . $icon_keyword . '"></i>';
            }

            echo '</span>';
        }

        if ($i < count($keywords) - 1) {
            if (!empty($keyword)) {
                echo '<span class="delimiter"> </span>';
            }
        }
    }

    echo '</span>';

} else { // keywords as list of items

    echo '<span class="detail_data">';

    // clean the keyword's list
    foreach ($keywords as $i => $keyword) {
        if (!empty($keyword)) {
            if ($link == 'search' && !Factory::getApplication()->input->getBool('print')) {
                // Find the menu item for the search
                $menu  = Factory::getApplication()->getMenu();
                $items = $menu->getItems('link', 'index.php?option=com_search&view=search');
                $searchUriAddition = '';

                if (isset($items[0])) {
                    $searchUriAddition = '&Itemid=' . $items[0]->id;
                }

                $keyword = '<a href="' . Route::_(Uri::base() . 'index.php?option=com_search&searchword=' . $keyword . '&searchphrase=all' . $searchUriAddition) . '">' . $keyword . '</a>';
            } else if ($link == 'finder' && !Factory::getApplication()->input->getBool('print')) {
                // Find the menu item for the smart search
                $menu  = Factory::getApplication()->getMenu();
                $items = $menu->getItems('link', 'index.php?option=com_finder&view=search');
                $searchUriAddition = '';

                if (isset($items[0])) {
                    $searchUriAddition = '&Itemid=' . $items[0]->id;
                }

                $keyword = '<a href="' . Route::_(Uri::base() . 'index.php?option=com_finder&q=' . $keyword . $searchUriAddition) . '">' . $keyword . '</a>';
            }

            echo $keyword;
        }

        if ($i < count($keywords) - 1) {
            if (!empty($keyword)) {
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_KEYWORDSSSEPARATOR');
            }
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
