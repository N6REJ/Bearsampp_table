<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$date = $displayData['date'];
$date_type = $displayData['date_type'];

$item = $displayData['item'];

$label = isset($displayData['label']) ? $displayData['label'] : '';

$default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_PUBLISHED');

if ($date_type == 'created') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_CREATED');
} else if ($date_type == 'modified') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_MODIFIED');
} else if ($date_type == 'finished') {
    $default_label = Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_FINISHED');
}

$label = empty($label) ? $default_label : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-calendar': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_date' . $extraclasses . '">';

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

$nbr_seconds = -1;
$nbr_minutes = -1;
$nbr_hours = -1;
$nbr_days = -1;

$date_format = Text::_('PLG_CONTENT_ARTICLEDETAILS_FORMAT_DATE');

if (empty($date_format)) {
    $date_format = $params->get('d_format', 'd F Y');
}

$show_date = $params->get('show_d', 'date');

if ($show_date == 'ago' || $show_date == 'agomhd' || $show_date == 'agohm') {
    if (!empty($date)) {
        $details = self::date_to_counter($date, false);

        $nbr_seconds  = intval($details['secs']);
        $nbr_minutes  = intval($details['mins']);
        $nbr_hours = intval($details['hours']);
        $nbr_days = intval($details['days']);
    }
}

if ($show_date == 'date') {
	echo HTMLHelper::_('date', $date, $date_format);
} else {
    if ($date_type == 'finished') {
        if ($show_date == 'ago') {
            if ($nbr_days == 0) {
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_TODAY');
            } else if ($nbr_days == 1) {
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_TOMORROW');
            } else {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INDAYSONLY', $nbr_days);
            }
        } else if ($show_date == 'agomhd') {
            if ($nbr_days > 0) {
                if ($nbr_days == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_INADAY');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INDAYSONLY', $nbr_days);
                }
            } else if ($nbr_hours > 0) {
                if ($nbr_hours == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_INANHOUR');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INHOURS', $nbr_hours);
                }
            } else {
                if ($nbr_minutes == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_INAMINUTE');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INMINUTES', $nbr_minutes);
                }
            }
        } else {
            if ($nbr_days > 0) {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INDAYSHOURSMINUTES', $nbr_days, $nbr_hours, $nbr_minutes);
            } else if ($nbr_hours > 0) {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INHOURSMINUTES', $nbr_hours, $nbr_minutes);
            } else {
                if ($nbr_minutes == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_INAMINUTE');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_INMINUTES', $nbr_minutes);
                }
            }
        }
    } else {
        if ($show_date == 'ago') {
            if ($nbr_days == 0) {
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_TODAY');
            } else if ($nbr_days == 1) {
                echo Text::_('PLG_CONTENT_ARTICLEDETAILS_YESTERDAY');
            } else {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_DAYSAGO', $nbr_days);
            }
        } else if ($show_date == 'agomhd') {
            if ($nbr_days > 0) {
                if ($nbr_days == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_DAYAGO');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_DAYSAGO', $nbr_days);
                }
            } else if ($nbr_hours > 0) {
                if ($nbr_hours == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_HOURAGO');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_HOURSAGO', $nbr_hours);
                }
            } else {
                if ($nbr_minutes == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_MINUTEAGO');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_MINUTESAGO', $nbr_minutes);
                }
            }
        } else {
            if ($nbr_days > 0) {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_DAYSHOURSMINUTESAGO', $nbr_days, $nbr_hours, $nbr_minutes);
            } else if ($nbr_hours > 0) {
                echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_HOURSMINUTESAGO', $nbr_hours, $nbr_minutes);
            } else {
                if ($nbr_minutes == 1) {
                    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_MINUTEAGO');
                } else {
                    echo Text::sprintf('PLG_CONTENT_ARTICLEDETAILS_MINUTESAGO', $nbr_minutes);
                }
            }
        }
    }
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
