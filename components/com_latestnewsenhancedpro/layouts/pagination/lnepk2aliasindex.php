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

$selected_alias = Factory::getApplication()->input->getString('alias', '');

$alias_list = $displayData['list'];
$output_style = $displayData['style'];
$selection_label = $displayData['selection_label'];

$classes_select = '';
switch ($bootstrap_version) {
	case '3': case '4': $classes_select = ' class="form-control"'; break;
	case '5': $classes_select = ' class="form-select"'; break;
}
?>
<?php if ($output_style == 'selection') : ?>
	<?php if ($selection_label) : ?>
		<label for="alias_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
    	<label for="alias_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_ALIAS'); ?></label>
	<?php endif; ?>
	<select id="alias_selector_<?php echo $suffix; ?>" name="alias_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.alias.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">	
		<option value=""<?php if (empty($selected_alias)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
		<?php foreach ($alias_list as $alias_item) : ?>
			<option value="<?php echo $alias_item; ?>"<?php if ($selected_alias == $alias_item) : ?> selected="selected"<?php endif; ?>><?php echo $alias_item; ?></option>
		<?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($alias_list as $alias_item) : ?>
			<li class="alias alias-<?php echo $alias_item; ?><?php if ($selected_alias == $alias_item) : ?> selected<?php endif; ?>">
				<a href="" onclick="document.adminForm.alias.value='<?php echo $alias_item; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
					<span><?php echo $alias_item; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>