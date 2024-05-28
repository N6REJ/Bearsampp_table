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
use SYW\Library\Utilities as SYWUtilities;
use SYW\Module\LatestNewsEnhanced\Site\Helper\CalendarHelper as LNECalendarHelper;
use SYW\Module\LatestNewsEnhanced\Site\Helper\Helper as LNEHelper;

if ($load_bootstrap) {
	HtmlHelper::_('bootstrap.tooltip', '.hasTooltip');
}
?>
<?php if ($datasource != 'newsfeeds') : ?>
	<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-error', $bootstrap_version); ?>"><?php echo Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_WRONGLAYOUT'); ?></div>
<?php elseif (empty($list)) : ?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee nonews<?php echo $isMobile ? ' mobile' : ''; ?>"><div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-info', $bootstrap_version); ?>"><?php echo $nodata_message; ?></div></div>
<?php else : ?>
	<?php
	// pagination
		$file_pagination = '';
		if ($animation && !empty($pagination)) {
			// in case pagination from module has template overrides
			$template = Factory::getApplication()->getTemplate();
			if (File::exists(JPATH_THEMES.'/'.$template.'/html/mod_latestnewsenhanced/pagination/'.$animation.'.php')) {
				$file_pagination = JPATH_THEMES.'/'.$template.'/html/mod_latestnewsenhanced/pagination/'.$animation.'.php';
			} else if (File::exists(JPATH_SITE.'/modules/mod_latestnewsenhanced/tmpl/pagination/'.$animation.'.php')) {
				$file_pagination = JPATH_SITE.'/modules/mod_latestnewsenhanced/tmpl/pagination/'.$animation.'.php';
			}
		}

		if ($remove_whitespaces) {
			ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
		}
	?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee newslist<?php echo $isMobile ? ' mobile' : ''; ?> <?php echo $alignment; ?>">
		<?php if ($animation) : ?>
			<?php if ($pagination && $pagination_position_type == 'title') : ?>
				<?php foreach ($module_title_array as $module_title_item) :  ?>
					<?php if ($module_title_item == 'pagination') : ?>
						<?php if ($file_pagination) : ?>
							<?php include $file_pagination; ?>
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
		<?php if ($animation) : ?>
			<?php if ($pagination && ($pagination_position_type == 'above' || $pagination_position_type == 'around')) : ?>
				<?php if ($file_pagination) : ?>
					<?php $pagination_position = $pagination_position_top; ?>
					<?php include $file_pagination; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<ul class="latestnews-items<?php echo ($use_leading && $leading_items_count > 0) ? ' altered' : '' ?>">
			<?php foreach ($list as $i => $item) : ?>
				<?php
					$extraclasses = ($i % 2) ? " even" : " odd";

					if ($show_image || $show_calendar || $show_icon) {
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

					$link_label_item = '';
					if (($add_readmore || $append_readmore) && $item->link) {
						$link_label_item = $link_label;
						if (empty($link_label_item)) {
							$link_label_item = $item->linktitle;
						}
					}

					$details = LNEHelper::getDetails($params, 'over_head_', 'information_blocks');
					$info_block_over_head = LNEHelper::getInfoBlock($details, $params, $item);
					
					$details = LNEHelper::getDetails($params, 'before_title_', 'information_blocks');
					$info_block_before_title = LNEHelper::getInfoBlock($details, $params, $item);

					$details = LNEHelper::getDetails($params, 'after_title_', 'information_blocks');
					$info_block_after_title = LNEHelper::getInfoBlock($details, $params, $item);

					$details = LNEHelper::getDetails($params, 'before_', 'information_blocks');
					$info_block_before_text = LNEHelper::getInfoBlock($details, $params, $item);

					$details = LNEHelper::getDetails($params, 'after_', 'information_blocks');
					$info_block_after_text = LNEHelper::getInfoBlock($details, $params, $item);

					$css_item = '';

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

					if ($show_icon && $shadow_width_icon > 0) {
						if (!($use_leading && $leading_items_count > 0 && $i >= $leading_items_count && $remove_head)) {
						    $css_item .= ' shadow simple';
						}
					}

					$css_hover = '';
					if ($link_head && $item->link) {
						if ($show_image && $hover_effect != 'none') {
						    $css_hover = ' '.$hover_effect;
						} elseif ($show_icon && $hover_effect_icon != 'none') {
						    $css_hover = ' '.$hover_effect_icon;
						}
					}
				?>
				<li class="latestnews-item<?php echo $css_item; ?>">
					<?php if ($show_errors && !empty($item->error)) : ?>
						<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-error', $bootstrap_version); ?>">
    						<ul>
    						<?php foreach ($item->error as $error) : ?>
    	  						<li><?php echo $error; ?></li>
    						<?php endforeach; ?>
    						</ul>
    					</div>
					<?php endif; ?>
					<div class="news<?php echo $extraclasses ?>">
						<div class="innernews">
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
										</h<?php echo $title_html_tag; ?>>
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
														<?php if ($link_head&& $item->link) : ?>
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
													<?php $date_params = LNECalendarHelper::getCalendarBlockData($params, $item->calendar_date); ?>
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
								<?php elseif ($show_icon) : ?>
									<?php if ($item->icon || $keep_space) : ?>
										<div class="newshead icontype<?php echo $css_hover; ?>">
											<?php if ($item->icon) : ?>
												<?php if ($link_head && $item->link) : ?>
													<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
												<?php endif; ?>
													<div class="icon">
														<i class="<?php echo $item->icon; ?>"></i>
													</div>
												<?php if ($link_head && $item->link) : ?>
													</a>
												<?php endif; ?>
											<?php elseif ($keep_space) : ?>
												<?php if ($link_head && $item->link) : ?>
													<?php echo LNEHelper::getHtmlATag($module, $item, $follow, $link_tooltip, $popup_width, $popup_height); ?>
												<?php endif; ?>
													<div class="noicon">
														<i class="SYWicon-fullscreen"></i>
													</div>
												<?php if ($link_head && $item->link) : ?>
													</a>
												<?php endif; ?>
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
										</h<?php echo $title_html_tag; ?>>
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
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($animation) : ?>
			<?php if ($pagination && ($pagination_position_type == 'below' || $pagination_position_type == 'around')) : ?>
				<?php if ($file_pagination) : ?>
					<div class="clearfix"></div>
					<?php $pagination_position = $pagination_position_bottom; ?>
					<?php include $file_pagination; ?>
				<?php endif; ?>
			<?php endif; ?>
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

	<?php if ($remove_whitespaces) : ?>
		<?php ob_get_flush(); ?>
	<?php endif; ?>
<?php endif; ?>