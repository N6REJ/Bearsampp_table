<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Libraries as SYWLibraries;

/** @var \SYW\Component\LatestNewsEnhancedPro\Site\View\Articles\HtmlView $this */

if ($this->load_bootstrap) {
    HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
}

if ($this->params->get('load_chosen_script', 0)) {
	HTMLHelper::_('formbehavior.chosen', 'select');
}

if ($this->params->get('s_headings', 0)) {
    $this->previous_heading = '';
    $this->heading = '';
    $this->previous_subheading = '';
    $this->subheading = '';
}
?>
<div class="lnep_list<?php echo $this->pageclass_sfx ? ' '.$this->pageclass_sfx : ''; ?><?php echo $this->isMobile ? ' mobile' : ''; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->show_print) : ?>
		<?php
			$layout = new FileLayout('content.lnepicons');

			$data = array('view' => 'articles', 'style' => $this->actions_style, 'classes' => $this->actions_classes, 'layout' => 'list', 'print' => $this->print, 'show_print' => $this->show_print);
			$data['category'] = $this->category_request;
			$data['tag'] = $this->tag_request;
			$data['author'] = $this->author_request;
			$data['alias'] = $this->alias_request;
			$data['period'] = $this->period_request;
			$data['index_fields'] = $this->field_index_array;
			$data['search'] = $this->search_request;
			$data['match'] = $this->search_options_request;
			$data['order'] = $this->order_request;
			$data['dir'] = $this->dir_request;
			$data['limitstart'] = $this->limitstart_request;
			$data['bootstrap_version'] = $this->bootstrap_version;
			$data['load_bootstrap'] = $this->load_bootstrap;

			echo $layout->render($data);
		?>
	<?php endif; ?>

	<?php if (trim($this->article_before_text)) : ?>
		<div class="pretext">
			<?php echo $this->article_before_text; ?>
		</div>
	<?php endif; ?>

	<form action="<?php echo Route::_('index.php?option=com_latestnewsenhancedpro&view=articles&layout=list'); ?>" method="post" name="adminForm" id="adminForm">

		<input type="hidden" name="category" value="<?php echo $this->category_request_value; ?>" />
		<input type="hidden" name="tag" value="<?php echo $this->tag_request_value; ?>" />
		<input type="hidden" name="author" value="<?php echo $this->author_request_value; ?>" />
		<input type="hidden" name="alias" value="<?php echo $this->alias_request_value; ?>" />
		<input type="hidden" name="period" value="<?php echo $this->period_request_value; ?>" />
		<?php foreach ($this->field_index_array as $field_index) : ?>
			<?php $field_request_value = 'field_' . $field_index . '_value'; ?>
			<input type="hidden" name="field_<?php echo $field_index; ?>" value="<?php echo $this->$field_request_value; ?>" />
		<?php endforeach; ?>
		<input type="hidden" name="limitstart" value="<?php echo $this->limitstart_request_value; ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $this->order_request_value; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->dir_request_value; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>

		<?php if (!$this->print) : ?>
			<div class="pagination_wrapper top">
				<?php if ($this->show_search || $this->pagination_top_index[0] || $this->pagination_top_index[1] || $this->pagination_top_index[2] || $this->pagination_bot_index[0] || $this->pagination_bot_index[1] || $this->pagination_bot_index[2]) : ?>
					<?php
            			$layout = new FileLayout('pagination.lnepsearch');

            			$data = array('tag' => $this->tag_request, 'category' => $this->category_request, 'author' => $this->author_request, 'alias' => $this->alias_request, 'period' => $this->period_request);
            			$data['index_fields'] = $this->field_index_array;
            			$data['search'] = $this->search_request;
            			$data['match'] = $this->search_options_request;
            			$data['order'] = $this->order_request;
            			$data['dir'] = $this->dir_request;
            			$data['show_search'] = $this->show_search;
            			$data['show_search_options'] = $this->show_search_options;
            			$data['search_value'] = $this->escape($this->get('state')->get('filter-search'));
            			$data['match_value'] = $this->escape($this->get('state')->get('filter-match'));
            			$data['match_default'] = $this->params->get('search_default', 'any');
            			$data['bootstrap_version'] = $this->bootstrap_version;
            			$data['load_bootstrap'] = $this->load_bootstrap;

						echo $layout->render($data);
            		?>
				<?php endif; ?>

				<?php if ($this->show_pagination && $this->show_pagination_limit) : ?>
					<?php
            			$layout = new FileLayout('pagination.lnepdisplay');
            			$data = array('options' => $this->limits, 'selected' => $this->state->get('list.limit'));
            			$data['bootstrap_version'] = $this->bootstrap_version;
            			$data['load_bootstrap'] = $this->load_bootstrap;
            			echo $layout->render($data);
            		?>
				<?php endif; ?>

				<?php foreach ($this->pagination_top_index as $index) : ?>
					<?php if ($index) : ?><!-- because of backward compatibility -->
						<div class="<?php echo $this->pagination_class[$index] ?>">
							<?php $this->pagination_data[$index]['suffix'] = 'top'; ?>
							<?php echo $this->pagination_layouts[$index]->render($this->pagination_data[$index]); ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>

			<?php
                $this->head_column_width = Helper::getHeadColumnWidth($this->params);

                $this->details_names = Helper::getDetailsColumnNames($this->params);
                $this->details_icons = Helper::getDetailsColumnIcons($this->params);
                $this->sortable_details = Helper::getDetailsSortable($this->params);
                $this->column_widths = Helper::getDetailsColumnWidth($this->params);
                $this->details = Helper::getDetails($this->params, '', 'info_columns');

                $this->details_names_bh = Helper::getDetailsColumnNames($this->params, 'before_');
                $this->details_icons_bh = Helper::getDetailsColumnIcons($this->params, 'before_');
                $this->sortable_details_bh = Helper::getDetailsSortable($this->params, 'before_');
                $this->column_widths_bh = Helper::getDetailsColumnWidth($this->params, 'before_');
                $this->details_bh = Helper::getDetails($this->params, 'before_', 'info_columns');
			?>

			<div class="latestnews-items">
				<?php echo $this->loadTemplate('items'); ?>
			</div>
		<?php endif; ?>

		<div class="pagination_wrapper bottom">
			<?php if ($this->show_pagination) : ?>
				<?php if (!empty($this->items)) : ?>
					<div class="counterpagination<?php echo intval($this->bootstrap_version) == 2 ? ' pagination' : '' ?>">
						<?php if ($this->show_pagination_results) : ?>
							<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
							</p>
						<?php endif; ?>
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
				<?php endif; ?>

				<?php if ($this->show_pagination_results_total) : ?>
					<div class="countertotal<?php echo ($this->pagination->total == 0) ? ' norecord' : '' ?>">
						<?php echo $this->pagination->getResultsCounter(); ?>
					</div>
				<?php else : ?>
					<?php if (empty($this->items)) : ?>
						<div class="countertotal norecord">
							<?php echo Text::_('JLIB_HTML_NO_RECORDS_FOUND'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (!$this->print) : ?>
				<?php foreach ($this->pagination_bot_index as $index) : ?>
					<?php if ($index) : ?>
						<div class="<?php echo $this->pagination_class[$index] ?>">
							<?php $this->pagination_data[$index]['suffix'] = 'bot'; ?>
							<?php echo $this->pagination_layouts[$index]->render($this->pagination_data[$index]); ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</form>

	<?php if (trim($this->article_after_text)) : ?>
		<div class="posttext">
			<?php echo $this->article_after_text; ?>
		</div>
	<?php endif; ?>
</div>
<?php
	if ($this->bootstrap_version == 0) {
		SYWLibraries::loadPureModal($this->load_remotely);
	}

	$layout = new FileLayout('content.lnepmodal'); // Joomla 3.2+

	$data = array('selector' => 'lnepmodal', 'width' => $this->popup_width, 'height' => $this->popup_height);
	$data['bootstrap_version'] = $this->bootstrap_version;
	$data['load_bootstrap'] = $this->load_bootstrap;

	echo $layout->render($data);
?>