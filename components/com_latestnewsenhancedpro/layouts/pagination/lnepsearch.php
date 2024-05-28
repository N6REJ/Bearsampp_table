<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;

$wam = Factory::getApplication()->getDocument()->getWebAssetManager();

$bootstrap_version = isset($displayData['bootstrap_version']) ? intval($displayData['bootstrap_version']) : 5;

$category = isset($displayData['category']) ? $displayData['category'] : '';
$tag = isset($displayData['tag']) ? $displayData['tag'] : '';
$author = isset($displayData['author']) ? $displayData['author'] : '';
$alias = isset($displayData['alias']) ? $displayData['alias'] : ''; 
$period = isset($displayData['period']) ? $displayData['period'] : '';
$index_fields = isset($displayData['index_fields']) ? $displayData['index_fields'] : '';
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

$show_search = $displayData['show_search'];
$search_value = $displayData['search_value'];

$show_search_options = isset($displayData['show_search_options']) ? $displayData['show_search_options'] : false;
$match_value = isset($displayData['match_value']) ? $displayData['match_value'] : '';
$match_default = isset($displayData['match_default']) ? $displayData['match_default'] : 'any';

if ($show_search) {
	$on_click = 'document.adminForm.limitstart.value=0;';
	if ($category) {
		$on_click .= 'document.adminForm.category.value=\'\';';
	}
	if ($tag) {
		$on_click .= 'document.adminForm.tag.value=\'\';';
	}
	if ($author) {
		$on_click .= 'document.adminForm.author.value=\'\';';
	}
	if ($alias) {
	    $on_click .= 'document.adminForm.alias.value=\'\';';
	}
	if ($period) {
		$on_click .= 'document.adminForm.period.value=\'\';';
	}
	if ($index_fields) {
		foreach ($index_fields as $field_id) {
			$on_click .= 'document.adminForm.field_' . $field_id . '.value=\'\';';
		}
	}
}

$script = 'function clearForm() { ';
if ($show_search) {
	$script .= 'document.getElementById(\'filter-search\').value=\'\'; ';
	if ($show_search_options) {
		$script .= 'document.getElementById(\'filter-match-' . $match_default . '\').checked=true; ';
	}
}
if ($category) {
	$script .= 'document.adminForm.category.value=\'\';';
}
if ($tag) {
	$script .= 'document.adminForm.tag.value=\'\';';
}
if ($author) {
	$script .= 'document.adminForm.author.value=\'\';';
}
if ($alias) {
    $script .= 'document.adminForm.alias.value=\'\';';
}
if ($period) {
	$script .= 'document.adminForm.period.value=\'\';';
}
if ($index_fields) {
	foreach ($index_fields as $field_id) {
		$script .= 'document.adminForm.field_' . $field_id . '.value=\'\';';
	}
}

if ($order) {
	$script .= 'document.adminForm.filter_order.value=\'\'; ';
	$script .= 'document.adminForm.filter_order_Dir.value=\'\'; ';
}

$script .= 'document.adminForm.limitstart.value=0; ';
$script .= 'document.adminForm.submit(); ';
$script .= ' } ';

$wam->addInlineScript($script);
?>
<?php if ($show_search) : ?>
	<div class="searchblock">
    	<?php if ($bootstrap_version < 3) : ?>
            <div class="form-inline">
        		<label for="filter-search" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JSEARCH_FILTER'); ?></label>
        		<div class="input-append">
    				<input type="text" name="filter-search" id="filter-search" class="input-medium" placeholder="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_TEXT'); ?>" value="<?php echo $search_value; ?>" />
    				<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>" class="btn hasTooltip" name="searchbtn" id="searchbtn" onclick="<?php echo $on_click; ?>this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>"><i class="SYWicon-search"></i></button>
    			</div>
    			<button type="button" class="btn" onclick="clearForm(); return false;"><?php echo Text::_('JSEARCH_FILTER_CLEAR');?></button>
			</div>
			<?php if ($show_search_options) : ?>
				<div class="options">
					<label class="radio inline">
	  					<input type="radio" name="filter-match" id="filter-match-all" value="all"<?php echo ($match_value == 'all' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ALL_WORDS'); ?>
					</label>
					<label class="radio inline">
	  					<input type="radio" name="filter-match" id="filter-match-any" value="any"<?php echo ($match_value == 'any' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ANY_WORDS'); ?>
					</label>
					<label class="radio inline">
	  					<input type="radio" name="filter-match" id="filter-match-exact" value="exact"<?php echo ($match_value == 'exact' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_EXACT_PHRASE'); ?>
					</label>
				</div>
			<?php endif; ?>
    	<?php endif; ?>
    	<?php if ($bootstrap_version == 3) : ?>
            <div class="form-inline">
            	<div class="input-group">
            		<label for="filter-search" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JSEARCH_FILTER'); ?></label>
            		<input type="text" name="filter-search" id="filter-search" class="form-control" placeholder="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_TEXT'); ?>" value="<?php echo $search_value; ?>" />
            		<span class="input-group-btn">
            			<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>" class="btn btn-primary hasTooltip" name="searchbtn" id="searchbtn" onclick="<?php echo $on_click; ?>this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>"><i class="SYWicon-search"></i></button>
            		</span>
            	</div>
            	<button type="button" class="btn btn-primary" onclick="clearForm(); return false;"><?php echo Text::_('JSEARCH_FILTER_CLEAR');?></button>
        	</div>
			<?php if ($show_search_options) : ?>
				<div class="options">
					<label class="radio-inline">
	  					<input type="radio" name="filter-match" id="filter-match-all" value="all"<?php echo ($match_value == 'all' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ALL_WORDS'); ?>
					</label>
					<label class="radio-inline">
	  					<input type="radio" name="filter-match" id="filter-match-any" value="any"<?php echo ($match_value == 'any' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ANY_WORDS'); ?>
					</label>
					<label class="radio-inline">
	  					<input type="radio" name="filter-match" id="filter-match-exact" value="exact"<?php echo ($match_value == 'exact' ? ' checked' : ''); ?>>
						<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_EXACT_PHRASE'); ?>
					</label>
				</div>
			<?php endif; ?>
    	<?php endif; ?>
    	<?php if ($bootstrap_version == 4) : ?>
            <div class="form-inline">
            	<div class="input-group mr-1">
            		<label for="filter-search" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JSEARCH_FILTER'); ?></label>
            		<input type="text" name="filter-search" id="filter-search" class="form-control" placeholder="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_TEXT'); ?>" value="<?php echo $search_value; ?>" />
            		<div class="input-group-append">
            			<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>" class="btn btn-primary hasTooltip" name="searchbtn" id="searchbtn" onclick="<?php echo $on_click; ?>this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>"><i class="SYWicon-search"></i></button>
            		</div>
            	</div>
    			<button type="button" class="btn btn-primary" onclick="clearForm(); return false;"><?php echo Text::_('JSEARCH_FILTER_CLEAR');?></button>
            </div>
			<?php if ($show_search_options) : ?>
				<div class="options">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-all" value="all"<?php echo ($match_value == 'all' ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ALL_WORDS'); ?></label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-any" value="any"<?php echo (($match_value == 'any') ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ANY_WORDS'); ?></label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-exact" value="exact"<?php echo ($match_value == 'exact' ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_EXACT_PHRASE'); ?></label>
					</div>
				</div>
			<?php endif; ?>
    	<?php endif; ?>
    	<?php if ($bootstrap_version == 5) : ?>
            <div class="row mb-3">
            	<div class="col-auto">
	            	<div class="input-group me-1">
	            		<label for="filter-search" class="<?php echo SYWUtilities::getBootstrapProperty('visually-hidden', $bootstrap_version) ?>"><?php echo Text::_('JSEARCH_FILTER'); ?></label>
	            		<input type="text" name="filter-search" id="filter-search" class="form-control" placeholder="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_TEXT'); ?>" value="<?php echo $search_value; ?>" />
	            		<button type="button" aria-label="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>" class="btn btn-primary hasTooltip" name="searchbtn" id="searchbtn" onclick="<?php echo $on_click; ?>this.form.submit(); return false;" title="<?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH');?>"><i class="SYWicon-search"></i></button>
	            	</div>
	            </div>
            	<div class="col-auto">
    				<button type="button" class="btn btn-primary" onclick="clearForm(); return false;"><?php echo Text::_('JSEARCH_FILTER_CLEAR');?></button>
	            </div>
            </div>
			<?php if ($show_search_options) : ?>
				<div class="options mb-3">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-all" value="all"<?php echo ($match_value == 'all' ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ALL_WORDS'); ?></label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-any" value="any"<?php echo (($match_value == 'any') ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_ANY_WORDS'); ?></label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="filter-match" id="filter-match-exact" value="exact"<?php echo ($match_value == 'exact' ? ' checked' : ''); ?>>
						<label class="form-check-label"><?php echo Text::_('COM_LATESTNEWSENHANCEDPRO_SEARCH_EXACT_PHRASE'); ?></label>
					</div>
				</div>
			<?php endif; ?>
    	<?php endif; ?>
    </div>
    <script>
		document.getElementById("filter-search").addEventListener("keyup", function(event) { event.preventDefault(); if (event.keyCode === 13) { document.getElementById("searchbtn").click(); } });
    </script>
<?php else : ?>
	<div class="clearblock">
    	<button type="button" class="btn<?php echo $bootstrap_version > 2 ? ' btn-primary' : '' ?>" onclick="clearForm(); return false;"><?php echo Text::_('JSEARCH_FILTER_CLEAR');?></button>
    </div>
<?php endif; ?>