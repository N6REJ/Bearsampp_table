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

$selected_tag = Factory::getApplication()->input->getString('tag', '');

$tag_list = $displayData['list'];
$output_style = $displayData['style'];
$use_tag_classes = $displayData['use_tag_classes'];
$show_picture = $displayData['show_picture'];
$default_picture = $displayData['default_picture'];
$selection_label = $displayData['selection_label'];
$tags_unselectable = isset($displayData['unselectable']) ? $displayData['unselectable'] : null;
$show_tag_hierarchy = isset($displayData['show_hierarchy']) ? $displayData['show_hierarchy'] : false;
$extra_classes = isset($displayData['classes']) ? trim($displayData['classes']) : '';

if ($output_style === 'list' && $show_tag_hierarchy) {
    
    SYWStylesheets::loadPureTreeMenu();
    
    $ex_classes = explode(' ', $extra_classes);
    if (in_array('pre', $ex_classes) || in_array('post', $ex_classes)) {
        
        SYWLibraries::loadPureTreeMenu();
        $preset = SYWUtilities::loadPureTreePreset($ex_classes);
        
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = Factory::getDocument()->getWebAssetManager();
        
        $js = <<< JS
    		window.addEventListener('load', function() {
    			let tagtree = new pureTreeMenu({
    				containerSelector: '.tagtree.rawmenu',
                    iconPrefixClass: '{$preset['prefix']}',
                    iconRetractedClass: '{$preset['retracted']}',
                    iconExpandedClass: '{$preset['expanded']}'
    			});
    		}, false);
JS;
        $wa->addInlineScript($js);
    }
    
    $childrenTree = array();
    
    if ($tag_list) {
        $lastitem = 0;
        foreach ($tag_list as $i => $tag) {
            $parent = $tag->parent_id;
            $list = @$childrenTree[$parent] ? $childrenTree[$parent] : array();
            if (!isset($childrenTree[$parent])) {
                $childrenTree[$parent] = [];
            }
            
            $childrenTree[$parent][] = $tag->id;
            
            $tag->deeper = false;
            $tag->shallower = false;
            $tag->level_diff = 0;
            
            if (isset($tag_list[$lastitem])) {
                $tag_list[$lastitem]->deeper     = ($tag->level > $tag_list[$lastitem]->level);
                $tag_list[$lastitem]->shallower  = ($tag->level < $tag_list[$lastitem]->level);
                $tag_list[$lastitem]->level_diff = ($tag_list[$lastitem]->level - $tag->level);
            }
            $lastitem = $i;
        }
    }
}

if ($show_tag_hierarchy) {
    
    $children = array();
    
    if ($tag_list) {
        foreach ($tag_list as $tag) {
            $parent = $tag->parent_id;
            $list = @$children[$parent] ? $children[$parent] : array();
            array_push($list, $tag);
            $children[$parent] = $list;
        }
    }
    
    $root = 1;
    if (!empty($children) && !isset($children[$root])) {
        $root = array_key_first($children);
    }
    
    $tag_list = HTMLHelper::_('menu.treerecurse', $root, '', array(), $children, 9999, 0, 0);
}

if ($output_style === 'selection') {
	$classes_select = '';
	switch ($bootstrap_version) {
		case '3': case '4': $classes_select = ' class="form-control"'; break;
		case '5': $classes_select = ' class="form-select"'; break;
	}
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
			<?php
                if ($show_tag_hierarchy) {
                	$tag_item->title = str_ireplace('-', '', $tag_item->treename);
                	$tag_item->title = str_ireplace('&#160;&#160;', '- ', $tag_item->title);
                }

                $disabled = '';
                if (!empty($tags_unselectable) && in_array($tag_item->id, $tags_unselectable)) {
                    $disabled = ' disabled="disabled"';
                }
            ?>
			<option value="<?php echo $tag_item->id; ?>"<?php echo $disabled; ?><?php if ($selected_tag == $tag_item->id) : ?> selected="selected"<?php endif; ?>><?php echo $tag_item->title; ?></option>
		<?php endforeach; ?>
	</select>
<?php elseif ($output_style === 'list' && $show_tag_hierarchy) : ?>
	<ul class="tagtree rawmenu<?php echo ($extra_classes ? ' '. $extra_classes : ''); ?>" <?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($tag_list as $tag_item) : ?>		
			<?php
				$tag_class = '';
				if ($use_tag_classes) {
					if (trim($tag_item->link_class) != '') {
						$tag_class = ' class="'.$tag_item->link_class.'"';
					}
				}
			?>		
    		<?php $item_class = ''; ?>
    		<?php if ($tag_item->deeper) : ?>
    			<?php $item_class .= ' deeper'; ?>
    		<?php endif; ?>
    		<?php if (Helper::isActive($tag_item->id, $childrenTree, (int) $selected_tag)) : ?>
    			<?php $item_class .= ' active'; ?>
    		<?php endif; ?>		
			<li class="tag<?php echo $item_class; ?> tag-<?php echo $tag_item->id; ?><?php if ($selected_tag == $tag_item->id) : ?> selected active<?php endif; ?>">				
				<?php if (empty($tags_unselectable) || (!empty($tags_unselectable) && !in_array($tag_item->id, $tags_unselectable))) : ?>
					<a href=""<?php echo $tag_class; ?> onclick="document.adminForm.tag.value='<?php echo $tag_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
				<?php else : ?>
					<span<?php echo $tag_class; ?>>
				<?php endif; ?>	
					<?php if ($show_picture) : ?>
    					<?php $image = empty($tag_item->image) ? $default_picture : $tag_item->image; ?>
    					<?php if ($image) : ?>
    						<img alt="<?php echo $tag_item->image_alt; ?>" src="<?php echo $image; ?>" />
    					<?php else : ?>
    						<span class="notagpicture nopicture">&nbsp;</span>
    					<?php endif; ?>
					<?php endif; ?>
					<span><?php echo $tag_item->title; ?></span>
				<?php if (empty($tags_unselectable) || (!empty($tags_unselectable) && !in_array($tag_item->id, $tags_unselectable))) : ?>
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>		
    		<?php if ($tag_item->deeper) : ?>
    			<ul class="rawmenu-child">
    		<?php elseif ($tag_item->shallower) : ?>
    			</li>
    			<?php echo str_repeat('</ul></li>', $tag_item->level_diff) ?>
    		<?php else : ?>
    			</li>
    		<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<ul<?php echo ($extra_classes ? ' class="'. $extra_classes .'"' : ''); ?><?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($tag_list as $tag_item) : ?>
			<?php
				$tag_class = '';
				if ($use_tag_classes) {
					if (trim($tag_item->link_class) != '') {
						$tag_class = ' class="'.$tag_item->link_class.'"';
					}
				}
			?>
			<li class="tag tag-<?php echo $tag_item->id; ?><?php if ($selected_tag == $tag_item->id) : ?> selected<?php endif; ?>">
				<?php if (empty($tags_unselectable) || (!empty($tags_unselectable) && !in_array($tag_item->id, $tags_unselectable))) : ?>
					<a href=""<?php echo $tag_class; ?> onclick="document.adminForm.tag.value='<?php echo $tag_item->id; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
				<?php else : ?>
					<span<?php echo $tag_class; ?>>
				<?php endif; ?>					
					<?php if ($show_picture) : ?>
						<?php $image = empty($tag_item->image) ? $default_picture : $tag_item->image; ?>
						<?php if ($image) : ?>
							<img alt="<?php echo $tag_item->image_alt; ?>" src="<?php echo $image; ?>" />
						<?php else : ?>
							<span class="notagpicture nopicture">&nbsp;</span>
						<?php endif; ?>
					<?php endif; ?>
					<span><?php echo $tag_item->title; ?></span>
				<?php if (empty($tags_unselectable) || (!empty($tags_unselectable) && !in_array($tag_item->id, $tags_unselectable))) : ?>	
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>