<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Libraries as SYWLibraries;
use SYW\Library\Stylesheets as SYWStylesheets;
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
$extra_classes = isset($displayData['classes']) ? trim($displayData['classes']) : '';

if ($output_style === 'list' && $show_category_hierarchy) {
    
    SYWStylesheets::loadPureTreeMenu();
    
    $ex_classes = explode(' ', $extra_classes);
    if (in_array('pre', $ex_classes) || in_array('post', $ex_classes)) {
        
        SYWLibraries::loadPureTreeMenu();
        $preset = SYWUtilities::loadPureTreePreset($ex_classes);
        
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = Factory::getDocument()->getWebAssetManager();
        
        $js = <<< JS
    		window.addEventListener('load', function() {
    			let categorytree = new pureTreeMenu({
    				containerSelector: '.categorytree.rawmenu',
                    iconPrefixClass: '{$preset['prefix']}',
                    iconRetractedClass: '{$preset['retracted']}',
                    iconExpandedClass: '{$preset['expanded']}'
    			});
    		}, false);
JS;
        $wa->addInlineScript($js);
    }
    
    $childrenTree = array();
    
    if ($category_list) {
        $lastitem = 0;
        foreach ($category_list as $i => $category) {
            $parent = $category->parent_id;
            $list = @$childrenTree[$parent] ? $childrenTree[$parent] : array();
            if (!isset($childrenTree[$parent])) {
                $childrenTree[$parent] = [];
            }
            
            $childrenTree[$parent][] = $category->id;
            
            $category->deeper = false;
            $category->shallower = false;
            $category->level_diff = 0;
            
            if (isset($category_list[$lastitem])) {
                $category_list[$lastitem]->deeper     = ($category->level > $category_list[$lastitem]->level);
                $category_list[$lastitem]->shallower  = ($category->level < $category_list[$lastitem]->level);
                $category_list[$lastitem]->level_diff = ($category_list[$lastitem]->level - $category->level);
            }
            $lastitem = $i;
        }
    }
}

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

    $root = 1;
    if (!empty($children) && !isset($children[$root])) {
        $root = array_key_first($children);
    }

    $category_list = HTMLHelper::_('menu.treerecurse', $root, '', array(), $children, 9999, 0, 0);
}

if ($output_style === 'selection') {
    $classes_select = '';
    switch ($bootstrap_version) {
    	case '3': case '4': $classes_select = ' class="form-control"'; break;
    	case '5': $classes_select = ' class="form-select"'; break;
    }
}
?>
<?php if ($output_style === 'selection') : ?>
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
<?php elseif ($output_style === 'list' && $show_category_hierarchy) : ?>
	<ul class="categorytree rawmenu<?php echo ($extra_classes ? ' '. $extra_classes : ''); ?>" <?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($category_list as $category_item) : ?>		
    		<?php $item_class = ''; ?>
    		<?php if ($category_item->deeper) : ?>
    			<?php $item_class .= ' deeper'; ?>
    		<?php endif; ?>
    		<?php if (Helper::isActive($category_item->id, $childrenTree, (int) $selected_category)) : ?>
    			<?php $item_class .= ' active'; ?>
    		<?php endif; ?>
			<li class="category<?php echo $item_class; ?> catid-<?php echo $category_item->id; ?><?php if ($selected_category == $category_item->id) : ?> selected active<?php endif; ?>">
				<?php if (empty($categories_unselectable) || (!empty($categories_unselectable) && !in_array($category_item->id, $categories_unselectable))) : ?>
					<a href="" onclick="document.adminForm.category.value='<?php echo $category_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
				<?php else : ?>
					<span>
				<?php endif; ?>	
					<?php if ($show_picture) : ?>
    					<?php $image = empty($category_item->image) ? $default_picture : $category_item->image; ?>
    					<?php if ($image) : ?>
    						<img alt="<?php echo $category_item->image_alt; ?>" src="<?php echo $image; ?>" />
    					<?php else : ?>
    						<span class="nocategorypicture nopicture">&nbsp;</span>
    					<?php endif; ?>
					<?php endif; ?>
					<span><?php echo $category_item->title; ?></span>
				<?php if (empty($categories_unselectable) || (!empty($categories_unselectable) && !in_array($category_item->id, $categories_unselectable))) : ?>
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>		
    		<?php if ($category_item->deeper) : ?>
    			<ul class="rawmenu-child">
    		<?php elseif ($category_item->shallower) : ?>
    			</li>
    			<?php echo str_repeat('</ul></li>', $category_item->level_diff) ?>
    		<?php else : ?>
    			</li>
    		<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<ul<?php echo ($extra_classes ? ' class="'. $extra_classes .'"' : ''); ?><?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($category_list as $category_item) : ?>
			<li class="category catid-<?php echo $category_item->id; ?><?php if ($selected_category == $category_item->id) : ?> selected<?php endif; ?>">
				<?php if (empty($categories_unselectable) || (!empty($categories_unselectable) && !in_array($category_item->id, $categories_unselectable))) : ?>
					<a href="" onclick="document.adminForm.category.value='<?php echo $category_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
				<?php else : ?>
					<span>
				<?php endif; ?>
					<?php if ($show_picture) : ?>
						<?php $image = empty($category_item->image) ? $default_picture : $category_item->image; ?>
						<?php if ($image) : ?>
							<img alt="<?php echo $category_item->image_alt; ?>" src="<?php echo $image; ?>" />
						<?php else : ?>
							<span class="nocategorypicture nopicture">&nbsp;</span>
						<?php endif; ?>
					<?php endif; ?>
					<span><?php echo $category_item->title; ?></span>
				<?php if (empty($categories_unselectable) || (!empty($categories_unselectable) && !in_array($category_item->id, $categories_unselectable))) : ?>
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>