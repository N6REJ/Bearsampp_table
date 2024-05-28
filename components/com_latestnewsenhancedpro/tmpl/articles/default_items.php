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
use Joomla\CMS\Layout\FileLayout;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\CalendarHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Utilities as SYWUtilities;

// line break after leading items

$this->addLineBreakAfterLeading = $this->params->get('leading_line_break', 0);

// headings

$this->showHeadings = $this->params->get('s_headings', 0);
$this->showSubHeadings = false;
$this->monthFormat = $this->params->get('heading_m_format', 'F');

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

$this->extrareadmoreclass = trim($this->params->get('readmore_classes', ''));
if ($this->params->get('readmore_style', 'default') == 'bootstrap' && $this->bootstrap_version > 0) {
    $this->extrareadmoreclass .= ' btn';
    $this->extrareadmoreclass .= ' '.SYWUtilities::getBootstrapProperty($this->params->get('readmore_type', 'btn-default'), $this->bootstrap_version);
    $this->extrareadmoreclass = trim($this->extrareadmoreclass);
    if ($this->params->get('readmore_size', 'default') !== 'default') {
    	$this->extrareadmoreclass .= ' '.SYWUtilities::getBootstrapProperty('btn-'.$this->params->get('readmore_size', 'default'), $this->bootstrap_version);
    }
}

// category

$this->pos_category_first = false;
$this->pos_category_over_picture = false;
$this->pos_category_before_title = false;
$this->pos_category_last = false;
$category_positions = $this->params->get('pos_cat', '');
if (is_array($category_positions)) {
	foreach ($category_positions as $choice) {
		switch ($choice) {
			case 'first' : $this->pos_category_first = true; break;
			case 'picture' : $this->pos_category_over_picture = true; break;
			case 'title' : $this->pos_category_before_title = true; break;
			case 'last' : $this->pos_category_last = true; break;
		}
	}
}

$this->extracatclass = trim($this->params->get('cat_classes', ''));
if ($this->params->get('cat_link_style', 'default') == 'bootstrap' && $this->bootstrap_version > 0 && $this->params->get('link_cat_to', 'none') !== 'none') {
    $this->extracatclass .= ' btn';
    $this->extracatclass .= ' '.SYWUtilities::getBootstrapProperty($this->params->get('cat_link_type', 'btn-default'), $this->bootstrap_version);
    $this->extracatclass = trim($this->extracatclass);
    if ($this->params->get('cat_link_size', 'default') !== 'default') {
    	$this->extracatclass .= ' '.SYWUtilities::getBootstrapProperty('btn-'.$this->params->get('cat_link_size', 'default'), $this->bootstrap_version);
    }
}

//

ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
?>
<?php $this->item_count = Factory::getApplication()->getUserState('global.ajaxlist.itemcount', 0); ?>
<?php foreach ($this->items as $i => $this->item) : ?>

	<?php if ($this->item_count < $this->params->get('leading_count', 0)) : ?>
		<?php $this->isleading = true; ?>
		<?php $this->head_width = $this->params->get('leading_head_w', '128'); ?>
		<?php $this->head_height = $this->params->get('leading_head_h', '128'); ?>
		<?php $this->show_calendar = $this->params->get('leading_head_type', 'none') == 'calendar' || substr($this->params->get('leading_head_type', 'none'), 0, strlen('jfield:calendar')) === 'jfield:calendar' ? true : false; ?>
		<?php $this->show_icon = substr($this->params->get('leading_head_type', 'none'), 0, strlen('jfield:sywicon')) === 'jfield:sywicon' ? true : false; ?>
		<?php
            $this->show_video = false;
            if (($this->video_type = Helper::isHeadVideo($this->params->get('leading_head_type', 'none'))) !== false) {
                $this->show_video = true;
            }
		?>
		<?php $this->show_image = (!$this->show_calendar && !$this->show_icon && !$this->show_video && $this->params->get('leading_head_type', 'none') != 'none') ? true : false ?>
		<?php $this->keep_head_space = $this->params->get('leading_keep_head_space', 1); ?>
		<?php $this->title_tag = $this->params->get('leading_title_tag', 4); ?>
		<?php $this->title_class = $this->params->get('leading_title_class', ''); ?>
		<?php $this->text_align = $this->params->get('leading_text_align', 'r'); ?>
	<?php else : ?>
		<?php $this->isleading = false; ?>
		<?php $this->head_width = $this->params->get('head_w', '64'); ?>
		<?php $this->head_height = $this->params->get('head_h', '64'); ?>
		<?php $this->show_calendar = $this->params->get('head_type', 'none') == 'calendar' || substr($this->params->get('head_type', 'none'), 0, strlen('jfield:calendar')) === 'jfield:calendar' ? true : false; ?>
		<?php $this->show_icon = substr($this->params->get('head_type', 'none'), 0, strlen('jfield:sywicon')) === 'jfield:sywicon' ? true : false; ?>
		<?php
    		$this->show_video = false;
    		if (($this->video_type = Helper::isHeadVideo($this->params->get('head_type', 'none'))) !== false) {
    		    $this->show_video = true;
    		}
		?>
		<?php $this->show_image = (!$this->show_calendar && !$this->show_icon && !$this->show_video && $this->params->get('head_type', 'none') != 'none') ? true : false ?>
		<?php $this->keep_head_space = $this->params->get('keep_head_space', 1); ?>
		<?php $this->title_tag = $this->params->get('title_tag', 4); ?>
		<?php $this->title_class = $this->params->get('title_class', ''); ?>
		<?php $this->text_align = $this->params->get('text_align', 'r'); ?>
	<?php endif; ?>

	<?php $this->item_count++; ?>

	<?php
		$link_label_item = '';
		if ($this->item->link && ($this->add_readmore || $this->append_readmore)) {
			if ($this->item->authorized) {
				$link_label_item = trim($this->params->get('link_lbl', '')) != '' ? $this->params->get('link_lbl', '') : $this->item->linktitle; //JText::_('COM_LATESTNEWSENHANCEDPRO_READMORE_LABEL');
			} else {
				$link_label_item = trim($this->params->get('unauthorized_link_lbl', '')) != '' ? $this->params->get('unauthorized_link_lbl', '') : $this->item->linktitle; //JText::_('COM_LATESTNEWSENHANCEDPRO_UNAUTHORIZEDREADMORE_LABEL');
			}

// 			if (strpos($this->item->linktitle, rtrim($this->item->title, '.')) === false) {
// 				$link_label_item = $this->item->linktitle;
// 			}
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

		// not possible if load more (except if even number of items to add and even initial count)
		//$extraclasses .= ($i % 2) ? " even" : " odd";

		if ($this->item->state == 0) {
			$extraclasses .= ' unpublished';
		}

		if ($this->show_image || $this->show_calendar || $this->show_icon || $this->show_video) {
			switch ($this->text_align) {
				case 'l' : $extraclasses .= ' head_right'; break;
				case 'r' : $extraclasses .= ' head_left'; break;
				case 't' : $extraclasses .= ' text_top'; break;
				case 'b' : $extraclasses .= ' text_bottom'; break;
				// next cases: not possible if load more (except if even number of items to add and even initial count)
				case 'lr' : $extraclasses .= ($i % 2) ? ' head_left' : ' head_right'; break;
				case 'rl' : $extraclasses .= ($i % 2) ? ' head_right' : ' head_left'; break;
				case 'bt' : $extraclasses .= ($i % 2) ? ' text_top' : ' text_bottom'; break;
				case 'tb' : $extraclasses .= ($i % 2) ? ' text_bottom' : ' text_top'; break;
			}
		}

		$css_hover_picture = '';
		$css_hover_icon = '';
		if ($this->item->link && $this->link_head && $this->item->cropped) {
			if ($this->show_image && $this->params->get('hover_effect_pic', 'none') != 'none') {
				$css_hover_picture = ' hvr-'.$this->params->get('hover_effect_pic', 'none');
			}
			if ($this->show_icon && $this->params->get('hover_effect_icon', 'none') != 'none') {
				$css_hover_icon = ' hvr-'.$this->params->get('hover_effect_icon', 'none');
			}
		}
	?>

	<?php $details = Helper::getDetails($this->params, 'over_head_', 'information_blocks', $this->isleading); ?>
	<?php $info_block_over_head = Helper::getInfoBlock($details, $this->params, $this->item); ?>

	<?php $details = Helper::getDetails($this->params, 'before_title_', 'information_blocks', $this->isleading); ?>
	<?php $info_block_before_title = Helper::getInfoBlock($details, $this->params, $this->item); ?>

	<?php $details = Helper::getDetails($this->params, 'before_', 'information_blocks', $this->isleading); ?>
	<?php $info_block_before = Helper::getInfoBlock($details, $this->params, $this->item); ?>

	<?php $details = Helper::getDetails($this->params, 'after_', 'information_blocks', $this->isleading); ?>
	<?php $info_block_after = Helper::getInfoBlock($details, $this->params, $this->item); ?>

	<?php

// 	   if ($this->addLineBreakAfterLeading) {
//         	$this->isPreviousLeading = false;
//         	if ($this->isleading) {
//         	    JFactory::getApplication()->setUserState('global.ajaxlist.ispreviousleading', true);
//         	} else {
//         	    $this->isPreviousLeading = JFactory::getApplication()->getUserState('global.ajaxlist.ispreviousleading', false);
//         	    JFactory::getApplication()->setUserState('global.ajaxlist.ispreviousleading', false);
//             }
//         }

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

	<?php if ($this->addLineBreakAfterLeading && $this->item_count == ($this->params->get('leading_count', 0) + 1) /*$this->isPreviousLeading*/) : ?>
		<hr class="linebreak" />
	<?php endif; ?>

	<?php if ($this->showHeadings && $this->heading && $this->heading != 'noshow') : ?>
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
	<?php endif; ?>

	<div class="latestnews-item id-<?php echo $this->item->id; ?> catid-<?php echo $this->item->catid; ?><?php echo $this->isleading ? ' leading' : ''; ?><?php echo $extraclasses; ?>">

		<?php if ($this->show_errors && !empty($this->item->error)) : ?>
			<?php foreach ($this->item->error as $error) :  ?>
				<?php Factory::getApplication()->enqueueMessage('id '.$this->item->id.': '.$error, 'error'); ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="news">
			<div class="innernews">

				<?php if ($this->pos_category_first) : ?>
					<?php // top-most category ?>
					<?php $this->extracatcontainerclass = ' first'; ?>
					<?php echo $this->loadTemplate('category'); ?>
				<?php endif; ?>

				<?php if ($this->show_image) : ?>
					<?php if ($this->item->imagetag || $this->keep_head_space) : ?>
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

									<?php if ($this->pos_category_over_picture) : ?>
										<?php // category over picture ?>
										<?php $this->extracatcontainerclass = ($this->extracatclass == '' ? ' nostyle' : ''); ?>
										<?php echo $this->loadTemplate('category'); ?>
									<?php endif; ?>

									<?php if (!empty($info_block_over_head)) : ?>
										<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
									<?php endif; ?>

								</div>
							<?php elseif ($this->keep_head_space) : ?>
								<div class="nopicture">

									<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
										<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, '', '', true, $this->bootstrap_version); ?>
									<?php endif; ?>

										<span></span>

									<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
										</a>
									<?php endif; ?>

									<?php if ($this->pos_category_over_picture) : ?>
										<?php // category over picture ?>
										<?php $this->extracatcontainerclass = ($this->extracatclass == '' ? ' nostyle' : ''); ?>
										<?php echo $this->loadTemplate('category'); ?>
									<?php endif; ?>

									<?php if (!empty($info_block_over_head)) : ?>
										<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
									<?php endif; ?>

								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php elseif ($this->show_calendar) : ?>
					<?php if ($this->item->calendar_date || $this->keep_head_space) : ?>
						<div class="newshead calendartype">
							<?php if ($this->item->calendar_date) : ?>
								<div class="calendar <?php echo $this->params->get('cal_bg', '') != '' ? 'image' : 'noimage'; ?>">
									<?php $date_params = CalendarHelper::getCalendarBlockData($this->params, $this->item->calendar_date); ?>
									<?php foreach ($date_params as $counter => $date_array) : ?>
										<?php if (!empty($date_array)) : ?>
											<span class="position<?php echo ($counter + 1); ?> <?php echo key($date_array); ?>"><?php echo $date_array[key($date_array)]; ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php elseif ($this->keep_head_space) : ?>
								<div class="nocalendar"></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php elseif ($this->show_icon) : ?>
					<?php if ($this->item->icon || $this->keep_head_space) : ?>
						<div class="newshead icontype<?php echo $css_hover_icon; ?>">
							<?php if ($this->item->icon) : ?>

								<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
									<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, '', '', true, $this->bootstrap_version); ?>
								<?php endif; ?>

									<div class="icon">
										<i class="<?php echo $this->item->icon; ?>"></i>
									</div>

								<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
									</a>
								<?php endif; ?>

							<?php elseif ($this->keep_head_space) : ?>

								<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
									<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, '', '', true, $this->bootstrap_version); ?>
								<?php endif; ?>

									<div class="noicon">
										<i class="SYWicon-fullscreen"></i>
									</div>

								<?php if ($this->item->link && $this->link_head && $this->item->cropped) : ?>
									</a>
								<?php endif; ?>

							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php elseif ($this->show_video) : ?>
					<?php if ($this->item->video || $this->item->imagetag || $this->keep_head_space) : ?>
						<div class="newshead videotype">

							<?php if ($this->item->video) : ?>
								<div class="video <?php echo (isset($item->video_type) ? $item->video_type : $this->video_type); ?>">
									<div class="innervideo">
        								<?php
                                            $layout = new FileLayout('heads.lnep_head_video_' . (isset($item->video_type) ? $item->video_type : $this->video_type));
                                			$data = array('item' => $this->item, 'width' => $this->head_width, 'height' => $this->head_height, 'prefix' => 'lnep_blog', 'show_errors' => $this->show_errors, 'bootstrap_version' => $this->bootstrap_version);
                                			echo $layout->render($data);
                                		?>
                                	</div>

									<?php if ($this->pos_category_over_picture) : ?>
										<?php // category over picture ?>
										<?php $this->extracatcontainerclass = ($this->extracatclass == '' ? ' nostyle' : ''); ?>
										<?php echo $this->loadTemplate('category'); ?>
									<?php endif; ?>

									<?php if (!empty($info_block_over_head)) : ?>
										<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
									<?php endif; ?>
    							</div>
							<?php elseif ($this->item->imagetag) : ?>
								<div class="video image">
									<div class="innerpicture">
										<?php echo $this->item->imagetag; ?>
									</div>

									<?php if ($this->pos_category_over_picture) : ?>
										<?php // category over picture ?>
										<?php $this->extracatcontainerclass = ($this->extracatclass == '' ? ' nostyle' : ''); ?>
										<?php echo $this->loadTemplate('category'); ?>
									<?php endif; ?>

									<?php if (!empty($info_block_over_head)) : ?>
										<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
									<?php endif; ?>
								</div>
							<?php elseif ($this->keep_head_space) : ?>
								<div class="novideo">
									<span></span>

									<?php if ($this->pos_category_over_picture) : ?>
										<?php // category over picture ?>
										<?php $this->extracatcontainerclass = ($this->extracatclass == '' ? ' nostyle' : ''); ?>
										<?php echo $this->loadTemplate('category'); ?>
									<?php endif; ?>

									<?php if (!empty($info_block_over_head)) : ?>
										<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="newsinfo">

					<?php if ($this->pos_category_before_title) : ?>
						<?php // category before title ?>
						<?php $this->extracatcontainerclass = ' title'; ?>
						<?php echo $this->loadTemplate('category'); ?>
					<?php endif; ?>

					<?php if (!empty($info_block_before_title)) : ?>
						<dl class="item_details before_title"><?php echo $info_block_before_title; ?></dl>
					<?php endif; ?>

					<?php if ($this->item->title) : ?>
						<h<?php echo $this->title_tag; ?> class="newstitle <?php echo $this->title_class; ?>">

						<?php if ($this->item->link && $this->link_title && $this->item->cropped) : ?>
							<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, '', '', true, $this->bootstrap_version); ?>
						<?php endif; ?>

							<span><?php echo $this->item->title; ?></span>

						<?php if ($this->item->link && $this->link_title && $this->item->cropped) : ?>
							</a>
						<?php endif; ?>

						<?php if (isset($this->item->link_edit)) : ?>
							<?php if ($this->item->checked_out > 0 && $this->item->checked_out != Factory::getUser()->get('id')) : ?>
								<?php $checkoutUser = Factory::getUser($this->item->checked_out); ?>
								<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
							<?php else : ?>
								<a href="<?php echo $this->item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
							<?php endif; ?>
						<?php endif; ?>

						</h<?php echo $this->title_tag; ?>>
					<?php else : ?>
						<h<?php echo $this->title_tag; ?> class="newstitle <?php echo $this->title_class; ?>">

						<?php if (isset($this->item->link_edit)) : ?>
							<?php if ($this->item->checked_out > 0 && $this->item->checked_out != Factory::getUser()->get('id')) : ?>
								<?php $checkoutUser = Factory::getUser($this->item->checked_out); ?>
								<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('COM_LATESTNEWSENHANCEDPRO_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
							<?php else : ?>
								<a href="<?php echo $this->item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
							<?php endif; ?>
						<?php endif; ?>

						</h<?php echo $this->title_tag; ?>>
					<?php endif; ?>

					<?php if (!empty($info_block_before)) : ?>
						<dl class="item_details before_text"><?php echo $info_block_before; ?></dl>
					<?php endif; ?>

					<?php if ($this->item->text) : ?>
						<div class="newsintro">
							<?php echo $this->item->text; ?>
							<?php if ($this->item->link && $this->append_readmore && $this->item->cropped && $link_label_item) : ?>
								<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, 'appended_link', '', true, $this->bootstrap_version); ?>
									<span><?php echo $link_label_item; ?></span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($info_block_after)) : ?>
						<dl class="item_details after_text"><?php echo $info_block_after; ?></dl>
					<?php endif; ?>

					<?php if ($this->item->link && $this->add_readmore && $this->item->cropped && $link_label_item) : ?>
						<p class="link<?php echo $this->params->get('readmore_align', 'default') !== 'default' ? ' '.$this->params->get('readmore_align', 'default') : ''; ?>">
							<?php echo Helper::getATag($this->item, $this->follow, $this->link_tooltip, $this->popup_width, $this->popup_height, $this->extrareadmoreclass, '', true, $this->bootstrap_version); ?>
								<span><?php echo $link_label_item; ?></span>
							</a>
						</p>
					<?php endif; ?>
				</div>

				<?php if ($this->pos_category_last) : ?>
					<?php // bottom-most category ?>
					<?php $this->extracatcontainerclass = ' last'; ?>
					<?php echo $this->loadTemplate('category'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
<?php Factory::getApplication()->setUserState('global.ajaxlist.itemcount', $this->item_count); ?>
<?php ob_get_flush(); ?>