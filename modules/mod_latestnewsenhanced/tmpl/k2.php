<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Registry\Registry;
use SYW\Library\Libraries as SYWLibraries;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Module\LatestNewsEnhanced\Site\Helper\CalendarHelper as LNECalendarHelper;
use SYW\Module\LatestNewsEnhanced\Site\Helper\K2Helper as LNEK2Helper;
use SYW\Module\LatestNewsEnhanced\Site\Helper\Helper as LNEHelper;

if ($load_bootstrap) {
    HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
}
?>
<?php if ($datasource != 'k2') : ?>
	<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-error', $bootstrap_version); ?>"><?php echo Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_WRONGLAYOUT'); ?></div>
<?php elseif (empty($list)) : ?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee nonews<?php echo $isMobile ? ' mobile' : ''; ?>"><div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-info', $bootstrap_version); ?>"><?php echo $nodata_message; ?></div></div>
<?php else : ?>
	<?php
		$categories = LNEK2Helper::getCategoryList($params, $list);
		$nbr_cat = count($categories);

		$current_catid = $list[0]->catid;
		$new_catid = true;

		$modal_needed = false;

		if ($remove_whitespaces) {
			ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
		}
	?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee newslist<?php echo $isMobile ? ' mobile' : ''; ?> <?php echo $alignment; ?>">
		<?php if ($animation) : ?>
			<?php if ($pagination && $pagination_position_type == 'title') : ?>
				<?php foreach ($module_title_array as $module_title_item) :  ?>
					<?php if ($module_title_item == 'pagination') : ?>
						<?php if (File::exists(dirname(__FILE__).'/pagination/'.$animation.'.php')) : ?>
							<?php include 'pagination/'.$animation.'.php'; ?>
						<?php endif; ?>
					<?php else : ?>
						<?php echo $module_title_item; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<div class="clearfix"></div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (trim($params->get('pretext', ''))) : ?>
			<div class="pretext">
				<?php if ($params->get('allow_plugins_prepost', 0)) : ?>
					<?php echo HTMLHelper::_('content.prepare', $params->get('pretext')); ?>
				<?php else : ?>
					<?php echo $params->get('pretext'); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($readall_link && $pos_readall == 'first') : ?>
			<div class="readalllink first<?php echo $extrareadallclass; ?>">
				<a href="<?php echo $readall_link; ?>"<?php echo $readall_additional_attributes; ?><?php echo $readall_isExternal ? ' target="_blank"' : ''; ?>><span><?php echo $readall_link_label ?></span></a>
			</div>
		<?php endif; ?>
		<?php if ($pos_category_first && $nbr_cat == 1 && $consolidate_category) : ?>
			<?php
				if ($list[0]->category_authorized) {
					$cat_label = empty($cat_link_text) ? $list[0]->category_title : $cat_link_text;
				} else {
					$cat_label = empty($unauthorized_cat_link_text) ? $list[0]->category_title : $unauthorized_cat_link_text;
				}

				if ($link_category) {
					$category_additional_attributes = '';
					if ($category_link_tooltip) {
						if ($extracategorylinkclass) {
							$extracategorylinkclass = ' '.$extracategorylinkclass;
						}
						$category_additional_attributes = ' title="'.$cat_label.'" class="hasTooltip'.$extracategorylinkclass.'"';
					} else {
						if ($extracategorylinkclass != '') {
							$category_additional_attributes = ' class="'.$extracategorylinkclass.'"';
						}
					}
				}
			?>
			<div class="onecatlink first<?php echo $extracategoryclass; ?>">
				<?php if ($link_category) : ?>
					<a href="<?php echo $list[0]->catlink; ?>"<?php echo $category_additional_attributes; ?>>
				<?php endif; ?>
					<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
					<?php if ($show_article_count) : ?>&nbsp;<span class="article_count <?php echo $article_count_classes; ?>"><?php echo $categories[$current_catid]->count; ?></span><?php endif; ?>
				<?php if ($link_category) : ?>
					</a>
				<?php endif; ?>
				<?php if ($show_category_description) : ?>
					<div class="category_description"><?php echo $categories[$current_catid]->description; ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($animation) : ?>
			<?php if ($pagination && ($pagination_position_type == 'above' || $pagination_position_type == 'around')) : ?>
				<?php if (File::exists(dirname(__FILE__).'/pagination/'.$animation.'.php')) : ?>
					<?php $pagination_position = $pagination_position_top; ?>
					<?php include 'pagination/'.$animation.'.php'; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<ul class="latestnews-items<?php echo ($use_leading && $leading_items_count > 0) ? ' altered' : '' ?>">
			<?php foreach ($list as $i => $item) : ?>
				<?php
					$extraclasses = ($i % 2) ? " even" : " odd";

					if ($show_image || $show_calendar) {
						switch ($text_align) {
							case 'l' : $extraclasses .= " head_right"; break;
							case 'r' : $extraclasses .= " head_left"; break;
							case 'lr' : $extraclasses .= ($i % 2) ? " head_left" : " head_right"; break;
							case 'rl' : $extraclasses .= ($i % 2) ? " head_right" : " head_left"; break;

							case 't' : $extraclasses .= " text_top"; break;
							case 'b' : $extraclasses .= " text_bottom"; break;
							case 'bt' : $extraclasses .= ($i % 2) ? " text_top" : " text_bottom"; break;
							case 'tb' : $extraclasses .= ($i % 2) ? " text_bottom" : " text_top"; break;
						}
					}

					if ($i > 0) {
						if ($current_catid != $item->catid) {
							$current_catid = $item->catid;
							$new_catid = true;
						} else {
							$new_catid = false;
						}
					}

					if (isset($item->linktarget) && $item->linktarget == 3) {
					    $modal_needed = true;
					}

					if ($item->category_authorized) {
						$cat_label = empty($cat_link_text) ? $item->category_title : $cat_link_text;
					} else {
						$cat_label = empty($unauthorized_cat_link_text) ? $item->category_title : $unauthorized_cat_link_text;
					}

					if ($link_category) {
						$category_additional_attributes = '';
						if ($category_link_tooltip) {
						if ($extracategorylinkclass) {
							$extracategorylinkclass = ' '.$extracategorylinkclass;
						}
							$category_additional_attributes = ' title="'.$cat_label.'" class="hasTooltip'.$extracategorylinkclass.'"';
						} else {
							if ($extracategorylinkclass != '') {
								$category_additional_attributes = ' class="'.$extracategorylinkclass.'"';
							}
						}
					}

					$link_label_item = '';
					if (($add_readmore || $append_readmore) && $item->link) {
						$link_label_item = $item->authorized ? $link_label : $unauthorized_link_label;
						if (empty($link_label_item)) {
							$link_label_item = $item->linktitle;
						}
					}

					$registry = new Registry();
					$registry->loadString($item->params);

					$registry_attribs = new Registry();

					// add params that will be useful and that need to correspond to article params

					$registry_attribs->set('show_hits', $registry->get('itemHits'));
					$registry_attribs->set('show_vote', $registry->get('itemRating'));
					$registry_attribs->set('show_author', $registry->get('itemCategory'));
					$registry_attribs->set('show_category', $registry->get('itemHits'));
					$registry_attribs->set('show_tags', $registry->get('itemTags'));
					$registry_attribs->set('show_email_icon', $registry->get('itemEmailButton'));

					$details = LNEHelper::getDetails($params, 'over_head_', 'information_blocks');
					$info_block_over_head = LNEHelper::getInfoBlock($details, $params, $item, $registry_attribs);

					$details = LNEHelper::getDetails($params, 'before_title_', 'information_blocks');
					$info_block_before_title = LNEHelper::getInfoBlock($details, $params, $item, $registry_attribs);

					$details = LNEHelper::getDetails($params, 'after_title_', 'information_blocks');
					$info_block_after_title = LNEHelper::getInfoBlock($details, $params, $item, $registry_attribs);

					$details = LNEHelper::getDetails($params, 'before_', 'information_blocks');
					$info_block_before_text = LNEHelper::getInfoBlock($details, $params, $item, $registry_attribs);

					$details = LNEHelper::getDetails($params, 'after_', 'information_blocks');
					$info_block_after_text = LNEHelper::getInfoBlock($details, $params, $item, $registry_attribs);

					$css_item = '';

					// check if the link is the same of the article activaly shown
					if ($app->input->get('option') === 'com_k2' && $app->input->get('view') === 'item') {
						$current_id = $app->input->getInt('id');
						if ($current_id == $item->id) {
						    $css_item .= ' active';
						}
					}

					if ($use_leading && $leading_items_count > 0) {
						if ($i < $leading_items_count) {
						    $css_item .= ' full';
						} else {
						    $css_item .= ' downgraded';
						}
					}

					if ($show_image && $shadow_width_pic > 0) {
						if (!($use_leading && $leading_items_count > 0 && $i >= $leading_items_count && $remove_head)) {
							switch ($shadow_type_pic) {
							    case 'v' : $css_item .= ' shadow vshapeleft vshaperight'; break;
							    case 'vl' : $css_item .= ' shadow vshapeleft'; break;
							    case 'vr' : $css_item .= ' shadow vshaperight'; break;
							    default : $css_item .= ' shadow simple'; break;
							}
						}
					}

					$css_hover = '';
					if ($link_head && $item->link) {
						if ($show_image && $hover_effect != 'none') {
						    $css_hover = ' '.$hover_effect;
						}
					}

					if ($item->featured) {
					    $css_item .= ' featured';
					}

					if (!$item->category_authorized) {
					    $css_item .= ' cat-unauthorized';
					}

					if (!$item->authorized) {
					    $css_item .= ' unauthorized';
					}

					if ($item->state == 0) {
						$css_item .= ' unpublished';
					}
				?>
				<li class="latestnews-item id-<?php echo $item->id; ?> catid-<?php echo $item->catid; ?><?php echo $css_item; ?>">
					<?php if ($show_errors && !empty($item->error)) : ?>
						<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-error', $bootstrap_version); ?>">
    						<span><?php echo 'id '.$item->id.':'; ?></span>
    						<ul>
    						<?php foreach ($item->error as $error) : ?>
    	  						<li><?php echo $error; ?></li>
    						<?php endforeach; ?>
    						</ul>
    					</div>
					<?php endif; ?>
					<div class="news<?php echo $extraclasses ?>">
						<div class="innernews">
							<?php if ($pos_category_first && (($nbr_cat > 1 && $consolidate_category && $new_catid) || !$consolidate_category)) : ?>
								<div class="catlink<?php echo $extracategoryclass; ?>">
									<?php if ($link_category) : ?>
										<a href="<?php echo $item->catlink; ?>"<?php echo $category_additional_attributes; ?>>
									<?php endif; ?>
										<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
										<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count <?php echo $article_count_classes; ?>"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>
									<?php if ($link_category) : ?>
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ($pos_category_first && ($nbr_cat > 1 && $consolidate_category && !$new_catid)) : ?>
								<div class="catlink emptyspace<?php echo $extracategoryclass; ?>">
									<?php if ($link_category) : ?>
										<a href="<?php echo $item->catlink; ?>"<?php echo $category_additional_attributes; ?>>
									<?php endif; ?>
										<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
									<?php if ($link_category) : ?>
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ($title_before_head) : ?>
								<div class="newsinfooverhead">
									<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block_before_title)) : ?>
											<dl class="item_details before_title"><?php echo $info_block_before_title; ?></dl>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($show_title) : ?>
										<h<?php echo $title_html_tag; ?> class="newstitle<?php echo $title_class; ?>">
										<?php if ($link_title) : ?>
											<?php if ($item->link) : ?>
												<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
													<span><?php echo $item->title; ?></span>
												</a>
											<?php else : ?>
												<span><?php echo $item->title; ?></span>
											<?php endif; ?>
										<?php else : ?>
											<span><?php echo $item->title; ?></span>
										<?php endif; ?>
										<?php if (isset($item->link_edit)) : ?>
											<?php if ($item->checked_out > 0 && $item->checked_out != Factory::getUser()->get('id')) : ?>
												<?php $checkoutUser = Factory::getUser($item->checked_out); ?>
												<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
											<?php else : ?>
												<?php $attribs_popup = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height; ?>
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>" onclick="window.open(this.href, 'targetWindow', '<?php echo $attribs_popup; ?>'); return false;"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
										<?php endif; ?>
										</h<?php echo $title_html_tag; ?>>
									<?php else : ?>
										<?php if (isset($item->link_edit)) : ?>
											<h<?php echo $title_html_tag; ?> class="newstitle<?php echo $title_class; ?>">
											<?php if ($item->checked_out > 0 && $item->checked_out != Factory::getUser()->get('id')) : ?>
												<?php $checkoutUser = Factory::getUser($item->checked_out); ?>
												<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
											<?php else : ?>
												<?php $attribs_popup = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height; ?>
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>" onclick="window.open(this.href, 'targetWindow', '<?php echo $attribs_popup; ?>'); return false;"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
											</h<?php echo $title_html_tag; ?>>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block_after_title)) : ?>
    										<dl class="item_details after_title"><?php echo $info_block_after_title; ?></dl>
    									<?php endif; ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ($use_leading && $remove_head && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
							<?php else : ?>
								<?php if ($show_image) : ?>
									<?php if ($item->imagetag || $keep_space) : ?>
										<div class="newshead picturetype<?php echo $css_hover; ?>">
											<?php if ($item->imagetag) : ?>
												<div class="picture">
											<?php elseif ($keep_space) : ?>
												<div class="nopicture">
											<?php endif; ?>
												<?php if ($item->imagetag) : ?>
													<div class="innerpicture">
														<?php if ($link_head && $item->link) : ?>
															<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
																<?php echo $item->imagetag; ?>
															</a>
														<?php else : ?>
															<?php echo $item->imagetag; ?>
														<?php endif; ?>
													</div>
												<?php elseif ($keep_space) : ?>
													<?php if ($link_head && $item->link) : ?>
														<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
															<span></span>
														</a>
													<?php else : ?>
														<span></span>
													<?php endif; ?>
												<?php endif; ?>
												<?php if ($pos_category_over_picture && (($consolidate_category && $new_catid) || !$consolidate_category)) : ?>
													<div class="catlink<?php echo $extracategoryclass; ?>">
														<?php if ($link_category) : ?>
															<a href="<?php echo $item->catlink; ?>"<?php echo $category_additional_attributes; ?>>
														<?php else : ?>
															<div>
														<?php endif; ?>
															<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
															<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count <?php echo SYWUtilities::getBootstrapProperty('label label-info', $bootstrap_version); ?>"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>
														<?php if ($link_category) : ?>
															</a>
														<?php else : ?>
															</div>
														<?php endif; ?>
													</div>
												<?php endif; ?>
												<?php if (!empty($info_block_over_head)) : ?>
													<dl class="item_details over_head"><?php echo $info_block_over_head; ?></dl>
												<?php endif; ?>
												</div>
										</div>
									<?php endif; ?>
								<?php elseif ($show_calendar) : ?>
									<?php if ($item->calendar_date || $keep_space) : ?>
										<div class="newshead calendartype">
											<?php if ($item->calendar_date) : ?>
												<div class="calendar <?php echo $extracalendarclass; ?>">
													<?php $date_params = LNECalendarHelper::getCalendarBlockData($params, $item->calendar_date, true); ?>
													<?php foreach ($date_params as $counter => $date_array) : ?>
														<?php if (!empty($date_array)) : ?>
															<span class="position<?php echo ($counter + 1); ?> <?php echo key($date_array); ?>"><?php echo $date_array[key($date_array)]; ?></span>
														<?php endif; ?>
													<?php endforeach; ?>
												</div>
											<?php elseif ($keep_space) : ?>
												<div class="nocalendar"></div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($show_image && empty($item->imagetag) && !$keep_space) : ?>
								<div class="newsinfo noimagespace">
							<?php else : ?>
								<div class="newsinfo">
							<?php endif; ?>
								<?php if ($pos_category_before_title && (($consolidate_category && $new_catid) || !$consolidate_category)) : ?>
									<p class="catlink<?php echo $extracategoryclass; ?>">
										<?php if ($link_category) : ?>
											<a href="<?php echo $item->catlink; ?>"<?php echo $category_additional_attributes; ?>>
										<?php endif; ?>
											<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
											<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count <?php echo $article_count_classes; ?>"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>
										<?php if ($link_category) : ?>
											</a>
										<?php endif; ?>
									</p>
								<?php endif; ?>
								<?php if (!$title_before_head) : ?>
									<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block_before_title)) : ?>
											<dl class="item_details before_title"><?php echo $info_block_before_title; ?></dl>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($show_title) : ?>
										<h<?php echo $title_html_tag; ?> class="newstitle<?php echo $title_class; ?>">
										<?php if ($link_title) : ?>
											<?php if ($item->link) : ?>
												<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
													<span><?php echo $item->title; ?></span>
												</a>
											<?php else : ?>
												<span><?php echo $item->title; ?></span>
											<?php endif; ?>
										<?php else : ?>
											<span><?php echo $item->title; ?></span>
										<?php endif; ?>
										<?php if (isset($item->link_edit)) : ?>
											<?php if ($item->checked_out > 0 && $item->checked_out != Factory::getUser()->get('id')) : ?>
												<?php $checkoutUser = Factory::getUser($item->checked_out); ?>
												<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
											<?php else : ?>
												<?php $attribs_popup = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height; ?>
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>" onclick="window.open(this.href, 'targetWindow', '<?php echo $attribs_popup; ?>'); return false;"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
										<?php endif; ?>
										</h<?php echo $title_html_tag; ?>>
									<?php else : ?>
										<?php if (isset($item->link_edit)) : ?>
											<h<?php echo $title_html_tag; ?> class="newstitle<?php echo $title_class; ?>">
											<?php if ($item->checked_out > 0 && $item->checked_out != Factory::getUser()->get('id')) : ?>
												<?php $checkoutUser = Factory::getUser($item->checked_out); ?>
												<span class="checked_out hasTooltip" title="<?php echo Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_CHECKED_OUT_BY', $checkoutUser->name); ?>"><i class="SYWicon-lock"></i></span>
											<?php else : ?>
												<?php $attribs_popup = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height; ?>
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>" onclick="window.open(this.href, 'targetWindow', '<?php echo $attribs_popup; ?>'); return false;"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
											</h<?php echo $title_html_tag; ?>>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block_after_title)) : ?>
    										<dl class="item_details after_title"><?php echo $info_block_after_title; ?></dl>
    									<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
								<?php else : ?>
									<?php if (!empty($info_block_before_text)) : ?>
										<dl class="item_details before_text"><?php echo $info_block_before_text; ?></dl>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($use_leading && $remove_text && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
								<?php else : ?>
									<?php if ($item->text) : ?>
										<div class="newsintro">
											<?php echo $item->text; ?>
											<?php if ($append_readmore && $link_label_item && $item->cropped) : ?>
												<?php if ($item->link) : ?>
													<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height, 'link_append', '', true); ?>
														<span><?php echo $link_label_item; ?></span>
													</a>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($use_leading && $remove_details && $leading_items_count > 0 && $i >= $leading_items_count) : ?>
								<?php else : ?>
									<?php if (!empty($info_block_after_text)) : ?>
										<dl class="item_details after_text"><?php echo $info_block_after_text; ?></dl>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($add_readmore && $link_label_item && $item->cropped) : ?>
									<?php if ($item->link) : ?>
										<p class="link<?php echo $extrareadmoreclass; ?>">
											<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height, $extrareadmorelinkclass, '', true); ?>
												<span><?php echo $link_label_item; ?></span>
											</a>
										</p>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($pos_category_last && (($nbr_cat > 1 && $consolidate_category && $new_catid) || !$consolidate_category)) : ?>
									<p class="catlink<?php echo $extracategoryclass; ?>">
										<?php if ($link_category) : ?>
											<a href="<?php echo $item->catlink; ?>"<?php echo $category_additional_attributes; ?>>
										<?php endif; ?>
											<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
											<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count <?php echo $article_count_classes; ?>"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>
										<?php if ($link_category) : ?>
											</a>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($animation) : ?>
			<?php if ($pagination && ($pagination_position_type == 'below' || $pagination_position_type == 'around')) : ?>
				<?php if (File::exists(dirname(__FILE__).'/pagination/'.$animation.'.php')) : ?>
					<div class="clearfix"></div>
					<?php $pagination_position = $pagination_position_bottom; ?>
					<?php include 'pagination/'.$animation.'.php'; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($params->get('link_to', 'item') == 'item' && $params->get('link_target', 'default') == 'inline') : ?>
			<?php foreach ($list as $i => $item) : ?>
				<?php
					$registry = new Registry();
					$registry->loadString($item->params);

					$registry_attribs = new Registry();

					// add params that will be useful and that need to correspond to article params

					$registry_attribs->set('show_hits', $registry->get('itemHits'));
					$registry_attribs->set('show_vote', $registry->get('itemRating'));
					$registry_attribs->set('show_author', $registry->get('itemCategory'));
					$registry_attribs->set('show_category', $registry->get('itemHits'));
					$registry_attribs->set('show_tags', $registry->get('itemTags'));
					$registry_attribs->set('show_email_icon', $registry->get('itemEmailButton'));

					$inline_details = LNEHelper::getDetails($params, 'inline_', 'information_blocks');
					$inline_info_block = LNEHelper::getInfoBlock($inline_details, $params, $item, $registry_attribs);
				?>
				<div id="inline-item-<?php echo $i; ?>" class="inline-item" style="display: none;">
					<?php if ($show_title) : ?>
						<h<?php echo $title_html_tag; ?> class="inline-title">
							<span><?php echo $item->linktitle; ?></span>
						</h<?php echo $title_html_tag; ?>>
					<?php endif; ?>
					<?php if (!empty($inline_info_block)) : ?>
						<dl class="inline-item-details"><?php echo $inline_info_block; ?></dl>
					<?php endif; ?>
					<div class="inline-text"><?php echo $item->inlinetext; ?></div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ($pos_category_last && $nbr_cat == 1 && $consolidate_category) : ?>
			<?php
				if ($list[0]->category_authorized) {
					$cat_label = empty($cat_link_text) ? $list[0]->category_title : $cat_link_text;
				} else {
					$cat_label = empty($unauthorized_cat_link_text) ? $list[0]->category_title : $unauthorized_cat_link_text;
				}

				if ($link_category) {
					$category_additional_attributes = '';
					if ($category_link_tooltip) {
						if ($extracategorylinkclass) {
							$extracategorylinkclass = ' '.$extracategorylinkclass;
						}
						$category_additional_attributes = ' title="'.$cat_label.'" class="hasTooltip'.$extracategorylinkclass.'"';
					} else {
						if ($extracategorylinkclass != '') {
							$category_additional_attributes = ' class="'.$extracategorylinkclass.'"';
						}
					}
				}
			?>
			<div class="onecatlink last<?php echo $extracategoryclass; ?>">
				<?php if ($link_category) : ?>
					<a href="<?php echo $list[0]->catlink; ?>"<?php echo $category_additional_attributes; ?>>
				<?php endif; ?>
					<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
					<?php if ($show_article_count) : ?>&nbsp;<span class="article_count <?php echo $article_count_classes; ?>"><?php echo $categories[$current_catid]->count; ?></span><?php endif; ?>
				<?php if ($link_category) : ?>
					</a>
				<?php endif; ?>
				<?php if ($show_category_description) : ?>
					<div class="category_description"><?php echo $categories[$current_catid]->description; ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($readall_link && $pos_readall == 'last') : ?>
			<div class="readalllink last<?php echo $extrareadallclass; ?>">
				<a href="<?php echo $readall_link; ?>"<?php echo $readall_additional_attributes; ?><?php echo $readall_isExternal ? ' target="_blank"' : ''; ?>><span><?php echo $readall_link_label ?></span></a>
			</div>
		<?php endif; ?>

		<?php if (trim($params->get('posttext', ''))) : ?>
			<div class="posttext">
				<?php if ($params->get('allow_plugins_prepost', 0)) : ?>
					<?php echo HTMLHelper::_('content.prepare', $params->get('posttext')); ?>
				<?php else : ?>
					<?php echo $params->get('posttext'); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>

	<?php if ($animation) : ?><div id="lnee_<?php echo $class_suffix; ?>_loader"><img src="<?php echo $loader_path ?>" alt="<?php echo Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_LOADER_ALT'); ?>" /></div><?php endif; ?>

	<?php
        if ($modal_needed) {
        	if ($bootstrap_version == 0) {
        		SYWLibraries::loadPureModal($load_remotely);
        	}

            $layout = new FileLayout('content.lnepmodal', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component

    		$data = array('selector' => 'lnepmodal_'.$module->id, 'width' => $popup_width, 'height' => $popup_height);
            $data['bootstrap_version'] = $bootstrap_version;
            $data['load_bootstrap'] = $load_bootstrap;

            echo $layout->render($data);
        }
    ?>
	<?php if ($remove_whitespaces) : ?>
		<?php ob_get_flush(); ?>
	<?php endif; ?>
<?php endif; ?>