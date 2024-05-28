<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\Helper;

$lang = Factory::getLanguage();
$lang->load('plg_content_articledetails', JPATH_ADMINISTRATOR);

$params = $displayData['params'];

$item = $displayData['item'];

$urls = $displayData['urls'];

$separated = $displayData['separated'];

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

$label = isset($displayData['label']) ? $displayData['label'] : '';
$label = empty($label) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_LINKS') : $label;

$postinfo = isset($displayData['postinfo']) ? $displayData['postinfo'] : '';

$show_icon = $displayData['show_icon'];
$icon = isset($displayData['icon']) ? $displayData['icon'] : '';
$icon = empty($icon) ? 'SYWicon-link': $icon;
$extraclasses = isset($displayData['extraclasses']) ? ' ' . trim($displayData['extraclasses']) : '';

// using 'echo' to prevent spaces if using direct html tags

echo '<span class="detail detail_links' . $extraclasses . '">';

if ($show_icon && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<i class="' . $icon . '"></i>';
}

if ($label && Factory::getDocument()->getDirection() != 'rtl') {
	echo '<span class="detail_label">' . $label . '</span>';
}

if ($postinfo && Factory::getDocument()->getDirection() == 'rtl') {
	echo '<span class="detail_post">' . $postinfo . '</span>';
}

echo '<span class="detail_multi_data">';

$label_link = empty($params->get('prepend_links', '')) ? Text::_('PLG_CONTENT_ARTICLEDETAILS_PREPEND_LINK') : $params->get('prepend_links', '');
$icon_link = empty($params->get('icon_links', '')) ? 'SYWicon-link' : SYWUtilities::getIconFullname($params->get('icon_links', ''));

if (!empty($urls->urla)) {
	echo '<span class="distinct distinct_linka">';

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() != 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo Helper::getATagLinks($urls->urla, $urls->urlatext, $targeta, false, '600', '500', 'detail_data');
	} else {
	    echo '<span class="detail_data">';
	}

	if (!empty($urls->urlatext)) {
		echo $urls->urlatext;
	} else {
		if (!$params->get('protocol', 1)) {
		    echo Helper::remove_protocol($urls->urla);
		} else {
			echo $urls->urla;
		}
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo '</a>';
	} else {
	    echo '</span>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() == 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	echo '</span>';

	if (!empty($urls->urlb) || !empty($urls->urlc)) {
		if ($separated) {
			echo '<br />';
		} else {
		    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_LINKSSEPARATOR');
		}
	}
}

if (!empty($urls->urlb)) {
	echo '<span class="distinct distinct_linkb">';

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() != 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo Helper::getATagLinks($urls->urlb, $urls->urlbtext, $targetb, false, '600', '500', 'detail_data');
	} else {
	    echo '<span class="detail_data">';
	}

	if (!empty($urls->urlbtext)) {
		echo $urls->urlbtext;
	} else {
		if (!$params->get('protocol', 1)) {
		    echo Helper::remove_protocol($urls->urlb);
		} else {
			echo $urls->urlb;
		}
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo '</a>';
	} else {
	    echo '</span>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() == 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	echo '</span>';

	if (!empty($urls->urlc)) {
		if ($separated) {
			echo '<br />';
		} else {
		    echo Text::_('PLG_CONTENT_ARTICLEDETAILS_LINKSSEPARATOR');
		}
	}
}

if (!empty($urls->urlc)) {
	echo '<span class="distinct distinct_linkc">';

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() != 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() != 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo Helper::getATagLinks($urls->urlc, $urls->urlctext, $targetc, false, '600', '500', 'detail_data');
	} else {
	    echo '<span class="detail_data">';
	}

	if (!empty($urls->urlctext)) {
		echo $urls->urlctext;
	} else {
		if (!$params->get('protocol', 1)) {
		    echo Helper::remove_protocol($urls->urlc);
		} else {
			echo $urls->urlc;
		}
	}

	if (!Factory::getApplication()->input->getBool('print')) {
	    echo '</a>';
	} else {
	    echo '</span>';
	}

	if (!empty($label_link) && Factory::getDocument()->getDirection() == 'rtl') {
	    echo '<span class="detail_label">' . $label_link . '</span>';
	}

	if ($params->get('show_icon_links', 0) && Factory::getDocument()->getDirection() == 'rtl') {
		echo '<i class="' . $icon_link . '"></i>';
	}

	echo '</span>';
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
