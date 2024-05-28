<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$suffix = isset($displayData['suffix']) ? $displayData['suffix'] : '';

$selected_tag = Factory::getApplication()->input->getString('tag', '');

$tag_list = $displayData['list'];
$output_style = $displayData['style'];
$show_picture = $displayData['show_picture'];
$default_picture = $displayData['default_picture'];
$selection_label = $displayData['selection_label'];

$classes_select = '';
switch ($bootstrap_version) {
	case '3': case '4': $classes_select = ' class="form-control"'; break;
	case '5': $classes_select = ' class="form-select"'; break;
}
?>
<?php if ($output_style == 'selection') : ?>
	<?php if ($selection_label) : ?>
		<label for="tag_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
    	<label for="tag_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JTAG'); ?></label>
	<?php endif; ?>
	<select id="tag_selector_<?php echo $suffix; ?>" name="tag_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.tag.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">
		<option value=""<?php if (empty($selected_tag)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
		<?php foreach ($tag_list as $tag_item) : ?>
			<option value="<?php echo $tag_item->id; ?>"<?php if ($selected_tag == $tag_item->id) : ?> selected="selected"<?php endif; ?>><?php echo $tag_item->title; ?></option>
		<?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($tag_list as $tag_item) : ?>
			<?php
				$tag_class = '';
				if (trim($tag_item->link_class) != '') {
					$tag_class = ' class="'.$tag_item->link_class.'"';
				}
			?>
			<li class="tag tag-<?php echo $tag_item->id; ?><?php if ($selected_tag == $tag_item->id) : ?> selected<?php endif; ?>">
				<?php if ($show_picture) : ?>
					<a href="" onclick="document.adminForm.tag.value='<?php echo $tag_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
						<?php if ($default_picture) : ?>
							<img alt="<?php echo $tag_item->title; ?>" src="<?php echo $default_picture; ?>" />
						<?php else : ?>
							<span class="notagpicture">&nbsp;</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
				<a href="" onclick="document.adminForm.tag.value='<?php echo $tag_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;"<?php echo $tag_class; ?>>
					<span><?php echo $tag_item->title; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>