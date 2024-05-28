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

// COM_LATESTNEWSENHANCEDPRO_FILTERFIELD_[field name] must be translated in the Joomla console language overrides

$field_id = $displayData['id'];
$field_name = $displayData['name'];
$options = $displayData['list'];
$multiple = $displayData['multiple']; // not supported yet
$output_style = $displayData['style'];
$selection_label = $displayData['selection_label'];
$extra_classes = isset($displayData['classes']) ? trim($displayData['classes']) : '';

$selected_option = Factory::getApplication()->input->getString('field_' . $field_id, '');

$classes_select = '';
switch ($bootstrap_version) {
	case '3': case '4': $classes_select = ' class="form-control"'; break;
	case '5': $classes_select = ' class="form-select"'; break;
}
?>
<?php if ($output_style == 'selection') : ?>
	<?php if ($selection_label) : ?>
	    <label for="field_<?php echo $field_id; ?>_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
	    <label for="field_<?php echo $field_id; ?>_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_FILTERFIELD_' . strtoupper($field_name)); ?></label>
	<?php endif; ?>
	<select id="field_<?php echo $field_id; ?>_selector_<?php echo $suffix; ?>" name="field_<?php echo $field_id; ?>_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.field_<?php echo $field_id; ?>.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">
		<option value=""<?php if (empty($selected_option)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
	    <?php foreach ($options as $option) : ?>
	    	<option value="<?php echo $option->value; ?>"<?php if ($selected_option == $option->value) : ?> selected="selected"<?php endif; ?>><?php echo Text::_($option->name); ?></option>
	    <?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($extra_classes ? ' class="'. $extra_classes .'"' : ''); ?><?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($options as $option) : ?>
			<li class="field field-<?php echo $field_id; ?><?php if ($selected_option == $option->value) : ?> selected<?php endif; ?>">
				<a href="" onclick="document.adminForm.field_<?php echo $field_id; ?>.value = '<?php echo $option->value; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
					<span><?php echo Text::_($option->name); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>