<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Registry\Registry;
use SYW\Library\Libraries as SYWLibraries;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Module\LatestNewsEnhanced\Site\Helper\CalendarHelper as LNECalendarHelper;
use SYW\Module\LatestNewsEnhanced\Site\Helper\ContentHelper as LNEContentHelper;
use SYW\Module\LatestNewsEnhanced\Site\Helper\Helper as LNEHelper;

if ($bootstrap_version === 0) {
    
    // accordion when no Bootstrap
    
    $style = '.collapse { max-height: 0; overflow: hidden; -webkit-transition: max-height .5s ease-in-out; -moz-transition: max-height .5s ease-in-out; -o-transition: max-height .5s ease-in-out; transition: max-height .5s ease-in-out; } ';
    $style .= '.collapse.in { max-height: 100vh; } ';
    $style .= '.panel-body { padding: 15px; } ';
    $style .= '.panel-heading { cursor: pointer; } ';
    $style .= '.panel-heading a { display: block; padding: 10px 15px; cursor: pointer; } ';
    
    Factory::getApplication()->getDocument()->getWebAssetManager()->addInlineStyle($style);
    
    $script = 'document.addEventListener("readystatechange", function(event) { ';
        $script .= 'if (event.target.readyState === "complete") { ';
            $script .= 'var panel_titles = document.querySelectorAll(".panel-heading"); ';
            $script .= 'for (var i = 0; i < panel_titles.length; i++) { ';        
                $script .= 'panel_titles[i].addEventListener("keydown", function(e) { ';
                    $script .= 'if (e.which === 32 || e.which === 13) { e.preventDefault(); this.click(); } ';
                $script .= '}); ';            
                $script .= 'panel_titles[i].addEventListener("click", function() { ';
                    $script .= 'var link = this.querySelector("a"); ';
                    $script .= 'link.classList.toggle("collapsed"); ';
                    $script .= 'if (link.classList.contains("collapsed")) { ';
                        $script .= 'link.setAttribute("aria-expanded", "false"); ';
                    $script .= '} else { ';
                        $script .= 'link.setAttribute("aria-expanded", "true"); ';
                    $script .= '} ';
                    $script .= 'var panel_id = link.getAttribute("href").replace("#", ""); ';
                    $script .= 'document.getElementById(panel_id).classList.toggle("in")';
                $script .= '}); ';
            $script .= '} ';
        $script .= '} ';
    $script .= '}); ';
    
    Factory::getApplication()->getDocument()->getWebAssetManager()->addInlineScript($script);
}

// accessibility not handled by Bootstrap 2
if ($bootstrap_version == 2) {
    $script = 'document.addEventListener("readystatechange", function(event) { ';
        $script .= 'if (event.target.readyState === "complete") { ';
            $script .= 'var panel_titles = document.querySelectorAll(".accordion-heading"); ';
                $script .= 'for (var i = 0; i < panel_titles.length; i++) { ';
                    $script .= 'panel_titles[i].addEventListener("click", function() { ';
                    $script .= 'var link = this.querySelector("a"); ';
                    $script .= 'if (link.classList.contains("collapsed")) { ';
                        $script .= 'link.setAttribute("aria-expanded", "false"); ';
                    $script .= '} else { ';
                        $script .= 'link.setAttribute("aria-expanded", "true"); ';
                    $script .= '} ';
                    $script .= '}); ';
            $script .= '} ';
        $script .= '} ';
    $script .= '}); ';
    
    Factory::getApplication()->getDocument()->getWebAssetManager()->addInlineScript($script);
}

if ($load_bootstrap) {
	HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
	HTMLHelper::_('bootstrap.collapse');
}
?>
<?php if ($datasource != 'articles') : ?>
	<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-error', $bootstrap_version); ?>"><?php echo Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_WRONGLAYOUT'); ?></div>
<?php elseif (empty($list)) : ?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee nonews<?php echo $isMobile ? ' mobile' : ''; ?>"><div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-info', $bootstrap_version); ?>"><?php echo $nodata_message; ?></div></div>
<?php else : ?>
	<?php
		$categories = LNEContentHelper::getCategoryList($params, $list);
		$nbr_cat = count($categories);

		$current_catid = $list[0]->catid;
		$new_catid = true;

		$modal_needed = false;

		if ($remove_whitespaces) {
			ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
		}
	?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee newslist<?php echo $isMobile ? ' mobile' : ''; ?> <?php echo $alignment; ?>">

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

		<?php if ($bootstrap_version < 3) : ?>
			<div class="latestnews-items accordion" id="lne_<?php echo $class_suffix; ?>_accordion">
		<?php endif; ?>
		<?php if ($bootstrap_version == 3) : ?>
			<div class="latestnews-items panel-group" id="lne_<?php echo $class_suffix; ?>_accordion" role="tablist" aria-multiselectable="true">
		<?php endif; ?>
		<?php if ($bootstrap_version >= 4) : ?>
			<div class="latestnews-items accordion" id="lne_<?php echo $class_suffix; ?>_accordion">
		<?php endif; ?>

			<?php foreach ($list as $i => $item) : ?>
				<?php
					$extraclasses = ($i % 2) ? " even" : " odd";

					if ($show_image || $show_calendar || $show_icon || $show_video) {
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

					$registry_attribs = new Registry();
					$registry_attribs->loadString($item->attribs);

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
					if ($app->input->get('option') === 'com_content' && $app->input->get('view') === 'article') {
						$current_id = $app->input->getInt('id');
						if ($current_id == $item->id) {
						    $css_item .= ' active';
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
				
    			<?php  if ($bootstrap_version == 2) : ?>
    				<?php $attributes = 'data-target="#' . 'slide_' . $class_suffix . '_' . $item->id . '" aria-controls="' . 'slide_' . $class_suffix . '_' . $item->id . '" data-toggle="collapse" aria-expanded="' . (0 === $i ? 'true' : 'false') . '"'; ?>
            		<div class="accordion-group latestnews-item id-<?php echo $item->id; ?> catid-<?php echo $item->catid; ?><?php echo $css_item; ?>">
            			<div class="accordion-heading" id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
            				<strong><a href="#<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" class="accordion-toggle<?php echo 0 === $i ? '' : ' collapsed' ?>" data-parent="#lne_<?php echo $class_suffix; ?>_accordion" <?php echo $attributes; ?>><?php echo $item->title ?></a></strong>
            			</div>
            			<div id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" class="accordion-body collapse<?php echo 0 === $i ? ' in' : '' ?>" aria-labelledby="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
            				<div class="accordion-inner">
        		<?php endif; ?>
        		<?php  if ($bootstrap_version == 0 || $bootstrap_version == 3) : ?>
    				<?php $attributes = 'data-target="#' . 'slide_' . $class_suffix . '_' . $item->id . '" aria-controls="' . 'slide_' . $class_suffix . '_' . $item->id . '"' . ($bootstrap_version == 3 ? ' data-toggle="collapse"' : ' onclick="return false;"') . ' aria-expanded="' . (0 === $i ? 'true' : 'false') . '"'; ?>
        			<div class="panel panel-default latestnews-item id-<?php echo $item->id; ?> catid-<?php echo $item->catid; ?><?php echo $css_item; ?>">
        				<div class="panel-heading" role="tab" id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
        					<h4 class="panel-title"><a href="#<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>"<?php echo 0 === $i ? '' : ' class="collapsed"' ?> role="button" data-parent="#lne_<?php echo $class_suffix; ?>_accordion" <?php echo $attributes; ?>><?php echo $item->title ?></a></h4>
        				</div>
        				<div id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" class="panel-collapse collapse<?php echo 0 === $i ? ' in' : '' ?>" role="tabpanel" aria-labelledby="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
        					<div class="panel-body">
        		<?php endif; ?>
        		<?php  if ($bootstrap_version == 4) : ?>
    				<?php $attributes = 'data-target="#' . 'slide_' . $class_suffix . '_' . $item->id . '" aria-controls="' . 'slide_' . $class_suffix . '_' . $item->id . '" data-toggle="collapse" aria-expanded="' . (0 === $i ? 'true' : 'false') . '"'; ?>
        			<div class="card latestnews-item id-<?php echo $item->id; ?> catid-<?php echo $item->catid; ?><?php echo $css_item; ?>">
        				<div class="card-header" id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
        					<h5 class="mb-0"><button class="btn btn-link<?php echo 0 === $i ? '' : ' collapsed' ?>" type="button" <?php echo $attributes; ?>><?php echo $item->title ?></button></h5>
        				</div>
        				<div id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" class="collapse<?php echo 0 === $i ? ' show' : '' ?>" aria-labelledby="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading" data-parent="#lne_<?php echo $class_suffix; ?>_accordion">
        					<div class="card-body">
        		<?php endif; ?>
        		<?php  if ($bootstrap_version == 5) : ?>
        			<div class="accordion-item">
                        <h2 class="accordion-header" id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading">
                          <button class="accordion-button<?php echo 0 === $i ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" aria-expanded="<?php echo 0 === $i ? 'true' : 'false' ?>" aria-controls="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>">
                            <?php echo $item->title ?>
                          </button>
                        </h2>
                        <div id="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>" class="accordion-collapse collapse<?php echo 0 === $i ? ' show' : '' ?>" aria-labelledby="<?php echo 'slide_' . $class_suffix . '_' . $item->id ?>_heading" data-bs-parent="#lne_<?php echo $class_suffix; ?>_accordion">
                          <div class="accordion-body">        		
				<?php endif; ?>
				
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
									<?php if (!empty($info_block_before_title)) : ?>
										<dl class="item_details before_title"><?php echo $info_block_before_title; ?></dl>
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
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
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
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
											</h<?php echo $title_html_tag; ?>>
										<?php endif; ?>
									<?php endif; ?>
									<?php if (!empty($info_block_after_title)) : ?>
										<dl class="item_details after_title"><?php echo $info_block_after_title; ?></dl>
									<?php endif; ?>
								</div>
							<?php endif; ?>							
							
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
							<?php elseif ($show_video) : ?>
								<?php if ($item->video || $item->imagetag || $keep_space) : ?>
									<div class="newshead videotype">
										<?php if ($item->video) : ?>
											<div class="video <?php echo (isset($item->video_type) ? $item->video_type : $video_type); ?>">
												<div class="innervideo">
													<?php
                                                        $layout = new FileLayout('heads.lnep_head_video_' . (isset($item->video_type) ? $item->video_type : $video_type), null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
                                                        $data = array('item' => $item, 'width' => $head_width, 'height' => $head_height, 'prefix' => 'lnee_' . $class_suffix, 'show_errors' => $show_errors, 'bootstrap_version' => $bootstrap_version);
                                            			echo $layout->render($data);
                                            		?>
                                            	</div>
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
										<?php elseif ($item->imagetag) : ?>
											<div class="video image">
												<div class="innerpicture">
													<?php echo $item->imagetag; ?>
												</div>
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
										<?php elseif ($keep_space) : ?>
											<div class="novideo">
												<span></span>
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
										<?php endif; ?>
									</div>
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
									<?php if (!empty($info_block_before_title)) : ?>
										<dl class="item_details before_title"><?php echo $info_block_before_title; ?></dl>
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
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
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
												<a href="<?php echo $item->link_edit; ?>" class="edit hasTooltip" title="<?php echo Text::_('JGLOBAL_EDIT'); ?>"><i class="SYWicon-create"></i></a>
											<?php endif; ?>
											</h<?php echo $title_html_tag; ?>>
										<?php endif; ?>
									<?php endif; ?>
									<?php if (!empty($info_block_after_title)) : ?>
										<dl class="item_details after_title"><?php echo $info_block_after_title; ?></dl>
									<?php endif; ?>
								<?php endif; ?>
								<?php if (!empty($info_block_before_text)) : ?>
									<dl class="item_details before_text"><?php echo $info_block_before_text; ?></dl>
								<?php endif; ?>
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
								<?php if (!empty($info_block_after_text)) : ?>
									<dl class="item_details after_text"><?php echo $info_block_after_text; ?></dl>
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
				
				</div></div></div>
				
			<?php endforeach; ?>
		</div>
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