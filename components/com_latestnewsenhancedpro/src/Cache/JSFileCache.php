<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Site\Cache;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\HeaderFilesCache;

class JSFileCache extends HeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$lang = Factory::getLanguage();
		$lang->load('com_latestnewsenhancedpro', JPATH_SITE);

		$variables = array();

// 		$view = $params->get('view', 'articles');
// 		$variables[] = 'view';

		$layout = $params->get('layout', 'blog');
		$variables[] = 'layout';

		$suffix = $params->get('css_prefix', '.lnep').'_'.$layout;
		$variables[] = 'suffix';

		$namespace = 'lnep_'.$layout.'_ns';
		$variables[] = 'namespace';

		$item_width = trim($params->get('item_width', 100)); // % : it is always in percentages when the caching occurs
		if ($item_width <= 0 || $item_width > 100) {
			$item_width = 100;
		}
		$variables[] = 'item_width';

		$min_width = trim($params->get('item_minwidth', 0)); // px : there is always a width when the caching occurs
		$variables[] = 'min_width';

		$margin_min_width = 3; // px
		$variables[] = 'margin_min_width';

		$margin_error = 1; // px
		$variables[] = 'margin_error';

		$load_more = $params->get('pag_type', 'std') == 'lm' ? true : false;
		$variables[] = 'load_more';

		$btn_load_more = $params->get('loadmore_type', 'btn') == 'btn' ? true : false;
		$variables[] = 'btn_load_more';

		$more_count = $params->get('more_count', 3);
		$variables[] = 'more_count';

		$no_more_message = $params->get('no_more_message', Text::_('COM_LATESTNEWSENHANCEDPRO_NOMORERECORDS'));
		$variables[] = 'no_more_message';

		$loader_alt = Text::_('COM_LATESTNEWSENHANCEDPRO_LOADER_ALT');
		$variables[] = 'loader_alt';

		$loader_path = Uri::base() . 'media/syw/images/loaders/spin.gif';

		$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
		switch ($config_params->get('lazy_type', 'default')) {
			case 'default':
				$selected_loader = $config_params->get('lazy_default', 'default');
				if ($selected_loader != 'default') {
					$loader_path = Uri::base() . 'media/syw/images/loaders/' . $selected_loader . '.svg';
				}
			break;
			case 'datauri':
				$data_uri = trim($config_params->get('lazy_datauri', ''));
				if ($data_uri) {
					$loader_path = $data_uri;
				}
			break;
			case 'image':
				$selected_image = $config_params->get('lazy_image', '');
				if ($selected_image) {
					$loader_path = $selected_image;
				}
		}

		$variables[] = 'loader_path';

		// set all necessary parameters
		$this->params = compact($variables);
	}

	public function getBuffer($inline = false)
	{
		// get all necessary parameters
		extract($this->params);

		// 		if (function_exists('ob_gzhandler')) { // not tested
		// 			ob_start('ob_gzhandler');
		// 		} else {
		ob_start();
		// 		}

		// set the header
		if (!$inline) {
			//$this->sendHttpHeaders('js');
		}

		echo 'jQuery(document).ready(function ($) { ';

// 			if ($item_width <= 50) {
// 				echo 'var itemlist = $("'.$suffix.' .latestnews-items"); ';

// 				echo 'if (itemlist != null) { ';
// 					echo $namespace.'.resize_news(); ';
// 				echo '} ';

// 				echo '$(window).resize(function() { ';
// 					echo 'if (itemlist != null) { ';
// 						echo $namespace.'.resize_news(); ';
// 					echo '} ';
// 				echo '}); ';
// 			}

//			if ($load_more) {

				//$loader_path = Uri::base().'media/syw/images/ajax-loader.gif';
				//$get_path = JRoute::_('index.php?option=com_latestnewsenhancedpro&view='.$view.'&format=raw&limit='.$more_count, false);

				echo 'var hasData = true; ';
				echo 'var previouseEventComplete = true; ';

				if ($btn_load_more) {

					echo '$("'.$suffix.' #loadmore").on("click", function (event) { ';
                        echo 'event.preventDefault();';
						echo 'if (hasData) { ';
							echo '$("'.$suffix.' .latestnews-items").after(\'<div class="loader"><img src="'.$loader_path.'" alt="'.$loader_alt.'" /></div>\'); ';
							//echo '$.get("'.$get_path.'", function(data) { ';
							echo '$.get(lnep_ajax_path, function(data) { ';
								echo '$("'.$suffix.' .loader").remove(); ';
								echo 'if (data.trim().length != 0) { ';
									echo '$(data).hide().appendTo("'.$suffix.' .latestnews-items").fadeIn(1000); ';
									//echo 'if (typeof '.$namespace.' != "undefined") { ';
										//echo $namespace.'.resize_news(); ';
									//echo '} ';
									echo '$(".hasTooltip").tooltip({"html": true,"container": "body"}); ';
								echo '} else { ';
									echo 'hasData = false; ';
									echo '$("'.$suffix.' .pagination_wrapper.bottom").append(\'<div class="countertotal nomorerecords">\' + "'.$no_more_message.'" + \'</div>\'); ';
									echo '$("'.$suffix.' #loadmore").remove(); ';
								echo '} ';
							echo '}); ';
						echo '} ';
					echo '}); ';

				} else {

					echo '$(window).on("scroll", function() { ';
						echo 'var pagination_bottom = $("'.$suffix.' .pagination_wrapper.bottom"); ';
						echo 'var position_pagination_bottom = pagination_bottom.offset(); ';
						echo 'var offset = $(document).height() - position_pagination_bottom.top; ';
						echo 'if ($(window).scrollTop() + offset + $(window).height() >= $(document).height()) { ';
							echo 'if (hasData && previouseEventComplete) { ';
								echo 'previouseEventComplete = false; ';
								echo '$("'.$suffix.' .latestnews-items").after(\'<div class="loader"><img src="'.$loader_path.'" alt="'.$loader_alt.'" /></div>\'); ';
								//echo '$.get("'.$get_path.'", function(data) { ';
								echo '$.get(lnep_ajax_path, function(data) { ';
									echo '$("'.$suffix.' .loader").remove(); ';
									echo 'if (data.trim().length != 0) { ';
										echo '$(data).hide().appendTo("'.$suffix.' .latestnews-items").fadeIn(1000); ';
										//echo 'if (typeof '.$namespace.' != "undefined") { ';
											//echo $namespace.'.resize_news(); ';
										//echo '} ';
										echo '$(".hasTooltip").tooltip({"html": true,"container": "body"}); ';
									echo '} else { ';
										echo 'hasData = false; ';
										echo '$("'.$suffix.' .pagination_wrapper.bottom").append(\'<div class="countertotal nomorerecords">\' + "'.$no_more_message.'" + \'</div>\'); ';
									echo '} ';
									echo 'previouseEventComplete = true; ';
								echo '}); ';
							echo '} ';
						echo '} ';
					echo '}); ';

				}
//			}

		echo '}); ';

// 		if ($item_width <= 50) {

// 			// need namespaces in order to be able to access resize_news when out of scope

// 			echo '(function($) {';

// 				echo 'var namespace;';

// 				echo 'namespace = {';
// 					echo 'resize_news: function() {';

// 						echo 'var itemlist = $("'.$suffix.' .latestnews-items"); ';
// 						echo 'var item = $("'.$suffix.' .latestnews-item").not(".leading"); ';

// 						echo 'var container_width = itemlist.width(); ';

// 						echo 'var news_per_row = 1; ';

// 						echo 'var news_width = Math.floor(container_width * '.$item_width.' / 100); ';

// 						echo 'if (news_width < '.$min_width.') { ';
// 							echo 'if (container_width < '.$min_width.') { ';
// 								echo 'news_width = container_width; ';
// 							echo '} else { ';
// 								echo 'news_width = '.$min_width.'; ';
// 							echo '} ';
// 						echo '} ';

// 						echo 'if ('.$item_width.' <= 50) { ';
// 							echo 'news_per_row = Math.floor(container_width / news_width); ';

// 							echo 'if (news_per_row == 1) { ';
// 								echo 'news_width = container_width; ';
// 							echo '} else { ';
// 								echo 'news_width = Math.floor(container_width / news_per_row) - ('.$margin_min_width.' * news_per_row); ';
// 							echo '} ';

// 						echo '} else { '; // we can never have 2 items on the same row
// 							echo 'news_width = container_width; ';
// 						echo '} ';

// 						//echo 'var left_for_margins = container_width - (news_per_row * news_width); ';
// 						//echo 'var margin_width = Math.floor(left_for_margins / (news_per_row * 2)) - '.$margin_error.'; ';

// 						echo 'item.each(function() { ';
// 							echo '$(this).width(news_width + "px"); ';
// 							//echo '$(this).css("margin-left", margin_width + "px"); ';
// 							//echo '$(this).css("margin-right", margin_width + "px"); ';
// 						echo '}); ';
// 					echo '} ';
// 				echo '};';

// 				echo 'window.'.$namespace.' = namespace;';

// 			echo '})(jQuery);';
// 		}

		return ob_get_clean();
	}

}
