<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\CalendarHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

// headings

$this->showHeadings = $this->params->get('s_headings', 0);
$this->showSubHeadings = false;
$this->monthFormat = $this->params->get('heading_m_format', 'F');

$i_header = 0; // keep or else subsequent pages won't work well with pagination

// link

$this->link_title = false;
$this->link_head = false;
$this->add_readmore = false;
$this->append_readmore = false;
$what_to_link = $this->params->get('what_to_link', '');
if (is_array($what_to_link)) {
	foreach ($what_to_link as $choice) {
		switch ($choice) {
			case 'title' : $this->link_title = true; break;
			case 'head' : $this->link_head = true; break;
			case 'label' : $this->add_readmore = true; break;
			case 'append' : $this->append_readmore = true; break;
		}
	}
}

$this->follow = $this->params->get('follow', 1);
$this->link_tooltip = $this->params->get('readmore_tooltip', 1);
?>

<?php if (!$this->showHeadings) : ?>
	<table class="<?php echo $this->params->get('table_classes', 'table table-striped'); ?>"><?php echo $this->loadTemplate('tableheader'); ?><tbody>
<?php endif; ?>

<?php foreach ($this->items as $i => $this->item) : ?>

	<?php if ($this->show_errors && !empty($this->item->error)) : ?>
		<?php foreach ($this->item->error as $error) :  ?>
			<?php Factory::getApplication()->enqueueMessage('id '.$this->item->id.': '.$error, 'error'); ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ($i < $this->params->get('leading_count', 0)) : ?>
		<?php $this->isleading = true; ?>
		<?php $this->head_width = $this->params->get('leading_head_w', '128'); ?>
		<?php $this->head_height = $this->params->get('leading_head_h', '128'); ?>
		<?php $this->show_calendar = $this->params->get('leading_head_type', 'none') == 'calendar' || substr($this->params->get('leading_head_type', 'none'), 0, strlen('k2field:date')) === 'k2field:date' ? true : false; ?>
		<?php $this->show_image = (!$this->show_calendar && $this->params->get('leading_head_type', 'none') != 'none') ? true : false ?>
	<?php else : ?>
		<?php $this->isleading = false; ?>
		<?php $this->head_width = $this->params->get('head_w', '64'); ?>
		<?php $this->head_height = $this->params->get('head_h', '64'); ?>
		<?php $this->show_calendar = $this->params->get('head_type', 'none') == 'calendar' || substr($this->params->get('head_type', 'none'), 0, strlen('k2field:date')) === 'k2field:date' ? true : false; ?>
		<?php $this->show_image = (!$this->show_calendar && $this->params->get('head_type', 'none') != 'none') ? true : false ?>
	<?php endif; ?>

	<?php
		$link_label_item = '';
		if ($this->item->link && ($this->add_readmore || $this->append_readmore)) {
			if ($this->item->authorized) {
				$link_label_item = trim($this->params->get('link_lbl', '')) != '' ? $this->params->get('link_lbl', '') :  $this->item->linktitle; //JText::_('COM_LATESTNEWSENHANCEDPRO_READMORE_LABEL');
			} else {
				$link_label_item = trim($this->params->get('unauthorized_link_lbl', '')) != '' ? $this->params->get('unauthorized_link_lbl', '') :  $this->item->linktitle; //JText::_('COM_LATESTNEWSENHANCEDPRO_UNAUTHORIZEDREADMORE_LABEL');
			}
		}

		$this->cat_label_item = '';
		if ($this->item->category_authorized) {
			$this->cat_label_item = trim($this->params->get('cat_link_lbl', '')) != '' ? $this->params->get('cat_link_lbl', '') : $this->item->category_title;
		} else {
			$this->cat_label_item = trim($this->params->get('unauthorized_cat_link_lbl', '')) != '' ? $this->params->get('unauthorized_cat_link_lbl', '') : $this->item->category_title;
		}
	?>

	<?php
		$extraclasses = $this->item->featured ? ' featured' : '';

		if (!$this->item->category_authorized) {
			$extraclasses .= ' cat-unauthorized';
		}

		if (!$this->item->authorized) {
			$extraclasses .= ' unauthorized';
		}

		if ($this->item->state == 0) {
			$extraclasses .= ' unpublished';
		}

		$css_hover_picture = '';
		if ($this->item->link && $this->link_head && $this->item->cropped) {
			if ($this->show_image && $this->params->get('hover_effect_pic', 'none') != 'none') {
				$css_hover_picture = ' hvr-'.$this->params->get('hover_effect_pic', 'none');
			}
		}

		$i_header++;
	?>

	<?php
        if ($this->showHeadings) {

            switch ($this->showHeadings) {
                case 'author' : $this->heading = $this->item->author; break;
                case 'date_y' : $this->heading = $this->item->year; break;
                case 'date_m' : $this->heading = HTMLHelper::_('date', $this->item->date, $this->monthFormat); break;
                case 'date_ym' :
                	$this->heading = $this->item->year;
                	$this->subheading = HTMLHelper::_('date', $this->item->date, $this->monthFormat);
                	$this->showSubHeadings = true;
                	break;
                default :
                	$this->heading = $this->item->category_title;
                	if ($this->params->get('headings_cat_desc', 0)) {
                		$this->heading_description = HTMLHelper::_('content.prepare', $this->categories_list[$this->item->catid]->description, '', 'com_content.category');
                	}
                	if ($this->params->get('headings_cat_image', 0)) {
                		$this->heading_image = $this->categories_list[$this->item->catid]->image;
                		$this->heading_image_alt = $this->categories_list[$this->item->catid]->image_alt;
                	}
            }

        	if (!isset($this->previous_heading)) {
        	    $this->previous_heading = Factory::getApplication()->getUserState('global.ajaxlist.previousheading', '');
        	}

        	if ($this->showSubHeadings && !isset($this->previous_subheading)) {
        	    $this->previous_subheading = Factory::getApplication()->getUserState('global.ajaxlist.previoussubheading', '');
        	}

            if ($this->previous_heading == $this->heading) {
                $this->heading = 'noshow';
    		} else {
    		    $this->previous_heading = $this->heading;
    		    Factory::getApplication()->setUserState('global.ajaxlist.previousheading', $this->previous_heading);
    		}

    		if ($this->showSubHeadings) {
        		if ($this->previous_subheading == $this->subheading) {
        		    $this->subheading = 'noshow';
        		} else {
        		    $this->previous_subheading = $this->subheading;
        		    Factory::getApplication()->setUserState('global.ajaxlist.previoussubheading', $this->previous_subheading);
        		}
    		}
    	}
	?>

	<?php if ($this->showHeadings && $this->heading && $this->heading != 'noshow') : ?>

		<?php if ($i_header > 1) : ?>
			</tbody></table>
		<?php endif; ?>

		<div class="heading_group">
			<h<?php echo $this->params->get('headings_tag', '2'); ?> class="heading"><?php echo $this->heading; ?></h<?php echo $this->params->get('headings_tag', '2'); ?>>

			<?php if ((isset($this->heading_description) && $this->heading_description) || (isset($this->heading_image) && $this->heading_image)) : ?>
				<div class="heading_description">
					<?php if (isset($this->heading_image) && $this->heading_image) : ?>
						<img class="heading_image" src="<?php echo $this->heading_image; ?>" alt="<?php echo htmlspecialchars($this->heading_image_alt, ENT_COMPAT, 'UTF-8'); ?>" />
					<?php endif; ?>
					<?php if (isset($this->heading_description) && $this->heading_description) : ?>
						<div class="heading_text"><?php echo $this->heading_description; ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($this->showSubHeadings && $this->subheading && $this->subheading != 'noshow') : ?>
				<h<?php echo ((int) $this->params->get('headings_tag', '2') + 1); ?> class="heading"><?php echo $this->subheading; ?></h<?php echo ((int) $this->params->get('headings_tag', '2') + 1); ?>>
			<?php endif; ?>
		</div>

		<table class="<?php echo $this->params->get('table_classes', 'table table-striped'); ?>"><?php echo $this->loadTemplate('tableheader'); ?><tbody>
	<?php else : ?>
		<?php if ($this->params->get('leading_line_break', 0) && $this->params->get('leading_count', 0) > 0 && $i == $this->params->get('leading_count', 0)) : ?>
    		</tbody></table>
    		<table class="<?php echo $this->params->get('table_classes', 'table table-striped'); ?>"><?php echo $this->loadTemplate('tableheader'); ?><tbody>
    	<?php endif; ?>
    <?php endif; ?>

	<?php $column_index = 0; ?>

	<tr class="latestnews-item id-<?php echo $this->item->id; ?> catid-<?php echo $this->item->catid; ?><?php echo $this->isleading ? ' leading' : ''; ?><?php echo $extraclasses; ?>">

		<?php if ($this->params->get('allow_edit', 0) && (int)Factory::getUser()->get('id') > 0) : ?>
			<td class="col_edit">
				<?php if (isset($this->item->link_edit)) : ?>
        			<?php if ($this->item->checked_out > 0 && $this->item->checked_out != Factory::getUser()->get('id')) : ?>
        				<?php $checkoutUser = Factory::getUser($this->item->checked_out); ?>
        				<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
        			<?php else : ?>
        				<a href="<?php echo $this->item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
        			<?php endif; ?>
        		<?php endif; ?>
    		</td>
		<?php endif; ?>

		<?php if ($this->details_bh) : ?>
			<?php foreach ($this->details_bh as $index => $detail) : ?>
				<td class="col_detail_<?php echo $column_index; ?>" data-title="<?php echo htmlspecialchars($this->details_names_bh[$index]); ?>">
					<?php echo Helper::getDetail($index, $detail, $this->isleading, $this->params, $this->item, null, 'com_k2'); ?>
				</td>

				<?php $column_index++; ?>

			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ($this->params->get('leading_head_type', 'none') !== 'none' || $this->params->get('head_type', 'none') !== 'none') : ?>
			<td class="col_head">
				<?php if ($this->show_image) : ?>
					<?php if ($this->item->imagetag) : ?>
						<div class="newshead picturetype<?php echo $css_hover_picture; ?>">

							<?php if ($this->item->imagetag) : ?>
								<div class="picture">
									<div class="innerpicture">

										<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
											<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, '', '', true, $this->bootstrap_version); ?>
										<?php endif; ?>

											<?php echo $this->item->imagetag; ?>

										<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
											</a>
										<?php endif; ?>

									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php elseif ($this->show_calendar) : ?>
					<?php if ($this->item->calendar_date) : ?>
						<div class="newshead calendartype">
							<?php if ($this->item->calendar_date) : ?>
								<div class="calendar <?php echo $this->params->get('cal_bg', '') != '' ? 'image' : 'noimage'; ?>">
									<?php $date_params = CalendarHelper::getCalendarBlockData($this->params, $this->item->calendar_date, true); ?>
									<?php foreach ($date_params as $counter => $date_array) : ?>
										<?php if (!empty($date_array)) : ?>
											<span class="position<?php echo ($counter + 1); ?> <?php echo key($date_array); ?>"><?php echo $date_array[key($date_array)]; ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		<?php endif; ?>

		<?php if ($this->details) : ?>
			<?php foreach ($this->details as $index => $detail) : ?>
				<td class="col_detail_<?php echo $column_index; ?>" data-title="<?php echo htmlspecialchars($this->details_names[$index]); ?>">
					<?php echo Helper::getDetail($index, $detail, $this->isleading, $this->params, $this->item, null, 'com_k2'); ?>
				</td>

				<?php $column_index++; ?>

			<?php endforeach; ?>
		<?php endif; ?>

	</tr>

<?php endforeach; ?>

</tbody></table>
