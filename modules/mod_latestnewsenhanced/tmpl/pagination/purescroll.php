<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die;

$extra_container_class = '';
$pagination_class_attribute = '';
$item_class_attribute = '';
$link_class = '';
if ($pagination_style && $bootstrap_version > 0) { // Bootstrap is selected
    if ($bootstrap_version == 2) {
        $extra_container_class = ' pagination';
        if ($pagination_size) {
            $extra_container_class .= ' '.$pagination_size;
        }
    } else if ($bootstrap_version == 3) {
        $pagination_class_attribute = ' class="pagination';
        if ($pagination_size) {
            $pagination_class_attribute .= ' '.$pagination_size;
        }
        $pagination_class_attribute .= '"';
    } else if ($bootstrap_version >= 4) {
        $pagination_class_attribute = ' class="pagination';
        if ($pagination_size) {
            $pagination_class_attribute .= ' '.$pagination_size;
        }
        if ($pagination_align) {
            $pagination_class_attribute .= ' '.$pagination_align;
        }
        $pagination_class_attribute .= '"';
        $item_class_attribute = ' class="page-item"';
        $link_class = ' page-link';
    }
}

$label_prev_output = '<span class="SYWicon-arrow-left2" aria-hidden="true"></span>';
if ($pagination_position == 'up') {
    $label_prev_output = '<span class="SYWicon-arrow-up2" aria-hidden="true"></span>';
}
if ($label_prev) {
    $label_prev_output = '<span>'.$label_prev.'</span>';
}

$label_next_output = '<span class="SYWicon-arrow-right2" aria-hidden="true"></span>';
if ($pagination_position == 'down') {
    $label_next_output = '<span class="SYWicon-arrow-down2" aria-hidden="true"></span>';
}
if ($label_next) {
    $label_next_output = '<span>'.$label_next.'</span>';
}

?>
<div class="items_pagination<?php echo $extra_container_class; ?><?php echo empty($pagination_position) ? '' : ' '.$pagination_position; ?>">
	<?php if ($pagination_position == '' || $pagination_position == 'top' || $pagination_position == 'bottom') : ?>
		<?php if ($pagination === 'p' || $pagination === 's') : ?>
			<div class="pagenumbers"><ul id="pager_<?php echo $class_suffix; ?>"<?php echo $pagination_class_attribute; ?>></ul></div>
		<?php else : ?>
			<ul<?php echo $pagination_class_attribute; ?>>
				<li<?php echo $item_class_attribute; ?>><a id="prev_<?php echo $class_suffix; ?>" class="previous<?php echo $link_class; ?>" href="#" aria-label="<?php echo $prev_aria_label; ?>"><?php echo $label_prev_output; ?></a></li><!--
				 --><li<?php echo $item_class_attribute; ?>><a id="next_<?php echo $class_suffix; ?>" class="next<?php echo $link_class; ?>" href="#" aria-label="<?php echo $next_aria_label; ?>"><?php echo $label_next_output; ?></a></li>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ($pagination !== 'p' && $pagination !== 's') : ?>
		<?php if ($pagination_position == 'left' || $pagination_position == 'up') : ?>
			<ul<?php echo $pagination_class_attribute; ?>>
				<li<?php echo $item_class_attribute; ?>><a id="prev_<?php echo $class_suffix; ?>" class="previous<?php echo $link_class; ?>" href="#" aria-label="<?php echo $prev_aria_label; ?>"><?php echo $label_prev_output; ?></a></li>
			</ul>
		<?php endif; ?>
		<?php if ($pagination_position == 'right' || $pagination_position == 'down') : ?>
			<ul<?php echo $pagination_class_attribute; ?>>
				<li<?php echo $item_class_attribute; ?>><a id="next_<?php echo $class_suffix; ?>" class="next<?php echo $link_class; ?>" href="#" aria-label="<?php echo $next_aria_label; ?>"><?php echo $label_next_output; ?></a></li>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
</div>