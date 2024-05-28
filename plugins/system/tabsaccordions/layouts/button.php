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

$title_tag       = $item->{'title-tag'} ?? $set->data->{'title-tag'} ?? $params->title_tag;
$attributes      = [
    'id'            => 'rlta-' . $item->id,
    'role'          => 'button',
    'aria-controls' => 'rlta-panel-' . $item->id,
    'aria-expanded' => $item->open ? 'true' : 'false',
    'tabindex'      => '0',
    'class'         => $item->class ?? null,
];
$data_attributes = [
    'alias'   => $item->id,
    'element' => 'button',
    'state'   => $item->open ? 'open' : 'closed',
    ...(array) $item->data,
];

$heading_attributes      = [
];
$heading_data_attributes = [
    'element' => 'heading',
];

$attributes = trim(
    RL_HtmlTag::flattenAttributes($attributes)
    . ' '
    . RL_HtmlTag::flattenAttributes($data_attributes, 'data-rlta-')
);

$heading_attributes = trim(
    RL_HtmlTag::flattenAttributes($heading_attributes)
    . ' '
    . RL_HtmlTag::flattenAttributes($heading_data_attributes, 'data-rlta-')
);

?>
<div <?php echo $attributes; ?>>
    <<?php echo $title_tag; ?> <?php echo $heading_attributes; ?>>
    <?php echo $item->title ?>
</<?php echo $title_tag; ?>>
</div>
