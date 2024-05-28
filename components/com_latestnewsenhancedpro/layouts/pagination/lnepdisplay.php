<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;
$load_bootstrap = isset($displayData['load_bootstrap']) ? $displayData['load_bootstrap'] : true;
if ($load_bootstrap) {
    HTMLHelper::_('bootstrap.framework');
}

$options = $displayData['options'];
$selected = $displayData['selected'];

$classes_select = '';
switch ($bootstrap_version) {
	case '3': case '4': $classes_select = ' form-control'; break;
	case '5': $classes_select = ' form-select'; break;
}
?>
<div class="pagination_limit">
	<label for="limit" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?></label>
	<?php echo HTMLHelper::_('select.genericlist', $options, 'limit', 'class="inputbox' . $classes_select . '" size="1" onchange="this.form.submit()"', 'value', 'text', $selected); ?>
</div>