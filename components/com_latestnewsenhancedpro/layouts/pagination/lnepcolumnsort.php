<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$print = Factory::getApplication()->input->getBool('print', false);

$selected_order = Factory::getApplication()->input->getString('filter_order', '');
$selected_order_dir = Factory::getApplication()->input->getString('filter_order_Dir', 'ASC');
if ($selected_order_dir == '') {
    $selected_order_dir = 'ASC';
}

$column = $displayData['column'];

$icon_asc = isset($displayData['icon_asc']) ? $displayData['icon_asc'] : 'SYWicon-arrow-up';
$icon_desc = isset($displayData['icon_desc']) ? $displayData['icon_desc'] : 'SYWicon-arrow-down';

$disabled_asc = ' disabled="disabled"';
if ($selected_order != $column || $selected_order_dir != 'ASC') {
    $disabled_asc = '';
}

$disabled_dsc = ' disabled="disabled"';
if ($selected_order != $column || $selected_order_dir != 'DESC') {
    $disabled_dsc = '';
}

$btn_classes = 'btn btn-link';
$btn_group_classes = 'heading-sort btn-group';
if ($bootstrap_version == 3) {
    $btn_group_classes .= ' btn-group-xs';
} else if ($bootstrap_version >= 4) {
    $btn_group_classes .= ' btn-group-sm';
}
?>
<?php if (!$print) : ?>
	<div class="<?php echo $btn_group_classes ?>"<?php echo $bootstrap_version > 2 ? ' role="group"' : '' ?>>
		<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_ASC'); ?>" class="<?php echo $btn_classes ?> hasTooltip"<?php echo $disabled_asc; ?> onclick="this.form.filter_order_Dir.value = 'ASC'; this.form.filter_order.value = '<?php echo $column; ?>'; this.form.limitstart.value=0; this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_ASC'); ?>"><i class="<?php echo $icon_asc; ?>"></i></button>
		<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_DESC'); ?>" class="<?php echo $btn_classes ?> hasTooltip"<?php echo $disabled_dsc; ?> onclick="this.form.filter_order_Dir.value = 'DESC'; this.form.filter_order.value = '<?php echo $column; ?>'; this.form.limitstart.value=0; this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_DESC'); ?>"><i class="<?php echo $icon_desc; ?>"></i></button>
	</div>
<?php else : ?>
	<?php if ($selected_order == $column) : ?>
		<?php if ($selected_order_dir == 'ASC') : ?>
			<i class="<?php echo $icon_asc; ?>"></i>
		<?php else : ?>
			<i class="<?php echo $icon_desc; ?>"></i>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>