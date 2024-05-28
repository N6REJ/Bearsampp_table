<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\ParameterType;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use SYW\Library\Fonts as SYWFonts;
use SYW\Library\Utilities as SYWUtilities;

class Helper
{
	static $contacts = array();
	static $adp_config_params = null;

	static function date_to_counter($date, $date_in_future = false) {

		$date_origin = new Date($date);
		$now = new Date(); // now

		if ($date_in_future) {
			$difference = $date_origin->toUnix() - $now->toUnix();
		} else {
			$difference = $now->toUnix() - $date_origin->toUnix();
		}

		//$difference = $date_origin->diff($now); // object PHP 5.3 [y] => 0 [m] => 0 [d] => 26 [h] => 23 [i] => 11 [s] => 32 [invert] => 0 [days] => 26

		$nbr_days = 0;
		$nbr_hours = 0;
		$nbr_mins = 0;
		$nbr_secs = 0;

		if ($difference < 60) { // less than 1 minute
			$nbr_secs = $difference;
		} else if ($difference < 3600) { // less than 1 hour
			$nbr_mins = $difference / 60;
			$nbr_secs = $difference % 60;
		} else if ($difference < 86400) { // less than 1 day
			$nbr_hours = $difference / 3600;
			$nbr_mins = ($difference % 3600) / 60;
			$nbr_secs = $difference % 60;
		} else { // 1 day or more
			$nbr_days = $difference / 86400;
			$nbr_hours = ($difference % 86400) / 3600;
			$nbr_mins = ($difference % 3600) / 60;
			$nbr_secs = $difference % 60;
		}

		return array('days' => $nbr_days, 'hours' => $nbr_hours, 'mins' => $nbr_mins, 'secs' => $nbr_secs);
	}

	/**
	 * Create the first part of the <a> tag for links a, b and c
	 */
	static function getATagLinks($url, $urltext, $target, $tooltip = true, $popup_width = '600', $popup_height = '500', $css_classes = '')
	{
		// do not add tooltips in case links are internal

		switch ($target) {
			case 1:	// open in a new window
				return '<a class="'.$css_classes.'" href="'.htmlspecialchars($url).'" target="_blank">';
				break;
			case 2: case 3:	// open in a popup window
				$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height;
				return '<a class="'.$css_classes.'" href="'.$url.'" onclick="window.open(this.href, \'targetWindow\', \''.$attribs.'\'); return false;">';
				break;
			default: // open in parent window
				return '<a class="'.$css_classes.'" href="'.htmlspecialchars($url).'">';
		}
	}

	/**
	 * Get detail parameters
	 *
	 * @return array
	 */
	private static function getDetails($params, $view, $prefix = '', $subform = '') {

		$infos = array();

		$user = Factory::getUser();
		$groups	= $user->getAuthorisedViewLevels();

		// get data from subform items

	    $information_blocs = $params->get($prefix.$subform); // array of objects
	    if (!empty($information_blocs) && is_object($information_blocs)) {
	        foreach ($information_blocs as $information_bloc) {
	            if ($information_bloc->info != 'none' && in_array($information_bloc->access, $groups)) {

	                if ((($information_bloc->showing_in == '' || $information_bloc->showing_in == 2) && $view == 'article')
	                    || (($information_bloc->showing_in == '' || $information_bloc->showing_in == 1) && $view != 'article')) {

		                $details = array();
		                $details['info'] = $information_bloc->info;
		                $details['prepend'] = $information_bloc->prepend;
		                $details['append'] = isset($information_bloc->append) ? $information_bloc->append : '';
		                $details['show_icon'] = $information_bloc->show_icons == 1 ? true : false;
		                $details['icon'] = SYWUtilities::getIconFullName($information_bloc->icon);
		                $details['extra_classes'] = isset($information_bloc->extra_classes) ? $information_bloc->extra_classes : '';

		                $infos[] = $details;

		                if ($information_bloc->new_line == 1) {
		                	$infos[] = array('info' => 'newline', 'prepend' => '', 'append' => '', 'show_icon' => false, 'icon' => '', 'extra_classes' => '');
		                }
                    }
	            }
	        }
	    }

		return $infos;
	}

	/**
	 * Get block information
	 */
	static function getInfoBlock($params, $item, $item_params, $view, $position) {

		$info_block = '';

		$infos = self::getDetails($params, $view, $position.'_', 'information_blocks');

		if (empty($infos)) {
			return $info_block;
		}

		$config_params = self::getConfig();

		$bootstrap_version = ($params->get('bootstrap_version', '') == '') ? $config_params->get('bootstrap_version', 'joomla') : $params->get('bootstrap_version', '');
		$load_bootstrap = false;
		if ($bootstrap_version === 'joomla') {
			$bootstrap_version = 5;
			$load_bootstrap = true;
		} else {
			$bootstrap_version = intval($bootstrap_version);
		}

		if ($load_bootstrap) {
		    HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
		}

		$db = Factory::getDbo();
		$app = Factory::getApplication();

// 		$show_date = $params->get('show_d', 'date');

// 		$date_format = Text::_('PLG_CONTENT_ARTICLEDETAILS_FORMAT_DATE');
// 		if (empty($date_format)) {
// 			$date_format = $params->get('d_format', 'd F Y');
// 		}

// 		$time_format = Text::_('PLG_CONTENT_ARTICLEDETAILS_FORMAT_TIME');
// 		if (empty($time_format)) {
// 			$time_format = $params->get('t_format', 'H:i');
// 		}

		$layout_suffix = trim($params->get('layout_suffix', ''));

		$separator = htmlspecialchars($params->get('separator', ''));
		$separator = empty($separator) ? ' ' : $separator;

		$info_block .= '<dt>'.Text::_('PLG_CONTENT_ARTICLEDETAILS_INFORMATION_LABEL').'</dt>';

		$info_block .= '<dd class="details">';
		$has_info_from_previous_detail = false;

		$force_show = $params->get('force_show', 0);

		foreach ($infos as $key => $value) {

			switch ($value['info']) {
				case 'newline':
					$info_block .= '</dd><dd class="details">';
					$has_info_from_previous_detail = false;
				break;

				case 'hits':

				    if (isset($item->hits) && ($item_params->get('show_hits') || $force_show)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_hits', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'rating':

				    /* if no rating, still need to be able to show that there is none */

				    if (/*isset($item->rating) && */($item_params->get('ad_show_vote') || $force_show)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_rating', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						if ($view == 'article' && $item->state == 1 && !$app->input->getBool('print')) {
						    $data['show_form'] = true;
						}
						
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'author':

				    if (isset($item->author) && ($item_params->get('show_author') || $force_show)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_author', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'authorcb':

				    if (isset($item->author) && ($item_params->get('show_author') || $force_show)) {
				        if ($has_info_from_previous_detail) {
				            $info_block .= '<span class="delimiter">'.$separator.'</span>';
				        }

				        $layout = new FileLayout('details.adp_detail_authorcommunitybuilder', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

				        if ($layout_suffix) {
				        	$layout->setSuffixes(array($layout_suffix));
				        }

				        $data = array('item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

				        $info_block .= $layout->render($data);

				        $has_info_from_previous_detail = true;
				    }
				    break;

				case 'keywords':
				case 'keywordssearch':
				case 'keywordsfinder':

					$keywords = preg_split ('/[\s]*[,][\s]*/', $item->metakey, -1, PREG_SPLIT_NO_EMPTY); // deals with "key1  ,key2,   key3  "
					// empty($keyword) in the following code should be unnecessary since we used PREG_SPLIT_NO_EMPTY

					if (!empty($keywords)) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_keywords', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$data['keywords'] = $keywords;

						$data['link'] = 'none';
						if ($value['info'] == 'keywordssearch') {
						    $data['link'] = 'search';
						} else if ($value['info'] == 'keywordsfinder') {
						    $data['link'] = 'finder';
						}

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'parentcategory':

				    if (isset($item->parent_title) && $item->parent_id !== 1 && ($item_params->get('show_parent_category') || $force_show)) { // do not show any parent info if the parent is root

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_parentcategory', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('view' => $view, 'item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'category':

				    if (isset($item->category_title) && ($item_params->get('show_category') || $force_show)) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_category', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('view' => $view, 'item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'combocategories':

				    if ($item_params->get('show_category') || $force_show) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_combocategories', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('view' => $view, 'item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'created':
				case 'modified':
				case 'published':
				case 'finished':

					$date = $item->publish_up;
					if ($value['info'] == 'created') {
						$date = $item->created;
					} else if ($value['info'] == 'modified') {
						$date = $item->modified;
					} else if ($value['info'] == 'finished') {
					    $date = $item->publish_down;
					}

					if ($date == $db->getNullDate() || empty($date)) {
						//$info_block .= '<span class="detail detail_date"><span class="article_nodate"></span></span>';
					} else {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_date', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$data['date'] = $date;
						$data['date_type'] = $value['info'];

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'createdtime':
				case 'modifiedtime':
				case 'publishedtime':
				case 'finishedtime':

					$date = $item->publish_up;
					if ($value['info'] == 'createdtime') {
						$date = $item->created;
					} else if ($value['info'] == 'modifiedtime') {
						$date = $item->modified;
					} else if ($value['info'] == 'finishedtime') {
						$date = $item->publish_down;
					}

					if ($date == $db->getNullDate() || empty($date)) {
						//$info_block .= '<span class="detail detail_time"><span class="article_notime"></span></span>';
					} else {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_time', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$data['date'] = $date;
						$data['date_type'] = $value['info'];

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'linka':
				case 'linkb':
				case 'linkc':
				case 'links':
				case 'linksnl':

					if (isset($item->urls)) {

						$urls = json_decode($item->urls);

						if ($urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))) {

							// if all links a b c
							if ($value['info'] == 'links' || $value['info'] == 'linksnl') {

							    if ($has_info_from_previous_detail) {
							        $info_block .= '<span class="delimiter">'.$separator.'</span>';
							    }

							    $layout = new FileLayout('details.adp_detail_linksabc', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

							    if ($layout_suffix) {
							    	$layout->setSuffixes(array($layout_suffix));
							    }

							    $data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							    $data['urls'] = $urls;

							    $data['separated'] = ($value['info'] == 'linksnl') ? true : false;

							    $info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							} // end all links a b c

							// link a
							if ($value['info'] == 'linka' && !empty($urls->urla)) {

							    if ($has_info_from_previous_detail) {
							        $info_block .= '<span class="delimiter">'.$separator.'</span>';
							    }

							    $layout = new FileLayout('details.adp_detail_linkabc', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

							    if ($layout_suffix) {
							    	$layout->setSuffixes(array($layout_suffix));
							    }

							    $data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							    $data['link'] = 'a';

							    $data['url_target'] = $urls->targeta;
							    $data['url_text'] = $urls->urlatext;
							    $data['url_link'] = $urls->urla;

							    $info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}

							// link b
							if ($value['info'] == 'linkb' && !empty($urls->urlb)) {

							    if ($has_info_from_previous_detail) {
							        $info_block .= '<span class="delimiter">'.$separator.'</span>';
							    }

							    $layout = new FileLayout('details.adp_detail_linkabc', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

							    if ($layout_suffix) {
							    	$layout->setSuffixes(array($layout_suffix));
							    }

							    $data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							    $data['link'] = 'b';

							    $data['url_target'] = $urls->targetb;
							    $data['url_text'] = $urls->urlbtext;
							    $data['url_link'] = $urls->urlb;

							    $info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}

							if ($value['info'] == 'linkc' && !empty($urls->urlc)) {

							    if ($has_info_from_previous_detail) {
							        $info_block .= '<span class="delimiter">'.$separator.'</span>';
							    }

							    $layout = new FileLayout('details.adp_detail_linkabc', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

							    if ($layout_suffix) {
							    	$layout->setSuffixes(array($layout_suffix));
							    }

							    $data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							    $data['link'] = 'c';

							    $data['url_target'] = $urls->targetc;
							    $data['url_text'] = $urls->urlctext;
							    $data['url_link'] = $urls->urlc;

							    $info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}
						}
					}
				break;

				case 'tags':
				case 'linkedtags':

				    if (isset($item->tags) && !empty($item->tags->itemTags) && ($item_params->get('ad_show_tags') || $force_show)) {

						$item_tags = $item->tags->itemTags;

						// remove tags to hide
						$tags_to_hide = $params->get('hide_tags');
						if (!empty($tags_to_hide)) {
							foreach ($item_tags as $key => $item_tag) {
								if (in_array($item_tag->id, $tags_to_hide)) {
									unset($item_tags[$key]);
								}
							}
						}

						if (!empty($item_tags)) {

							// order tags

							switch ($params->get('order_tags', 'none')) {
							    case 'console': usort($item_tags, array(__CLASS__, 'compare_tags_by_console')); break;
								case 'alpha': usort($item_tags, array(__CLASS__, 'compare_tags_by_name')); break;
							}

// 							if ($value['info'] == 'linkedtags') {
// 								JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
// 							}

							if ($has_info_from_previous_detail) {
								$info_block .= '<span class="delimiter">'.$separator.'</span>';
							}

							$layout = new FileLayout('details.adp_detail_tags', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

							if ($layout_suffix) {
								$layout->setSuffixes(array($layout_suffix));
							}

							$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							$data['tags'] = $item_tags;

							$data['linked'] = false;
							if ($value['info'] == 'linkedtags') {
							    $data['linked'] = true;
							}

							$info_block .= $layout->render($data);

							$has_info_from_previous_detail = true;
						} // end not empty tags
					}
				break;

				case 'share':
				    
					if (!empty($item->link) && !$app->input->getBool('print')) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_share', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'email':
					if ($item->link && !$app->input->getBool('print')) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_email', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'print':

					if (isset($item->slug) && !$app->input->getBool('print')) {
						// only article and blog views get slug property

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_print', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
					break;

				case 'associations':

					if (isset($item->associations) && !empty($item->associations) && ($item_params->get('show_associations') || $force_show)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.adp_detail_associations', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						$data = array('item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				default:

					$type_temp = explode(':', $value['info']); // $value['info'] is jfield:type:field

					if (count($type_temp) < 2) {
						break;
					}

					if ($type_temp[0] == 'jfield') { // Joomla fields

						$field_id = $type_temp[2];
						$field_type = $type_temp[1];

						$db = Factory::getDBO();

						$query = $db->getQuery(true);

						// not using GROUP_CONCAT to make sure compatible with all databases
						$query->select($db->quoteName(array('fv.value', 'f.label', 'f.name', 'f.params', 'f.fieldparams'), array('value', 'title', 'alias', 'fieldoptions', 'fieldparams')));
						$query->from($db->quoteName('#__fields_values', 'fv'));
						$query->where($db->quoteName('fv.field_id') . '= :fieldId');
						$query->bind(':fieldId', $field_id, ParameterType::INTEGER);
						$query->where($db->quoteName('fv.item_id') . '= ' . $item->id);

						// $query->where('f.access IN ('.$groups.')'); // TODO?

						$query->join('LEFT', $db->quoteName('#__fields', 'f'), $db->quoteName('f.id') . ' = ' . $db->quoteName('fv.field_id'));

						$db->setQuery($query);

						$results = array();
						try {
							$results = $db->loadAssocList();
						} catch (ExecutionFailureException $e) {
							Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
						}

						if (count($results) > 0) {

							if ($has_info_from_previous_detail) {
								$info_block .= '<span class="delimiter">'.$separator.'</span>';
							}

							$options = array();

							$field_params = json_decode($results[0]['fieldparams']);
							if (isset($field_params->options) && is_object($field_params->options)) {
								foreach ($field_params->options as $key => $the_value) {
									$options[$the_value->value] = $the_value->name;
								}
							}

							$field_values = array();
							foreach ($results as $result) {
								if (!empty($options)) {
									if (isset($options[$result['value']])) {
										if (Factory::getLanguage()->hasKey($options[$result['value']])) {
											$field_values[] = Text::_($options[$result['value']]);
										} else {
											$field_values[] = $options[$result['value']];
										}
									} else {
										//$field_values[] = ''; // could happen, for instance 3 values then get down to 2
									}
								} else {
									$field_values[] = $result['value'];
								}
							}
							
							if ($field_type === 'sql') {
							    
							    $db = Factory::getDbo();
							    
							    $query = $db->getQuery(true);
							    $query->setQuery($field_params->query . ' HAVING ' . $db->quoteName('value') . ' IN (' . implode(', ', $field_values) . ')');
							    
							    try {
							        $db->setQuery($query);
							        $items = $db->loadObjectList();
							        
							        if (count($items) > 0) {							        
    							        $text_values = array();
    							        
    							        foreach ($items as $item) {
    							            $text_values[] = $item->text;
    							        }
    							        
    							        $field_values = $text_values;
							        }
							    } catch (ExecutionFailureException $e) {
							        // return the raw values
							    }
							}

							$supported_layout_types = array('calendar', 'textarea', 'url', 'editor'); // all other types use the generic output

							// additional fields - need a layout to show
							// if no layout in template, will show nothing (will not go to generic layout)

							$config_params = self::getConfig();

							$additional_types = $config_params->get('additional_supported_fields', array());
							if (!empty($additional_types)) {
								$supported_layout_types = array_merge($additional_types, $supported_layout_types);
							}

							if (in_array($field_type, $supported_layout_types)) {
                                $layout = new FileLayout('details.adp_detail_jfield_'.$field_type, null, array('component' => 'com_articledetailsprofiles')); // gets override from component
							} else {
							    $layout = new FileLayout('details.adp_detail_jfield_generic', null, array('component' => 'com_articledetailsprofiles')); // gets override from component
							}

							if ($layout_suffix) {
								$layout->setSuffixes(array($layout_suffix));
							}

							$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'postinfo' => $value['append'], 'show_icon' => $value['show_icon'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							$data['field_id'] = $field_id;
							$data['field_type'] = $field_type;
							$data['field_label'] = $results[0]['title'];
							$data['field_values'] = $field_values;
							$data['field_options'] = json_decode($results[0]['fieldoptions']);
							$data['field_params'] = $field_params;

							$output = $layout->render($data);
							if (trim($output) != '') {
							    $info_block .= $output;
							    $has_info_from_previous_detail = true;
							}
						}
					} else { // plugins

						PluginHelper::importPlugin('articledetails');

						$value['info'] = $type_temp[1];

						$results = Factory::getApplication()->triggerEvent('onArticleDetailsGetDetailData', array($value, $params, $item, $item_params));
						foreach ($results as $result) {
							if ($result != null) {

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$info_block .= $result;
								$has_info_from_previous_detail = true;
							}
						}
					}
			}
		}

		$info_block .= '</dd>';

		// when using the vote form, potential delimiter if the next info is on the same line
		// we should never have a delimiter at the start of the line
		if (!empty($separator)) {
			 $info_block = str_replace('<dd class="details"><span class="delimiter">'.$separator.'</span>', '<dd class="details">', $info_block);
		} else {
			 $info_block = str_replace('<dd class="details"><span class="delimiter"> </span>', '<dd class="details">', $info_block);
		}

		// remove potential <dd class="details"></dd> when no data is available
		$info_block = str_replace('<dd class="details"></dd>', '', $info_block);

		if (strpos($info_block, 'dd') === false) {
			return ''; // accessibility rule: if no dd then no dt is allowed
		}

		return $info_block;
	}

	static function remove_protocol($url)
	{
		$disallowed = array('http://', 'https://');
		foreach($disallowed as $d) {
			if(strpos($url, $d) === 0) {
				return str_replace($d, '', $url);
			}
		}
		return $url;
	}

	static function getInlineStyles($params)
	{
		$_style = '';

		// additional user styles
		$_user_style = trim($params->get('style_overrides', ''));
		if (!empty($_user_style)) {
			$_style .= $_user_style.' ';
		}

		// font details

		$font_details = $params->get('fontdetails', '');
		if (!empty($font_details)) {
			$font_details = str_replace('\'', '"', $font_details); // " lost, replaced by '

			$google_font = SYWUtilities::getGoogleFont($font_details); // get Google font, if any
			if ($google_font) {
				SYWFonts::loadGoogleFont($google_font);
			}

			$_style .= '.articledetails .info .details {';
			$_style .= 'font-family: '.$font_details;
			$_style .= '} ';
		}

		// get elements to override

		if (!$params->get('autohide_title', 0)) {
			$title_element = trim($params->get('title_element', ''));
			if (!empty($title_element)) {
				$elements = explode(',', $title_element);
				foreach ($elements as $element) {
					$_style .= $element.',';
				}
				$_style = rtrim($_style, ',');
				$_style .= ' { display:none; } ';
			}
		}

		$info_element = trim($params->get('info_element', '.article-info'));
		if (!empty($info_element)) {
			$elements = explode(',', $info_element);
			foreach ($elements as $element) {
				$_style .= $element.',';
			}
			$_style = rtrim($_style, ',');
			$_style .= ' { display:none; } ';
		}

		$links_element = trim($params->get('links_element', ''));
		if (!empty($links_element)) {
			$elements = explode(',', $links_element);
			foreach ($elements as $element) {
				$_style .= $element.',';
			}
			$_style = rtrim($_style, ',');
			$_style .= ' { display:none; } ';
		}

		if (!$params->get('autohide_tags', 0)) {
			$tags_element = trim($params->get('tags_element', ''));
			if (!empty($tags_element)) {
				$elements = explode(',', $tags_element);
				foreach ($elements as $element) {
					$_style .= $element.',';
				}
				$_style = rtrim($_style, ',');
				$_style .= ' { display:none; } ';
			}
		}

		$icons_element = trim($params->get('icons_element', ''));
		if (!empty($icons_element)) {
			$elements = explode(',', $icons_element);
			foreach ($elements as $element) {
				$_style .= $element.',';
			}
			$_style = rtrim($_style, ',');
			$_style .= ' { display:none; } ';
		}

		$fields_element = trim($params->get('fields_element', ''));
		if (!empty($fields_element)) {
			$elements = explode(',', $fields_element);
			foreach ($elements as $element) {
				$_style .= $element.',';
			}
			$_style = rtrim($_style, ',');
			$_style .= ' { display:none; } ';
		}

		$images_element = trim($params->get('images_element', ''));
		if (!empty($images_element)) {
			$elements = explode(',', $images_element);
			foreach ($elements as $element) {
				$_style .= $element.',';
			}
			$_style = rtrim($_style, ',');
			$_style .= ' { display:none; } ';
		}

		return $_style;
	}

	static function compare_tags_by_name($tag1, $tag2)
	{
		return strcmp($tag1->title, $tag2->title);
	}

	static function compare_tags_by_console($tag1, $tag2)
	{
		return (intval($tag1->lft) > intval($tag2->lft) ) ? 1 : -1;
	}

	static function getContact($author_id)
	{
		if (isset(self::$contacts[$author_id])) {
			return self::$contacts[$author_id];
		}

		$db = Factory::getDbo();

		$query = $db->getQuery(true);

		$query->select('MAX(' . $db->quoteName('id') . ') AS contactid');
		$query->select($db->quoteName(array('alias', 'catid', 'webpage', 'email_to'), array('alias', 'catid', 'webpage', 'email')));
		$query->from($db->quoteName('#__contact_details'));
		$query->where($db->quoteName('published') . ' = 1');
		$query->where($db->quoteName('user_id') . ' = :userId');
		$query->bind(':userId', $author_id, ParameterType::INTEGER);

		if (Multilanguage::isEnabled()) {
		    $query->where('(' . $db->quoteName('language') . ' IS NULL OR ' . $db->quoteName('language') . ' IN (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . '))');
		}

		$db->setQuery($query);

		try {
			self::$contacts[$author_id] = $db->loadObject();
		} catch (ExecutionFailureException $e) {
			//Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return null;
		}

		return self::$contacts[$author_id];
	}

	static function getContactPicture($author_id)
	{
		$picture = '';

		$db = Factory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('image'));
		$query->from($db->quoteName('#__contact_details'));
		$query->where($db->quoteName('published') . ' = 1');
		$query->where($db->quoteName('user_id') . ' = :userId');
		$query->bind(':userId', $author_id, ParameterType::INTEGER);

		if (Multilanguage::isEnabled()) {
		    $query->where('(' . $db->quoteName('language') . ' IS NULL OR ' . $db->quoteName('language') . ' IN (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . '))');
		}

		$db->setQuery($query);

		try {
			$picture = $db->loadResult();
		} catch (ExecutionFailureException $e) {
			//Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return null;
		}

		return $picture;
	}

	static function getAuthorEmail($author_id)
	{
		$email = '';

		$db = Factory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('email'));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id') . ' = :id');
		$query->bind(':id', $author_id, ParameterType::INTEGER);

		$db->setQuery($query);

		try {
			$email = $db->loadResult();
		} catch (ExecutionFailureException $e) {
			//Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$email = null;
		}

		return $email;
	}

	// $selected_field is jfield:[type]:fieldid
	static function getCustomField($selected_field, $id)
	{
		$result = '';

		$type_temp = explode(':', $selected_field);

		$db = Factory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('value'));
		$query->from($db->quoteName('#__fields_values'));
		$query->where($db->quoteName('field_id') . ' = :fieldId');
		$query->bind(':fieldId', $type_temp[2], ParameterType::INTEGER);
		$query->where($db->quoteName('item_id') . ' = :id');
		$query->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);

		try {
			$result = $db->loadResult();
		} catch (ExecutionFailureException $e) {
			//Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$result = null;
		}

		return $result;
	}

	/**
	 * Get the site mode
	 * @return string (dev|prod|adv)
	 */
	public static function getSiteMode($params)
	{
		return ($params->get('site_mode', '') == '') ? self::getConfig()->get('site_mode', 'dev') : $params->get('site_mode', '');
	}

	/**
	 * Is the picture cache set to be cleared
	 * @return boolean
	 */
	public static function IsClearPictureCache($params)
	{
		if (self::getSiteMode($params) == 'dev') {
			return true;
		}
		if (self::getSiteMode($params) == 'prod') {
			return false;
		}
		return boolval(($params->get('clear_cache', '') == '') ? self::getConfig()->get('clear_cache', true) : $params->get('clear_cache', ''));
	}

	/**
	 * Is the style/script cache set to be cleared
	 * @return boolean
	 */
	public static function IsClearHeaderCache($params)
	{
		if (self::getSiteMode($params) == 'dev') {
			return true;
		}
		if (self::getSiteMode($params) == 'prod') {
			return false;
		}
		return boolval(($params->get('clear_header_files_cache', '') == '') ? self::getConfig()->get('clear_header_files_cache', true) : $params->get('clear_header_files_cache', ''));
	}

	/**
	 * Are errors shown ?
	 * @return boolean
	 */
	public static function isShowErrors($params)
	{
		if (self::getSiteMode($params) == 'dev') {
			return true;
		}
		if (self::getSiteMode($params) == 'prod') {
			return false;
		}
		return boolval(($params->get('show_errors', '') == '') ? self::getConfig()->get('show_errors', false) : $params->get('show_errors', ''));
	}

	/**
	 * Get the component's configuration parameters
	 * @return \Joomla\Registry\Registry
	 */
	public static function getConfig()
	{
	    if (!isset(self::$adp_config_params)) {

	        self::$adp_config_params = new Registry();

			if (File::exists(JPATH_ADMINISTRATOR . '/components/com_articledetailsprofiles/config.xml')) {
			    self::$adp_config_params = ComponentHelper::getParams('com_articledetailsprofiles');
			}
		}

		return self::$adp_config_params;
	}

}
?>