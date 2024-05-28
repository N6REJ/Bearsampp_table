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

$selected_period = Factory::getApplication()->input->getString('period', '');

$period_list = $displayData['list'];
$output_style = $displayData['style'];
$selection_label = $displayData['selection_label'];
$disable_years = $displayData['disable_years'];
$extra_classes = isset($displayData['classes']) ? trim($displayData['classes']) : '';

$classes_select = '';
switch ($bootstrap_version) {
	case '3': case '4': $classes_select = ' class="form-control"'; break;
	case '5': $classes_select = ' class="form-select"'; break;
}
?>
<?php if ($output_style == 'selection') : ?>
	<?php if ($selection_label) : ?>
		<label for="period_selector_<?php echo $suffix; ?>"><?php echo $selection_label; ?></label>
	<?php else : ?>
    	<label for="period_selector_<?php echo $suffix; ?>" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JDATE'); ?></label>
	<?php endif; ?>
	<select id="period_selector_<?php echo $suffix; ?>" name="period_selector_<?php echo $suffix; ?>"<?php echo $classes_select; ?> size="1" onChange="document.adminForm.period.value = this.value; document.adminForm.limitstart.value=0; document.adminForm.submit()">
		<option value=""<?php if (empty($selected_period)) : ?> selected="selected"<?php endif; ?>><?php echo Text::_('JALL'); ?></option>
		<?php foreach ($period_list as $key => $value) : ?>
			<?php $disabled = ''; ?>
			<?php if (strpos($key, '00') !== false || strpos($key, '99') !== false) : ?>
				<?php $key = substr($key, 0, -3); ?>
				<?php if ($disable_years) : ?>
					<?php $disabled = ' disabled="disabled"'; ?>
				<?php endif; ?>
			<?php endif; ?>
			<option value="<?php echo $key; ?>"<?php if ($selected_period == $key) : ?> selected="selected"<?php endif; ?><?php echo $disabled; ?>><?php echo $value; ?></option>
		<?php endforeach; ?>
	</select>
<?php else : ?>
	<ul<?php echo ($extra_classes ? ' class="'. $extra_classes .'"' : ''); ?><?php echo ($selection_label ? ' data-label="'. $selection_label .'"' : ''); ?>>
		<?php foreach ($period_list as $key => $value) : ?>
			<?php if ($disable_years && (strpos($key, '00') !== false || strpos($key, '99') !== false)) : ?>
				<li class="period period-year">
					<span><?php echo $value; ?></span>
				</li>
			<?php else : ?>
				<?php $extra_class = 'period-month'; ?>
				<?php if (strpos($key, '00') !== false || strpos($key, '99') !== false) : ?>
					<?php $key = substr($key, 0, -3); ?>
					<?php $extra_class = 'period-year'; ?>
				<?php endif; ?>
				<li class="period <?php echo $extra_class; ?><?php if ($selected_period == $key) : ?> selected<?php endif; ?>">
					<a href="" onclick="document.adminForm.period.value='<?php echo $key; ?>'; document.adminForm.limitstart.value=0; document.adminForm.submit(); return false;">
						<span><?php echo $value; ?></span>
					</a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>