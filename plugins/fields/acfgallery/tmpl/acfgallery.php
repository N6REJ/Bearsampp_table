<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$gallery_data = $field->value)
{
	return;
}

require_once JPATH_SITE . '/plugins/fields/acfgallery/fields/helper.php';

if (is_string($gallery_data) && !$gallery_data = json_decode($gallery_data, true))
{
    return;
}

$gallery_items = isset($gallery_data['items']) ? $gallery_data['items'] : [];

$style = $fieldParams->get('style', 'grid');
$style = ACFGalleryHelper::getStyle($style);

$payload = [
    'items' => ACFGalleryHelper::prepareItems($gallery_items),
    'ordering' => $fieldParams->get('ordering', 'default'),
    'lightbox' => $fieldParams->get('lightbox', '0') === '1',
    'module' => $fieldParams->get('module', ''),
    'justified_item_height' => $fieldParams->get('justified_item_height'),
    'thumb_width' => $fieldParams->get('thumb_width', ''),
    'thumb_height' => $style === 'grid.svg' ? $fieldParams->get('thumb_height', '0') : null,
    'style' => $style,
    'columns' => $fieldParams->get('devices_columns.columns', []),
    'gap' => $fieldParams->get('devices_gap.gap', []),
    // tags
    'tags_position' => $fieldParams->get('tags.position', 'disabled'),
    'tags_ordering' => $fieldParams->get('tags.ordering', 'default'),
    'all_tags_item_label' => $fieldParams->get('tags.all_tags_item_label', 'All'),
    'tags_mobile' => $fieldParams->get('tags.mobile', 'show'),
    'tags_text_color' => $fieldParams->get('tags.text_color', '#555'),
    'tags_text_color_hover' => $fieldParams->get('tags.text_color_hover', '#fff'),
    'tags_bg_color_hover' => $fieldParams->get('tags.bg_color_hover', '#1E3148')
];

// Set custom layout
if ($field->params->get('acf_layout_override'))
{
	$payload['layout'] = $field->params->get('acf_layout_override');
}

$widgetName = $style === 'slideshow' ? 'Slideshow' : 'Gallery';

// Add Slideshow-related payload
if ($style === 'slideshow')
{
    $payload = array_merge($payload, [
        'lightbox' => $fieldParams->get('slideshow_lightbox', '0') === '1',
        'module' => $fieldParams->get('slideshow_module', ''),
        'ordering' => $fieldParams->get('ordering', 'default'),
        'slides_per_view' => $fieldParams->get('slides_per_view', ['desktop' => 1]),
        'space_between_slides' => $fieldParams->get('space_between_slides', ['desktop' => 10]),
        'infinite_loop' => $fieldParams->get('infinite_loop', '0') === '1',
        'keyboard_control' => $fieldParams->get('keyboard_control', '0') === '1',
        'autoplay' => $fieldParams->get('autoplay', '0') === '1',
        'autoplay_delay' => $fieldParams->get('autoplay_delay', 3000),
        'autoplay_progress' => $fieldParams->get('autoplay_progress', '0') === '1',
        'show_thumbnails' => $fieldParams->get('show_thumbnails', '0') === '1',
        'show_thumbnails_arrows' => $fieldParams->get('show_thumbnails_arrows', '0') === '1',
        'nav_controls' => $fieldParams->get('nav_controls', 'none'),
        'theme_color' => $fieldParams->get('theme_color', '#333'),
        'transition_effect' => $fieldParams->get('transition_effect', 'slide')
    ]);
}

echo \NRFramework\Widgets\Helper::render($widgetName, $payload);