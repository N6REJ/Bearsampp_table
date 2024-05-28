<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\View\K2Items;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use SYW\Component\LatestNewsEnhancedPro\Site\Cache\CSSFileCache;
use SYW\Component\LatestNewsEnhancedPro\Site\Cache\JSFileCache;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\CalendarHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Cache as SYWCache;
use SYW\Library\Fonts as SYWFonts;
use SYW\Library\Stylesheets as SYWStylesheets;
use SYW\Library\Utilities as SYWUtilities;

/**
 * View class for a blog of K2 items
 */
class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 * @return void
	 * @throws \Exception
	 */
	public function display($tpl = null)
	{
		//$doc = Factory::getDocument();
		$app = Factory::getApplication();
		$wam = $app->getDocument()->getWebAssetManager();
		$this->input = $app->input;

		$this->params = $app->getParams('com_latestnewsenhancedpro');

		$this->load_more = false;
		if ($this->params->get('pag_type', 'std') == 'lm') {
			$this->load_more = true;
			$app->setUserState('global.ajaxlist.limit', $this->params->get('initial_count', 10)); // only when load more
// 		} else {
// 			$pagination_limit_count = intVal($this->params->get('pag_l_c', ''));
// 			if ($pagination_limit_count > 0 && !$this->params->get('s_pag_l', 1)) {
// 				$app->setUserState('global.list.limit', $pagination_limit_count);
// 			} else {
// 				$app->setUserState('global.list.limit', $app->get('list_limit', 0)); // only gets the default, no way around it or else use 2 different pagination objects
// 			}
		}

		$this->state = $this->get('State');

// 		if ($this->state->get('firstuse')) {
// 			$pre_selected_category = $this->params->get('preselect_cat', 'none');
// 			if ($pre_selected_category != 'none') {
// 				$this->state->set('category', $pre_selected_category);
// 				$this->input->set('category', $pre_selected_category);
// 			}
// 		}

		$app->setUserState('global.ajaxlist.itemcount', 0);

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if ($this->load_more) {
			$app->setUserState('com_latestnewsenhancedpro.ajaxtotal', $this->pagination->total); // only when load more
		}

		$this->_prepareDocument();

		$this->pageclass_sfx = trim(htmlspecialchars($this->params->get('pageclass_sfx', '')));

		$this->css_prefix = '.lnep';
		$this->params->set('css_prefix', $this->css_prefix);

		$this->params->set('view', $this->input->get('view'));

		$this->params->set('layout', $this->input->get('layout', 'blog'));

		$this->extra_suffix = $this->input->get('view').'_'.$this->params->get('layout');
		if ($app->getMenu()->getActive()) {
			$this->extra_suffix .= '_'.$app->getMenu()->getActive()->id;
		}

		$this->urlPath = Uri::base().'components/com_latestnewsenhancedpro/';

		$subdirectory = 'thumbnails/lnep';
		if ($this->params->get('thumb_path', 'cache') == 'cache') {
			$subdirectory = 'com_latestnewsenhancedpro';
		}
		$this->tmp_path = SYWCache::getTmpPath($this->params->get('thumb_path', 'cache'), $subdirectory);

		// unique filenames

// 		$this->unique_filename_extra = '';
// 		if ($this->params->get('unique_files', 1)) {
// 			if ($app->getMenu()->getActive()) {
// 				$this->unique_filename_extra = 'blog_'.$app->getMenu()->getActive()->id;
// 			} else {
// 				$this->unique_filename_extra = 'blog';
// 			}
// 		}

// 		$this->clear_cache = $this->params->get('clear_cache', 0);
// 		if ($this->clear_cache) {
// 			Helper::clearThumbnails($this->tmp_path, $this->unique_filename_extra);
// 		}

		$this->bootstrap_version = $this->params->get('bootstrap_version', 'joomla');
		$this->load_bootstrap = false;
		if ($this->bootstrap_version === 'joomla') {
		    $this->bootstrap_version = 5;
		    $this->load_bootstrap = true;
		} else {
			$this->bootstrap_version = intval($this->bootstrap_version);
		}

		$this->site_mode = Helper::getSiteMode($this->params);

		$this->show_errors = Helper::isShowErrors($this->params);

		$this->load_remotely = intval($this->params->get('remote_libraries', 0));

		$this->clear_header_files_cache = Helper::IsClearHeaderCache($this->params);

		$this->popup_width = $this->params->get('popup_x', 600);
		$this->popup_height = $this->params->get('popup_y', 500);

		// configuration

		$this->head_type_leading = $this->params->get('leading_head_type', 'none');
		$this->head_type = $this->params->get('head_type', 'none');

		$show_image = false;
		$show_calendar = false;

		$image_types = array('image', 'imageintro', 'authorcontact', 'authork2user', 'allimagesasc', 'allimagesdesc');
		if (in_array($this->head_type_leading, $image_types) || substr($this->head_type_leading, 0, strlen('k2field:image')) === 'k2field:image'
				|| in_array($this->head_type, $image_types) || substr($this->head_type, 0, strlen('k2field:image')) === 'k2field:image') {
			$show_image = true;
		}

		if ($this->head_type_leading == 'calendar' || substr($this->head_type_leading, 0, strlen('k2field:date')) === 'k2field:date'
				|| $this->head_type == 'calendar' || substr($this->head_type, 0, strlen('k2field:date')) === 'k2field:date') {
			$show_calendar = true;
		}

		// pagination

		$layout_suffix = trim($this->params->get('layout_suffix', ''));

		$this->show_pagination = false;
		if ($this->params->get('pag_type', 'std') == 'std') {
			$this->show_pagination = true;
		}

		$this->show_pagination_results = $this->params->get('s_pag_r', 1);
		$this->show_pagination_results_total = $this->params->get('s_pag_r_t', 1);
		$this->show_pagination_limit = $this->params->get('s_pag_l', 1);
		if ($this->show_pagination_limit) {
			$this->limits = Helper::getLimitsForBox($this->params->get('pag_l', ''));
		}
		//$this->pagination_limit_count = $this->params->get('pag_l_c', '');

		$this->show_cat_picture = $this->params->get('show_cat_pic', 0);
		$this->category_default_picture = $this->params->get('cat_d_pic', '');

		$this->show_tag_picture = $this->params->get('show_tag_pic', 0);
		$this->tag_default_picture = $this->params->get('tag_d_pic', '');

		$this->show_author_picture = $this->params->get('show_author_pic', 0);
		$this->author_default_picture = $this->params->get('author_d_pic', '');

		$this->pagination_top_index = array('', '', ''); // filled to avoid overrides to break if search but no index and backward compatibility with overrides
		$this->pagination_bot_index = array('', '', ''); // filled to avoid overrides to break if search but no index and backward compatibility with overrides

		$this->pagination_layouts = array();
		$this->pagination_data = array();
		$this->pagination_class = array();

		$filters = array();

		$top_filters = $this->params->get('top_filters', null);
		if (!empty($top_filters) && is_array($top_filters)) {
			foreach ($top_filters as $filter) {
				switch ($filter['formfilter']) {
					case 'a' :
						if (!in_array('top_authors', $this->pagination_top_index)) {
							$this->pagination_top_index[] = 'top_authors';
							$filters['top_authors'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'i' :
					    if (!in_array('top_alias', $this->pagination_top_index)) {
					        $this->pagination_top_index[] = 'top_alias';
					        $filters['top_alias'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
					    }
					    break;
					case 't' :
						if (!in_array('top_tags', $this->pagination_top_index)) {
							$this->pagination_top_index[] = 'top_tags';
							$filters['top_tags'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'c' :
						if (!in_array('top_categories', $this->pagination_top_index)) {
							$this->pagination_top_index[] = 'top_categories';
							$filters['top_categories'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'p' :
						if (!in_array('top_periods', $this->pagination_top_index)) {
							$this->pagination_top_index[] = 'top_periods';
							$filters['top_periods'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					default : // K2 extra field - value k2field:list:70 where 70 is the id of the field
						$field_temp = explode(':', $filter['formfilter']);
						$field_id = $field_temp[2];
						if (!in_array('top_k2field_' . $field_id, $this->pagination_top_index)) {
							$this->pagination_top_index[] = 'top_k2field_' . $field_id;
							$filters['top_k2field_' . $field_id] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
				}
			}
		}

		// for backward compatibility with overrides
		while (count($this->pagination_top_index) > 3 && $this->pagination_top_index[0] == '') {
			array_shift($this->pagination_top_index);
		}

		$bottom_filters = $this->params->get('bot_filters', null);
		if (!empty($bottom_filters) && is_array($bottom_filters)) {
			foreach ($bottom_filters as $filter) {
				switch ($filter['formfilter']) {
					case 'a' :
						if (!in_array('bot_authors', $this->pagination_bot_index)) {
							$this->pagination_bot_index[] = 'bot_authors';
							$filters['bot_authors'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'i' :
					    if (!in_array('bot_alias', $this->pagination_bot_index)) {
					        $this->pagination_bot_index[] = 'bot_alias';
					        $filters['bot_alias'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
					    }
					    break;
					case 't' :
						if (!in_array('bot_tags', $this->pagination_bot_index)) {
							$this->pagination_bot_index[] = 'bot_tags';
							$filters['bot_tags'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'c' :
						if (!in_array('bot_categories', $this->pagination_bot_index)) {
							$this->pagination_bot_index[] = 'bot_categories';
							$filters['bot_categories'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					case 'p' :
						if (!in_array('bot_periods', $this->pagination_bot_index)) {
							$this->pagination_bot_index[] = 'bot_periods';
							$filters['bot_periods'] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
						break;
					default : // K2 extra field - value k2field:list:70 where 70 is the id of the field
						$field_temp = explode(':', $filter['formfilter']);
						$field_id = $field_temp[2];
						if (!in_array('bot_k2field_' . $field_id, $this->pagination_bot_index)) {
							$this->pagination_bot_index[] = 'bot_k2field_' . $field_id;
							$filters['bot_k2field_' . $field_id] = array('style' => $filter['ff_style'], 'label' => $filter['ff_label']);
						}
				}
			}
		}

		// for backward compatibility with overrides
		while (count($this->pagination_bot_index) > 3 && $this->pagination_bot_index[0] == '') {
			array_shift($this->pagination_bot_index);
		}

		$this->field_index_array = array();

		foreach ($filters as $key => $filter) {
			switch ($key) {
				case 'top_categories': case 'bot_categories':

					$layout = new FileLayout('pagination.lnepk2categoryindex');
					if ($layout_suffix) {
						$layout->setSuffixes(array($layout_suffix));
					}

					$data = array('list' => $this->get('CategoriesList'), 'style' => $filter['style'], 'show_picture' => $this->show_cat_picture, 'default_picture' => $this->category_default_picture, 'selection_label' => $filter['label']);
					$data['unselectable'] = $this->params->get('cat_index_unselect', array());
					$data['show_hierarchy'] = $this->params->get('show_cat_hierarchy', 0);
					$data['bootstrap_version'] = $this->bootstrap_version;
					$data['load_bootstrap'] = $this->load_bootstrap;

					$this->pagination_layouts[$key] = $layout;
					$this->pagination_data[$key] = $data;
					$this->pagination_class[$key] = 'category_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');
					break;

				case 'top_tags': case 'bot_tags':

					$layout = new FileLayout('pagination.lnepk2tagindex');
					if ($layout_suffix) {
						$layout->setSuffixes(array($layout_suffix));
					}

					$data = array('list' => $this->get('TagsList'), 'style' => $filter['style'], 'show_picture' => $this->show_tag_picture, 'default_picture' => $this->tag_default_picture, 'selection_label' => $filter['label']);
					$data['bootstrap_version'] = $this->bootstrap_version;
					$data['load_bootstrap'] = $this->load_bootstrap;

					$this->pagination_layouts[$key] = $layout;
					$this->pagination_data[$key] = $data;
					$this->pagination_class[$key] = 'tag_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');
					break;

				case 'top_authors': case 'bot_authors':

					$layout = new FileLayout('pagination.lnepk2authorindex');
					if ($layout_suffix) {
						$layout->setSuffixes(array($layout_suffix));
					}

					$data = array('list' => $this->get('AuthorsList'), 'style' => $filter['style'], 'show_picture' => $this->show_author_picture, 'default_picture' => $this->author_default_picture, 'selection_label' => $filter['label']);
					$data['bootstrap_version'] = $this->bootstrap_version;
					$data['load_bootstrap'] = $this->load_bootstrap;

					$this->pagination_layouts[$key] = $layout;
					$this->pagination_data[$key] = $data;
					$this->pagination_class[$key] = 'author_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');
					break;
					
				case 'top_alias': case 'bot_alias':
				    
				    $layout = new FileLayout('pagination.lnepk2aliasindex');
				    if ($layout_suffix) {
				        $layout->setSuffixes(array($layout_suffix));
				    }
				    
				    $data = array('list' => $this->get('AliasList'), 'style' => $filter['style'], 'selection_label' => $filter['label']);
				    $data['bootstrap_version'] = $this->bootstrap_version;
				    $data['load_bootstrap'] = $this->load_bootstrap;
				    
				    $this->pagination_layouts[$key] = $layout;
				    $this->pagination_data[$key] = $data;
				    $this->pagination_class[$key] = 'alias_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');
				    break;

				case 'top_periods': case 'bot_periods':

					$periods = array();

					if ($this->params->get('use_range', 0) > 0) {
						if ((int)$this->params->get('use_range') === 1) {

							$start_date = new \DateTime('now');
							if ($this->params->get('range_to', 'week') !== 'now') {
								$start_date = Helper::getDateFromInterval($this->params->get('spread_to', 1), $this->params->get('range_to', 'week'));
							}

							$end_date = new \DateTime('now');
							if ($this->params->get('range_from', 'now') !== 'now') {
								$end_date = Helper::getDateFromInterval($this->params->get('spread_from', 1), $this->params->get('range_from', 'now'));
							}

						} else {
							$start = $this->params->get('start_date_range');
							if (empty($start)) {
								$start = 'now';
							}

							$end = $this->params->get('end_date_range');
							if (empty($end)) {
								$end = 'now';
							}

							$start_date = new \DateTime($start);
							$end_date = new \DateTime($end);
						}

						$periods = Helper::getPeriods($start_date, $end_date, $this->params->get('period_direction', 'desc'), !$this->params->get('period_year_only', 1), $this->params->get('period_m_format', 'm'));
					}

					$layout = new FileLayout('pagination.lnepperiodindex');
					if ($layout_suffix) {
						$layout->setSuffixes(array($layout_suffix));
					}

					$data = array('list' => $periods, 'style' => $filter['style'], 'selection_label' => $filter['label'], 'disable_years' => $this->params->get('period_year_disabled', 0));
					$data['bootstrap_version'] = $this->bootstrap_version;
					$data['load_bootstrap'] = $this->load_bootstrap;

					$this->pagination_layouts[$key] = $layout;
					$this->pagination_data[$key] = $data;
					$this->pagination_class[$key] = 'period_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');

					break;

				default: // K2 extra fields

					$field_temp = explode('_', $key);

					$field_id = $field_temp[2];
					if (!in_array($field_id, $this->field_index_array)) {
						$this->field_index_array[] = $field_id;
					}

					$field_name = '';
					$options_list = array();
					$multiple = false;

					$field_info = Helper::getK2ExtraFieldInfo($field_id);
					if (!empty($field_info)) {
						$field_name = $field_info['name'];
						if ($field_info['type'] == 'multipleSelect') {
							$multiple = true;
						}
						$default_values = json_decode($field_info['value']);
						foreach ($default_values as $default_value) {
							$options_list[] = (object) ['name' => $default_value->name, 'value' => $default_value->value];
						}
					}

					$layout = new FileLayout('pagination.lneplistfield');

					$data = array('id' => $field_id, 'name' => $field_name, 'list' => $options_list, 'multiple' => $multiple, 'style' => $filter['style'], 'selection_label' => $filter['label']);
					$data['bootstrap_version'] = $this->bootstrap_version;
					$data['load_bootstrap'] = $this->load_bootstrap;

					$this->pagination_layouts[$key] = $layout;
					$this->pagination_data[$key] = $data;
					$this->pagination_class[$key] = 'field_directory index_filter' . (($filter['style'] == 'selection') ? ' selection' : ' listing');
			}
		}

		// page display

		$this->actions_style = $this->params->get('actions_style', 'dropdown');
		$this->actions_classes = $this->params->get('actions_classes', '');
		$this->show_print = $this->params->get('print', 0);
		$this->show_search = $this->params->get('search', 0);
		$this->show_search_options = $this->params->get('search_options', 0);

 		$this->article_before_text = $this->get('TextArticleBefore');
 		$this->article_after_text = $this->get('TextArticleAfter');

		// add specific styles
 		$extra_styles = trim($this->params->get('style_overrides', ''));
 		if (!empty($extra_styles)) {
 			$extra_styles .= ' ';
		}

 		// add calendar
		if ($show_calendar) {
			$extra_styles .= CalendarHelper::getCalendarInlineStyles($this->params, $this->css_prefix.'_'.$this->input->get('layout', 'blog').' form');
		}
		
		// font details
		$font_details = $this->params->get('details_font', '');
		if (!empty($font_details)) {
		    SYWFonts::loadWebFonts(SYWFonts::getWebfontsFromFamily($font_details));
		    
		    $extra_styles .= $this->params->get('css_prefix') . '_' . $this->params->get('layout') . ' form .newsextra { font-family: ' . $font_details . '} ';
		}
		
		if (!empty($extra_styles)) {
		    $extra_styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $extra_styles); // minify the CSS code
		}

		// caching the stylesheet
		$cache_css = new CSSFileCache('com_latestnewsenhancedpro', $this->params);
		$cache_css->addDeclaration($extra_styles);

		$result = $cache_css->cache('style_'.$this->extra_suffix.'.css', $this->clear_header_files_cache);

		if ($result) {
			$wam->registerAndUseStyle('lnep.style_' . $this->extra_suffix, $cache_css->getCachePath() . '/style_' . $this->extra_suffix . '.css');
		}

		// TODO
// 		Helper::loadUserStylesheet('blog');

		// resizing of items & load more scripts

		if (/*$this->params->get('item_width', 48) <= 50 || */$this->load_more) {

			HTMLHelper::_('jquery.framework');

			$cache_js = new JSFileCache('com_latestnewsenhancedpro', $this->params);

			if ($this->params->get('inline_scripts', 0)) {

				$wam->addInlineScript($cache_js->getBuffer(true));

			} else {
				$result = $cache_js->cache('script_'.$this->extra_suffix.'.js', $this->clear_header_files_cache);

				if ($result) {
					$wam->registerAndUseScript('script_' . $this->extra_suffix, $cache_js->getCachePath() . '/script_' . $this->extra_suffix . '.js');
				}
			}
		} else {
			// remove style.js if it exists
			if (File::exists(JPATH_SITE . '/media/cache/com_latestnewsenhancedpro/script_'.$this->extra_suffix.'.js')) {
				File::delete(JPATH_SITE . '/media/cache/com_latestnewsenhancedpro/script_'.$this->extra_suffix.'.js');
			}
		}

		// mobile

		$this->isMobile = SYWUtilities::isMobile();

		// Bootstrap compatible alerts
		// not used anymore, keep for backward compatibility
		$this->alert_error_classes = SYWUtilities::getBootstrapProperty('alert alert-error', $this->bootstrap_version);

		// extra parameters

		$this->limitstart_request_value = $this->input->getInt('limitstart', 0);
		$this->limitstart_request = '&limitstart='.$this->limitstart_request_value;

		$this->category_request = '';
		$this->category_request_value = $this->input->getString('category', '');
		if (!empty($this->category_request_value)) {
			$this->category_request = '&category='.$this->category_request_value;
		}
		$this->pagination->setAdditionalUrlParam('category', $this->category_request_value);

		$this->tag_request = '';
		$this->tag_request_value = $this->input->getString('tag', '');
		if (!empty($this->tag_request_value)) {
			$this->tag_request = '&tag='.$this->tag_request_value;
		}
		$this->pagination->setAdditionalUrlParam('tag', $this->tag_request_value);

		$this->author_request = '';
		$this->author_request_value = $this->input->getString('author', '');
		if (!empty($this->author_request_value)) {
			$this->author_request = '&author='.$this->author_request_value;
		}
		$this->pagination->setAdditionalUrlParam('author', $this->author_request_value);
		
		$this->alias_request = '';
		$this->alias_request_value = $this->input->getString('alias', '');
		if (!empty($this->alias_request_value)) {
		    $this->alias_request = '&alias='.$this->alias_request_value;
		}
		$this->pagination->setAdditionalUrlParam('alias', $this->alias_request_value);

		$this->period_request = '';
		$this->period_request_value = $this->input->getString('period', '');
		if (!empty($this->period_request_value)) {
			$this->period_request = '&period='.$this->period_request_value;
		}
		$this->pagination->setAdditionalUrlParam('period', $this->period_request_value);

		// K2 extra fields as filter

		$this->fields_request = '';
		foreach ($this->field_index_array as $field_index) {
			$field_request_value = 'field_' . $field_index . '_value';
			$this->$field_request_value = $this->input->getString('field_' . $field_index, '');

			if ($this->$field_request_value) {
				$this->fields_request .= '&field_' . $field_index. '=' . $this->$field_request_value;
			}

			$this->pagination->setAdditionalUrlParam('field_' . $field_index, $this->$field_request_value);
		}

		$this->search_request = '';
		$this->search_request_value = $this->input->getString('filter-search', '');
		if (!empty($this->search_request_value)) {
			$this->search_request = '&filter-search='.$this->search_request_value;
		}
		$this->pagination->setAdditionalUrlParam('filter-search', $this->search_request_value);

		$this->search_options_request = '';
		$this->search_options_request_value = $this->input->getString('filter-match', 'any');
		if (!empty($this->search_options_request_value)) {
			$this->search_options_request = '&filter-match='.$this->search_options_request_value;
		}
		$this->pagination->setAdditionalUrlParam('filter-match', $this->search_options_request_value);

		$this->order_request = '';
		$this->order_request_value = $this->input->getString('filter_order', '');
		if (!empty($this->order_request_value)) {
		    $this->order_request = '&filter_order='.$this->order_request_value;
		}
		$this->pagination->setAdditionalUrlParam('filter_order', $this->order_request_value);

		$this->dir_request = '';
		$this->dir_request_value = $this->input->getString('filter_order_Dir', '');
		if (!empty($this->dir_request_value)) {
		    $this->dir_request = '&filter_order_Dir='.$this->dir_request_value;
		}
		$this->pagination->setAdditionalUrlParam('filter_order_Dir', $this->dir_request_value);

		$this->print = $this->input->getBool('print', false);
		if ($this->print) { // we are in the popup window
			$wam->registerAndUseStyle('lnep.' . $this->params->get('layout') . '_print', 'com_latestnewsenhancedpro/layouts/' . $this->params->get('layout') . '/print.css', ['relative' => true, 'version' => 'auto'], ['media' => 'print']);
		}

		if ($this->load_more) { // grid view only
		    $ajax_path = Route::_('index.php?option=com_latestnewsenhancedpro&view=k2items&format=raw&limit='.$this->params->get('more_count', 3).$this->category_request.$this->tag_request.$this->author_request.$this->alias_request.$this->period_request.$this->fields_request.$this->search_request.$this->search_options_request.$this->order_request.$this->dir_request, false);
			if (Factory::getConfig()->get('sef_suffix', 0)) {
				//$ajax_path = preg_replace('/.raw/', '', $ajax_path) . '&format=raw';
				$ajax_path .= '&format=raw';
			}
			$wam->addInlineScript('var lnep_ajax_path = "'.$ajax_path.'";');
		}

		// load icon fonts

		if ($this->params->get('load_icon_font', 1)) {
		    SYWFonts::loadIconFont();
		}
		
		if ($this->params->get('load_fontawesome', 0)) {
		    SYWFonts::loadIconFont('fontawesome');
		}

		// load effects library
		if ($show_image && $this->params->get('hover_effect_pic', 'none') != 'none') {
			//SYWStylesheets::load2DTransitions();
			$transition_method = SYWStylesheets::getTransitionMethod('hvr-' . $this->params->get('hover_effect_pic'));
			SYWStylesheets::$transition_method();
		}

		// load high-resolution images library and lazy loading capability
// 		if ($show_image && ($this->params->get('create_highres', false) || $this->params->get('lazyload', false))) {
// 			//HTMLHelper::_('jquery.framework');
// 			SYWLibraries::loadLazysizes($this->load_remotely);
// 		}

		// leading items are allowed in the view
		//$this->possibleLeading = true;

		// load the CSS needed for accessibility
		if ($this->bootstrap_version == 0 || $this->bootstrap_version == 2) {
		    SYWStylesheets::loadAccessibilityVisibilityStyles();
		}

		$this->categories_list = $this->get('CategoriesList');

		// display the view
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('COM_LATESTNEWSENHANCEDPRO_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		// add alternative feed link
		if ($this->params->get('show_feed_link', 1) == 1) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(Route::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(Route::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
	}

}
