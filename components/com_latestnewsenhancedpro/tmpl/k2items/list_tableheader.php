<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$column_index = 0;
?>
<thead>
	<tr>
		<?php if ($this->params->get('allow_edit', 0) && (int)Factory::getUser()->get('id') > 0) : ?>
			<th class="col_edit"></th>
		<?php endif; ?>

		<?php if ($this->details_bh) : ?>
    		<?php foreach ($this->details_names_bh as $index => $detail_name) : ?>
    			<th class="col_detail_<?php echo $column_index; ?>"<?php echo $this->column_widths_bh[$index]; ?>>
    				<?php if ($this->details_icons_bh[$index]) : ?>
    					<i class="<?php echo $this->details_icons_bh[$index]; ?>" aria-hidden="true"></i>
    				<?php endif; ?>
        			<span><?php echo $detail_name; ?></span>
        			<?php echo $this->sortable_details_bh[$index]; ?>
    			</th>

    			<?php $column_index++; ?>

    		<?php endforeach; ?>
    	<?php endif; ?>

		<?php if ($this->params->get('leading_head_type', 'none') !== 'none' || $this->params->get('head_type', 'none') !== 'none') : ?>
			<th class="col_head"<?php echo $this->head_column_width; ?>>
				<span class="visually-hidden"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_LABEL_MEDIA'); ?></span>
			</th>
		<?php endif; ?>

		<?php if ($this->details) : ?>
    		<?php foreach ($this->details_names as $index => $detail_name) : ?>
    			<th class="col_detail_<?php echo $column_index; ?>"<?php echo $this->column_widths[$index]; ?>>
    				<?php if ($this->details_icons[$index]) : ?>
    					<i class="<?php echo $this->details_icons[$index]; ?>" aria-hidden="true"></i>
    				<?php endif; ?>
        			<span><?php echo $detail_name; ?></span>
        			<?php echo $this->sortable_details[$index]; ?>
    			</th>

    			<?php $column_index++; ?>

    		<?php endforeach; ?>
    	<?php endif; ?>
	</tr>
</thead>
