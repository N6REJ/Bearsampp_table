<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$link_target = $displayData['target']; // same, new, popup, modal, inline
$link_url = $displayData['url'];
$link_text = isset($displayData['text']) ? $displayData['text'] : $link_url; // TODO remove http
$link_is_internal = isset($displayData['internal']) ? $displayData['internal'] : true; // TODO bool value?

$attribute_rel = '';

$rel = [];
if (isset($displayData['nofollow'])) {
    $rel[] = 'nofollow';
}
if (isset($displayData['noreferrer'])) {
    $rel[] = 'noreferrer';
}
if (isset($displayData['noopener'])) {
    $rel[] = 'noopener';
}

if (count($rel) > 0) {
    $attribute_rel = ' rel="' . implode(' ', $rel) . '"';
}

$tooltip = isset($displayData['tooltip']) ? $displayData['tooltip'] : false; // TODO bool value?
$popup_width = isset($displayData['popup_width']) ? $displayData['popup_width'] : '600';
$popup_height = isset($displayData['popup_height']) ? $displayData['popup_height'] : '500';
$css_classes = isset($displayData['css_classes']) ? explode(' ', $displayData['css_classes']) : [];
$anchors = isset($displayData['anchors']) ? $displayData['anchors'] : '';

$attribute_title = $tooltip ? ' title="' . htmlspecialchars($link_text, ENT_COMPAT, 'UTF-8') . '"' : '';
$attribute_aria_label = isset($displayData['aria_label']) ? ' aria-label="' . $displayData['aria_label'] . '"' : '';

$attribute_class = '';

if ($link_target === 'modal') {
    $css_classes[] = 'lnepmodal';
} else if ($link_target === 'inline') {
    $css_classes[] = 'inline-link';
}

if ($tooltip) {
    $css_classes[] = 'hasTooltip';
}

if (count($css_classes) > 0) {
    $attribute_class = ' class="' . implode(' ', $css_classes) . '"';
}
?>
<?php if ($link_target === 'new') : ?>
	<a href="<?php echo $link_url . $anchors ?>" target="_blank"<?php echo $attribute_class . $attribute_title . $attribute_aria_label . $attribute_rel ?>>
<?php elseif ($link_target === 'popup') : ?>
	<a href="<?php echo $link_url . $anchors ?>"<?php echo $attribute_class . $attribute_title . $attribute_aria_label . $attribute_rel ?> onclick="window.open(this.href, 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=<?php echo $popup_width ?>,height=<?php echo  $popup_height ?>'); return false;">
<?php elseif ($link_target === 'modal') : ?>
	<?php $extra_url = $link_is_internal ? (strpos($link_url, '?') !== false ? '&' : '?') . 'tmpl=component&print=1' : '' ?>
	<?php $attribute_rel = $link_is_internal ? '' : $attribute_rel ?>
	<?php $bootstrap_attributes = '' ?>
	<?php if ($bootstrap_version > 0) : ?>
		<?php $bootstrap_attributes = ' data-' . ($bootstrap_version >= 5 ? 'bs-' : '') . 'toggle="modal" data-' . ($bootstrap_version >= 5 ? 'bs-' : '') . 'target="#lnepmodal"' ?>
	<?php endif; ?>
	<a href="<?php echo $link_url . $extra_url . $anchors ?>"<?php echo $attribute_class . $attribute_title . $attribute_aria_label . $attribute_rel . $bootstrap_attributes ?> data-modaltitle="<?php echo htmlspecialchars($link_text, ENT_COMPAT, 'UTF-8') ?>" onclick="return false;">
<?php elseif ($link_target === 'inline') : ?>
	<a href=""<?php echo $attribute_class . $attribute_title . $attribute_aria_label ?> onclick="return false;">
<?php else : ?>
	<a href="<?php echo $link_url . $anchors ?>"<?php echo $attribute_class . $attribute_title . $attribute_aria_label . $attribute_rel ?>>
<?php endif; ?>
	<?php echo $displayData['content']; ?>
</a>
