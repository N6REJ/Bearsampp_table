<?php
/**
 * @package         Tabs & Accordions
 * @version         2.1.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

use RegularLabs\Library\HtmlTag as RL_HtmlTag;
use RegularLabs\Plugin\System\TabsAccordions\Params;

defined('JPATH_BASE') or die;

/**
 * Layout variables
 * -----------------
 *
 * @var   object $displayData
 * @var   object $item
 * @var   object $set
 */

extract($displayData);

$params = Params::get();

$attributes = [
    'id'              => 'rlta-panel-' . $item->id,
    'aria-labelledby' => 'rlta-' . $item->id,
    'tabindex'        => '-1',
    'class'           => $item->class ?? null,
];

if ($params->hide_inactive_content && ! $item->open)
{
    $attributes['hidden'] = true;
}

$data_attributes = [
    'element' => 'panel',
    'state'   => $item->open ? 'open' : 'closed',
    'color'   => $item->color ?? false,
];

$content_data_attributes = [
    'element' => 'panel-content',
];

$attributes = trim(
    RL_HtmlTag::flattenAttributes($attributes)
    . ' ' . RL_HtmlTag::flattenAttributes($data_attributes, 'data-rlta-')
);

$content_attributes = trim(
    RL_HtmlTag::flattenAttributes($content_data_attributes, 'data-rlta-')
);
?>
<div <?php echo $attributes; ?>>
    <div <?php echo $content_attributes; ?>>
