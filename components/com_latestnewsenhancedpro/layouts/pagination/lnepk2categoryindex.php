<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use SYW\Library\Utilities as SYWUtilities;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$suffix = isset($displayData['suffix']) ? $displayData['suffix'] : '';

$selected_category = isset($displayData['selected_category']) ? $displayData['selected_category'] : Factory::getApplication()->input->getString('category', '');

$category_list = $displayData['list'];
$output_style = $displayData['style'];
$show_picture = $displayData['show_picture'];
$default_picture = $displayData['default_picture'];
$selection_label = $displayData['selection_label'];
$categories_unselectable = $displayData['unselectable'];
$show_category_hierarchy = $displayData['show_hierarchy'];

if ($output_style == 'selection') {
	
	if ($show_category_hierarchy) {
	    $children = array();
	
	    if ($category_list) {
	        foreach ($category_list as $category) {
	            $parent = $category->parent_id;
	            $list = @$children[$parent] ? $children[$parent] : array();
	            array_push($list, $category);
	            $children[$parent] = $list;
	        }
	    }
	
	    $root = 0;
	    if (!empty($children) && !isset($children[$root])) {
	        $root = array_key_first($children);
	    }
	
	    $category_list = HTMLHelper::_('menu.treerecurse', $root, '', array(), $children, 9999, 0, 0);
	}
    
    $classes_select = '';
    switch ($bootstrap_version) {
    	case '3': case '4': $classes_select = ' class="form-control"'; break;
    	case '5': $classes_select = ' class="form-select"'; break;
    }
}
?>
<?php if ($output_style == 'selection') : ?>
	<?php if ($selection_label) : ?>
		<label for="category_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
    	<label for="category_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JCATEGORY'); ?></label>
	<?php endif; ?>
	<select id="category_selector_<?php echo $suffix; ?>" name="category_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.category.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">
		<option value=""<?php if (empty($selected_category)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
		<?php foreach ($category_list as $category_item) : ?>
			<?php
                 if ($show_category_hierarchy) {
                     $category_item->title = str_ireplace('-', '', $category_item->treename);
                     $category_item->title = str_ireplace('&#160;&#160;', '- ', $category_item->title);
                 }

                $disabled = '';
                if (!empty($categories_unselectable) && in_array($category_item->id, $categories_unselectable)) {
                    $disabled = ' disabled="disabled"';
                }
            ?>
			<option value="<?php echo $category_item->id; ?>"<?php echo $disabled; ?><?php if ($selected_category == $category_item->id) : ?> selected="selected"<?php endif; ?>><?php echo $category_item->title; ?></option>
		<?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($category_list as $category_item) : ?>
			<li class="category catid-<?php echo $category_item->id; ?><?php if ($selected_category == $category_item->id) : ?> selected<?php endif; ?>">
				<?php if ($show_picture) : ?>
					<a href="" onclick="document.adminForm.category.value='<?php echo $category_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
						<?php $image = empty($category_item->image) ? $default_picture : 'media/k2/categories/'.$category_item->image; ?>
						<?php if ($image) : ?>
							<img alt="<?php echo $category_item->title; ?>" src="<?php echo $image; ?>" />
						<?php else : ?>
							<span class="nocategorypicture">&nbsp;</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
				<a href="" onclick="document.adminForm.category.value='<?php echo $category_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
					<span><?php echo $category_item->title; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>