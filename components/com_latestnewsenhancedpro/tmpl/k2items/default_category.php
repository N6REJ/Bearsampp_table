<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

$this->extracatcontainerclass .= ($this->params->get('cat_align', 'default') !== 'default' ? ' '.$this->params->get('cat_align', 'default') : '');

$additional_attributes = '';
if ($this->params->get('cat_tooltip', 1) && $this->params->get('link_cat_to', 'none') !== 'none') {
    if ($this->extracatclass) {
        $this->extracatclass = ' '.$this->extracatclass;
    }
    $additional_attributes = ' title="'.$this->cat_label_item.'" class="hasTooltip'.$this->extracatclass.'"';
} else {
    if ($this->extracatclass) {
        $additional_attributes = ' class="'.$this->extracatclass.'"';
    }
}
?>
<div class="catlink<?php echo $this->extracatcontainerclass; ?>">
	<?php if ($this->item->catlink) : ?>									
		<a href="<?php echo $this->item->catlink; ?>"<?php echo $additional_attributes; ?>>
			<span><?php echo $this->cat_label_item; ?></span>
		</a>
	<?php elseif ($this->params->get('link_cat_to', 'none') == 'self') : ?>
		<a href="" onclick="document.adminForm.category.value='<?php echo $this->item->catid; ?>'; document.adminForm.submit(); return false;"<?php echo $additional_attributes; ?>>
			<span><?php echo $this->cat_label_item; ?></span>
		</a>						
	<?php else : ?>
		<span<?php echo $additional_attributes; ?>><?php echo $this->cat_label_item; ?></span>
	<?php endif; ?>
</div>