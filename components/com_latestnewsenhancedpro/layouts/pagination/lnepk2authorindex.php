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

$selected_author = Factory::getApplication()->input->getString('author', '');

$author_list = $displayData['list'];
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
		<label for="author_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
    	<label for="author_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JAUTHOR'); ?></label>
	<?php endif; ?>
	<select id="author_selector_<?php echo $suffix; ?>" name="author_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.author.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">
		<option value=""<?php if (empty($selected_author)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
		<?php foreach ($author_list as $author_item) : ?>
			<option value="<?php echo $author_item->id; ?>"<?php if ($selected_author == $author_item->id) : ?> selected="selected"<?php endif; ?>><?php echo $author_item->name; ?></option>
		<?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($author_list as $author_item) : ?>
			<li class="author author-<?php echo $author_item->id; ?><?php if ($selected_author == $author_item->id) : ?> selected<?php endif; ?>">
				<?php if ($show_picture) : ?>
					<a href="" onclick="document.adminForm.author.value='<?php echo $author_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
						<?php $image = empty($author_item->image) ? $default_picture : $author_item->image; ?>
						<?php if ($image) : ?>
							<img alt="<?php echo $author_item->name; ?>" src="<?php echo $image; ?>" />
						<?php else : ?>
							<span class="noauthorpicture">&nbsp;</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
				<a href="" onclick="document.adminForm.author.value='<?php echo $author_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
					<span><?php echo $author_item->name; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>