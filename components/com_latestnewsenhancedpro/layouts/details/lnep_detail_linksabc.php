<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

// used in module also, therefore need to load the component's language file
$lang = Factory::getLanguage();
$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

$params = $displayData['params'];

$item = $displayData['item'];
//$item_params = isset($displayData['item_params']) ? $displayData['item_params'] : null;

$urls = $displayData['urls'];
//$all = $displayData['alllinks'];
$link = $displayData['link'];
$separated = ($link == 'links') ? $displayData['separated'] : null;

$label = isset($displayData['label']) ? $displayData['label'] : '';
$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-link': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

$globalparams = ComponentHelper::getParams('com_content');

$targeta = $globalparams->get('targeta', 0);
if (!empty($urls->targeta)) {
	$targeta = $urls->targeta;
}

$targetb = $globalparams->get('targetb', 0);
if (!empty($urls->targetb)) {
	$targetb = $urls->targetb;
}

$targetc = $globalparams->get('targetc', 0);
if (!empty($urls->targetc)) {
	$targetc = $urls->targetc;
}

// using 'echo' to prevent spaces if using direct html tags

if ($link == 'links') {
	echo '<span class="detail detail_links' . $extraclasses . '">';

	if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="detail_icon ' . $icon . '"></i>';
	}

	if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	echo '<span class="detail_multi_data">';

	if (!empty($urls->urla)) {

		echo '<span class="distinct distinct_linka">';

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() != 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		echo Helper::getATagLinks($urls->urla, $urls->urlatext, $targeta, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlatext)) {
			echo $urls->urlatext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urla);
			} else {
				echo $urls->urla;
			}
		}

		echo '</a>';

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() == 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		echo '</span>';

		if (!empty($urls->urlb) || !empty($urls->urlc)) {
			if ($separated) {
				echo '<br />';
			} else {
				echo '<span class="delimiter"> </span>';
			}
		}
	}

	if (!empty($urls->urlb)) {

		echo '<span class="distinct distinct_linkb">';

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() != 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		echo Helper::getATagLinks($urls->urlb, $urls->urlbtext, $targetb, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlbtext)) {
			echo $urls->urlbtext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urlb);
			} else {
				echo $urls->urlb;
			}
		}

		echo '</a>';

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() == 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		echo '</span>';

		if (!empty($urls->urlc)) {
			if ($separated) {
				echo '<br />';
			} else {
				echo '<span class="delimiter"> </span>';
			}
		}
	}

	if (!empty($urls->urlc)) {

		echo '<span class="distinct distinct_linkc">';

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() != 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		echo Helper::getATagLinks($urls->urlc, $urls->urlctext, $targetc, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlctext)) {
			echo $urls->urlctext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urlc);
			} else {
				echo $urls->urlc;
			}
		}

		echo '</a>';

		if (!empty($params->get('prepend_links', '')) && Factory::getDocument()->getDirection() == 'rtl') {
			echo '<span class="detail_label">' . $params->get('prepend_links', '') . '</span>';
		}

		if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
			$icon_link = empty($params->get('icon_links', '')) ? 'link' : $params->get('icon_links', '');
			echo '<i class="detail_icon ' . $icon_link . '"></i>';
		}

		echo '</span>';
	}

	echo '</span>';

	if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="detail_icon ' . $icon . '"></i>';
	}

	echo '</span>';

} else {

    echo '<span class="detail detail_link detail_' . $link . $extraclasses . '">';

	if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="detail_icon ' . $icon . '"></i>';
	}

	if (!empty($label) && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	if ($link == 'linka') {
		echo Helper::getATagLinks($urls->urla, $urls->urlatext, $targeta, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlatext)) {
			echo $urls->urlatext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urla);
			} else {
				echo $urls->urla;
			}
		}

		echo '</a>';

	} else if ($link == 'linkb') {

		echo Helper::getATagLinks($urls->urlb, $urls->urlbtext, $targetb, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlbtext)) {
			echo $urls->urlbtext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urlb);
			} else {
				echo $urls->urlb;
			}
		}

		echo '</a>';

	} else if ($link == 'linkc') {

		echo Helper::getATagLinks($urls->urlc, $urls->urlctext, $targetc, false, $params->get('popup_x', 600), $params->get('popup_y', 500), 'detail_data');

		if (!empty($urls->urlctext)) {
			echo $urls->urlctext;
		} else {
			if (!$params->get('protocol', 1)) {
				echo Helper::remove_protocol($urls->urlc);
			} else {
				echo $urls->urlc;
			}
		}

		echo '</a>';
	}

	if (!empty($label) && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<span class="detail_label">' . $label . '</span>';
	}

	if ($show_icon && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="detail_icon  ' . $icon . '"></i>';
	}

	echo '</span>';
}

