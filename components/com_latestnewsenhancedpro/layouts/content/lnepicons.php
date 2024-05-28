<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use SYW\Library\Utilities as SYWUtilities;

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;
$load_bootstrap = isset($displayData['load_bootstrap']) ? $displayData['load_bootstrap'] : true;
if ($load_bootstrap) {
	HTMLHelper::_('bootstrap.framework');
}

$view = $displayData['view'];
$layout = isset($displayData['layout']) ? '&layout=' . $displayData['layout'] : '';

$active_menu_item = Factory::getApplication()->getMenu()->getActive();
$itemId = isset($active_menu_item) ? '&Itemid=' . $active_menu_item->id : '';

$category = isset($displayData['category']) ? $displayData['category'] : '';
$tag = isset($displayData['tag']) ? $displayData['tag'] : '';
$author = isset($displayData['author']) ? $displayData['author'] : '';
$alias = isset($displayData['alias']) ? $displayData['alias'] : '';
$period = isset($displayData['period']) ? $displayData['period'] : '';
$search = isset($displayData['search']) ? $displayData['search'] : '';
$match = isset($displayData['match']) ? $displayData['match'] : '';
if (empty($search)) {
	$match = '';
}
$order = isset($displayData['order']) ? $displayData['order'] : '';
$dir = isset($displayData['dir']) ? $displayData['dir'] : '';
if (empty($order)) {
	$dir = '';
}

$field = '';
$index_fields = isset($displayData['index_fields']) ? $displayData['index_fields'] : array();
foreach ($index_fields as $field_id) {
	$input_request = Factory::getApplication()->input->getString('field_' . $field_id, '');
	if ($input_request) {
		$field .= '&field_' . $field_id . '=' . $input_request;
	}
}

$limitstart = isset($displayData['limitstart']) ? $displayData['limitstart'] : '';

$print = $displayData['print'];

$showPrint = $displayData['show_print'];

$output_style = isset($displayData['style']) ? $displayData['style'] : 'dropdown';
$action_classes = isset($displayData['classes']) ? $displayData['classes'] : '';
if ($action_classes) {
	$action_classes = ' class="' . $action_classes . '"';
}
?>
<?php if ($showPrint) : ?>
	<?php if (!$print) : ?>
		<div class="icons">
			<?php if ($bootstrap_version == 0 || $output_style == 'list') : ?>
				<ul class="list">
					<li class="print-icon">
			            <a<?php echo $action_classes; ?> rel="nofollow" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
			           		href="<?php echo Route::_('index.php?option=com_latestnewsenhancedpro&view='.$view.$layout.$itemId.$category.$tag.$author.$alias.$period.$field.$search.$match.$order.$dir.$limitstart.'&tmpl=component&print=1'); ?>">
			                <i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
			            </a>
					</li>
				</ul>
			<?php else : ?>
    			<?php if ($bootstrap_version == 2) : ?>
    				<div class="btn-group pull-right">
    					<button class="btn dropdown-toggle" type="button" id="dropdownIcons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo Text::_('JUSER_TOOLS'); ?>">
    						<i class="SYWicon-settings" aria-hidden="true"></i>&nbsp;<span class="caret" aria-hidden="true"></span>
    					</button>
    					<ul class="dropdown-menu" aria-labelledby="dropdownIcons">
        					<li class="print-icon">
        						<a rel="nofollow" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
        							href="<?php echo Route::_('index.php?option=com_latestnewsenhancedpro&view='.$view.$layout.$itemId.$category.$tag.$author.$alias.$period.$field.$search.$match.$order.$dir.$limitstart.'&tmpl=component&print=1'); ?>">
        							<i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
        						</a>
        					</li>
    					</ul>
    				</div>
    			<?php endif; ?>
    			<?php if ($bootstrap_version == 3) : ?>
    				<div class="dropdown pull-right">
          				<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownIcons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo Text::_('JUSER_TOOLS'); ?>">
          					<i class="SYWicon-settings" aria-hidden="true"></i>&nbsp;<span class="caret" aria-hidden="true"></span>
          				</button>
        				<ul class="dropdown-menu" aria-labelledby="dropdownIcons">
        					<li class="print-icon">
        						<a rel="nofollow" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
        							href="<?php echo Route::_('index.php?option=com_latestnewsenhancedpro&view='.$view.$layout.$itemId.$category.$tag.$author.$alias.$period.$field.$search.$match.$order.$dir.$limitstart.'&tmpl=component&print=1'); ?>">
        							<i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
        						</a>
        					</li>
          				</ul>
        			</div>
    			<?php endif; ?>
    			<?php if ($bootstrap_version >= 4) : ?>
    				<div class="btn-group <?php echo SYWUtilities::getBootstrapProperty('float-right', $bootstrap_version) ?>">
        				<button type="button" class="btn btn-primary dropdown-toggle" data-<?php echo ($bootstrap_version >= 5 ? 'bs-' : ''); ?>toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo Text::_('JUSER_TOOLS'); ?>">
        					<i class="SYWicon-settings" aria-hidden="true"></i>
        				</button>
        				<div class="dropdown-menu">
        					<a class="dropdown-item print-icon" rel="nofollow"
        						onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
        						href="<?php echo Route::_('index.php?option=com_latestnewsenhancedpro&view='.$view.$layout.$itemId.$category.$tag.$author.$alias.$period.$field.$search.$match.$order.$dir.$limitstart.'&tmpl=component&print=1'); ?>">
        						<i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
        					</a>
        				</div>
        			</div>
    			<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php if ($bootstrap_version == 0) : ?>
			<div id="pop-print" style="float: right;" class="hidden-print">
    	    	<a class="btn btn-light" href="#" onclick="window.print();return false;">
    	        	<i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
    	        </a>
    		</div>
		<?php else : ?>
			<div id="pop-print" class="<?php echo SYWUtilities::getBootstrapProperty('float-right', $bootstrap_version) ?> hidden-print">
				<a class="btn btn-primary" href="#" onclick="window.print();return false;">
					<i class="SYWicon-print" aria-hidden="true"></i><span>&nbsp;<?php echo Text::_('JGLOBAL_PRINT'); ?></span>
				</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>