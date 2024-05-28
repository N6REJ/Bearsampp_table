<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_RATING') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';

$icon_default = 'SYWicon-star-outline';

$show_form = isset($displayData['show_form']) ? $displayData['show_form'] : false;

if (!empty($item->rating)) {
    if (intval($item->rating) == 5) {
        $icon_default = 'SYWicon-star';
    } else {
        $icon_default = 'SYWicon-star-half';
    }
}

$icon = empty($icon) ? $icon_default : $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_rating' . $extraclasses . '">';

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

if (!empty($item->rating)) {
    if ($params->get('show_rating') == 'text') {
        echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_RATING', $item->rating).' ';
        echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_FROMUSERS', $item->rating_count);
    } else {
        // use stars
        $whole = intval($item->rating);

        $stars = '';

        for ($i = 0; $i < $whole; $i++) {
            $stars .= '<i class="SYWicon-star" aria-hidden="true"></i>';
        }

        if ($whole < 5) { 
            // Joomla rounds the rating, therefore there will never be a fraction
            // get fraction

            $fraction = $item->rating - $whole;

            if ($fraction > .4) {
                $stars .= '<i class="SYWicon-star-half" aria-hidden="true"></i>';
            } else {
                $stars .= '<i class="SYWicon-star-outline" aria-hidden="true"></i>';
            }

            for ($i = $whole + 1; $i < 5; $i++) {
                $stars .= '<i class="SYWicon-star-outline" aria-hidden="true"></i>';
            }
        }

        echo $stars;
    }
} else {
	echo Text::_('PLG_CONTENT_ARTICLEDETAILS_NORATING');
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

if ($show_form) {
    $uri = clone Uri::getInstance();
    $uri->setVar('hitcount', '0');
    
    $options = array();
    $options[] = HTMLHelper::_('select.option', 5, Text::_('PLG_CONTENT_ARTICLEDETAILS_VOTE5'));
    for ($i = 4; $i > 0; $i--) {
        $options[] = HTMLHelper::_('select.option', $i, Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_VOTE', $i));
    }
    
    echo '<form method="post" action="' . htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8') . '" class="form-inline">';
    echo '<span class="article_vote">';
    echo '<label class="visually-hidden" for="article_vote_' . $item->id . '">' . Text::_('PLG_CONTENT_ARTICLEDETAILS_PLEASEVOTE') . '</label>';
    echo HTMLHelper::_('select.genericlist', $options, 'user_rating', 'class="form-select form-select-sm w-auto"', 'value', 'text', '5', 'article_vote_' . $item->id);
    echo '&#160;<input class="btn btn-sm btn-primary align-baseline" type="submit" name="submit_vote" value="' . Text::_('PLG_CONTENT_ARTICLEDETAILS_RATE') . '" />';
    echo '<input type="hidden" name="task" value="article.vote" />';
    echo '<input type="hidden" name="hitcount" value="0" />';
    echo '<input type="hidden" name="url" value="' . htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8') . '" />';
    echo HTMLHelper::_('form.token');
    echo '</span>';
    echo '</form>';
}
