<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Cache;

defined('_JEXEC') or die;

use SYW\Library\HeaderFilesCache;

class CSSFileCache extends HeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$view = $params->get('view', 'article');

		// body parameters

		$font_details = $params->get('d_fs', 80);  // TODO make it $font_size_details
		$variables[] = 'font_details';

		$details_line_spacing = $params->get('details_line_spacing', array('', 'px'));
		$variables[] = 'details_line_spacing';

		$details_font_color = trim($params->get('details_color', '#000000'));
		$variables[] = 'details_font_color';

		$iconfont_color = trim($params->get('iconscolor', '#000000'));
		$variables[] = 'iconfont_color';

		// rating

		$star_color = trim($params->get('star_color', '#000000'));
		$variables[] = 'star_color';

		// share

		$share_color_type = $params->get('share_color', 'none');
		$share_color = false;
		$share_bgcolor = false;
		if ($share_color_type == 'bg') {
			$share_bgcolor = true;
		} else if ($share_color_type == 'icon') {
			$share_color = true;
		}
		$variables[] = 'share_color';
		$variables[] = 'share_bgcolor';

		$share_radius = $params->get('share_r', 0);
		if ($share_radius < 0) {
			$share_radius = 0;
		}
		if ($share_radius > 20) {
			$share_radius = 20;
		}
		$variables[] = 'share_radius';

		// social networks

		$social_networks = $params->get('social_networks', array());
		$variables[] = 'social_networks';

		// head type, width and height

		$head_width = 0;
		$head_height = 0;
		$head_type = 'none';
		$footer_head_width = 0;
		$footer_head_height = 0;
		$footer_head_type = 'none';
		if ($view == 'article') {
			$head_type = $params->get('head_type', 'none');
			$footer_head_type = $params->get('footer_head_type', 'none');
		} else {
			$head_type = $params->get('lists_head_type', 'none');
		}

		// vertical align

		if ($view == 'article') {
			$vertical_align = $params->get('vertical_align', 'top');
		} else {
			$vertical_align = $params->get('lists_vertical_align', 'top');
		}
		$footer_vertical_align = $params->get('footer_vertical_align', 'top');

		$variables[] = 'vertical_align';
		$variables[] = 'footer_vertical_align';

		// align details

		$align_details = 'left';
		$footer_align_details = 'left';
		if ($view == 'article') {
			$align_details = $params->get('align_details', 'left');
			$footer_align_details = $params->get('footer_align_details', 'left');
		} else {
			$align_details = $params->get('lists_align_details', 'left');
		}
		$variables[] = 'align_details';
		$variables[] = 'footer_align_details';

		// image header

		$image_header = false;

		$image_types = array('contact', 'gravatar');

		if (in_array($head_type, $image_types) || substr($head_type, 0, strlen('jfield:media')) === 'jfield:media'|| substr($head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {

			if ($view == 'article') {
				$head_width = $params->get('head_w', 64);
				$head_height = $params->get('head_h', 80);
			} else {
				$head_width = $params->get('lists_head_w', 64);
				$head_height = $params->get('lists_head_h', 80);
			}

			$pic_bgcolor = trim($params->get('bgcolor_pic', '')) != '' ? trim($params->get('bgcolor_pic')) : 'transparent';
			$variables[] = 'pic_bgcolor';

			$pic_shadow_width = $params->get('sh_w_pic', 0);
			$variables[] = 'pic_shadow_width';

			$pic_border_width = $params->get('border_w_pic', 0);
			$variables[] = 'pic_border_width';

			$pic_border_radius = $params->get('border_r_pic', 0);
			$variables[] = 'pic_border_radius';

			$pic_border_radius_img = $pic_border_radius;
			if ($pic_border_width > 0 && $pic_border_radius >= $pic_border_width) {
				$pic_border_radius_img -= $pic_border_width;
			}
			$variables[] = 'pic_border_radius_img';

			$pic_border_color = trim($params->get('border_c_pic', '#FFFFFF'));
			$variables[] = 'pic_border_color';

			$head_width = $head_width - $pic_border_width * 2;
			$head_height = $head_height - $pic_border_width * 2;

			$image_header = true;
		}
		$variables[] = 'image_header';

		$filter = $params->get('filter', 'none');
		if (strpos($filter, '_css') !== false) {
		    $filter = str_replace('_css', '', $filter);
		    $variables[] = 'filter';
		}

		// image footer

		$image_footer = false;

		$image_types = array('contact', 'gravatar');

		if (in_array($footer_head_type, $image_types) || substr($footer_head_type, 0, strlen('jfield:media')) === 'jfield:media'|| substr($footer_head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {

			$footer_head_width = $params->get('footer_head_w', 64);
			$footer_head_height = $params->get('footer_head_h', 80);

			$footer_pic_bgcolor = trim($params->get('footer_bgcolor_pic', '')) != '' ? trim($params->get('footer_bgcolor_pic')) : 'transparent';
			$variables[] = 'footer_pic_bgcolor';

			$footer_pic_shadow_width = $params->get('footer_sh_w_pic', 0);
			$variables[] = 'footer_pic_shadow_width';

			$footer_pic_border_width = $params->get('footer_border_w_pic', 0);
			$variables[] = 'footer_pic_border_width';

			$footer_pic_border_radius = $params->get('footer_border_r_pic', 0);
			$variables[] = 'footer_pic_border_radius';

			$footer_pic_border_radius_img = $footer_pic_border_radius;
			if ($footer_pic_border_width > 0 && $footer_pic_border_radius >= $footer_pic_border_width) {
				$footer_pic_border_radius_img -= $footer_pic_border_width;
			}
			$variables[] = 'footer_pic_border_radius_img';

			$footer_pic_border_color = trim($params->get('footer_border_c_pic', '#FFFFFF'));
			$variables[] = 'footer_pic_border_color';

			$footer_head_width = $footer_head_width - $footer_pic_border_width * 2;
			$footer_head_height = $footer_head_height - $footer_pic_border_width * 2;

			$image_footer = true;
		}
		$variables[] = 'image_footer';

		$footer_filter = $params->get('footer_filter', 'none');
		if (strpos($footer_filter, '_css') !== false) {
		    $footer_filter = str_replace('_css', '', $footer_filter);
		    $variables[] = 'footer_filter';
		}

		// calendar

		$calendar = '';
		if ($head_type == 'calendar' || substr($head_type, 0, strlen('jfield:calendar')) === 'jfield:calendar') {

			if ($view == 'article') {
				$head_width = $params->get('head_w', 64);
				$head_height = $params->get('head_h', 80); // uncommented
			} else {
				$head_width = $params->get('lists_head_w', 64);
				$head_height = $params->get('lists_head_h', 80); // uncommented
			}

			$color = trim($params->get('c1', '#3D3D3D'));
			$variables[] = 'color';
			$bgcolor1 = trim($params->get('bgc11', '')) != '' ? trim($params->get('bgc11')) : 'transparent';
			$variables[] = 'bgcolor1';
			$bgcolor2 = trim($params->get('bgc12', '')) != '' ? trim($params->get('bgc12')) : 'transparent';
			$variables[] = 'bgcolor2';

			$color_top = trim($params->get('c2', '#494949'));
			$variables[] = 'color_top';
			$bgcolor1_top = trim($params->get('bgc21', '')) != '' ? trim($params->get('bgc21')) : 'transparent';
			$variables[] = 'bgcolor1_top';
			$bgcolor2_top = trim($params->get('bgc22', '')) != '' ? trim($params->get('bgc22')) : 'transparent';
			$variables[] = 'bgcolor2_top';

			$color_bottom = trim($params->get('c3', '#494949'));
			$variables[] = 'color_bottom';
			$bgcolor1_bottom = trim($params->get('bgc31', '')) != '' ? trim($params->get('bgc31')) : 'transparent';
			$variables[] = 'bgcolor1_bottom';
			$bgcolor2_bottom = trim($params->get('bgc32', '')) != '' ? trim($params->get('bgc32')) : 'transparent';
			$variables[] = 'bgcolor2_bottom';

			$cal_shadow_width = $params->get('sh_w', 0);
			$variables[] = 'cal_shadow_width';
			$cal_border_width = $params->get('border_w', 0);
			$variables[] = 'cal_border_width';
			$cal_border_radius = $params->get('border_r', 0);
			$variables[] = 'cal_border_radius';
			$cal_border_color = trim($params->get('border_c', '#000000'));
			$variables[] = 'cal_border_color';

			$font_ref_cal = $params->get('f_r', 14);
			$variables[] = 'font_ref_cal';
			$font_ratio = 1; // floatval($head_height) / 80; // 1em base for a height of 80px
			$variables[] = 'font_ratio';

			$calendar = $params->get('cal_style', 'original');
		}
		$variables[] = 'calendar';

		// icon

		$icon = false;
		if (substr($head_type, 0, strlen('jfield:sywicon')) === 'jfield:sywicon') {

			if ($view == 'article') {
				$head_width = $params->get('head_w', 64);
				$head_height = $params->get('head_h', 80);
			} else {
				$head_width = $params->get('lists_head_w', 64);
				$head_height = $params->get('lists_head_h', 80);
			}

			$icon_bgcolor = trim($params->get('icon_bgcolor', '')) != '' ? trim($params->get('icon_bgcolor')) : 'transparent';
			$variables[] = 'icon_bgcolor';

			$icon_color = trim($params->get('icon_color', '#000000'));
			$variables[] = 'icon_color';

			$icon_shadow_width = $params->get('sh_w_icon', 0);
			$variables[] = 'icon_shadow_width';

			$icon_text_shadow_width = $params->get('sh_w_text_icon', 0);
			$variables[] = 'icon_text_shadow_width';

			$icon_border_width = $params->get('border_w_icon', 0);
			$variables[] = 'icon_border_width';

			$icon_border_color = trim($params->get('border_c_icon', '#FFFFFF'));
			$variables[] = 'icon_border_color';

			$icon_border_radius = $params->get('border_r_icon', 0);
			$variables[] = 'icon_border_radius';

			$icon_padding = $params->get('padding_icon', 0);
			$variables[] = 'icon_padding';

			$icon = true;
		}
		$variables[] = 'icon';

		// head width and height

		$variables[] = 'head_width';
		$variables[] = 'head_height';

		$variables[] = 'footer_head_width';
		$variables[] = 'footer_head_height';

		// mobile

		$breakpoint = $params->get('breakpoint', '640');
		$variables[] = 'breakpoint';

		$center_image = false;
		$lists_center_image = false;
		if ($view == 'article') {
			$center_image = $params->get('center_image', 0);
		} else {
			$lists_center_image = $params->get('lists_center_image', 0);
		}
		$variables[] = 'center_image';
		$variables[] = 'lists_center_image';

		$fixed_share = $params->get('fixed_share', 0);
		$variables[] = 'fixed_share';

		// set all necessary parameters
		$this->params = compact($variables);
	}

	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);

// 		if (function_exists('ob_gzhandler')) { // TODO not tested
// 			ob_start('ob_gzhandler');
// 		} else {
 			ob_start();
// 		}

		// set the header
		//$this->sendHttpHeaders('css');

		include JPATH_ROOT . '/media/plg_content_articledetailsprofiles/styles/style.css.php';
		if ($calendar) {
			include JPATH_ROOT . '/media/plg_content_articledetailsprofiles/styles/calendars/'.$calendar.'/style.css.php';
		}

		// social networks
		if (!empty($social_networks) && is_object($social_networks)) {

			$default_colors = array('facebook' => '#43609c', 'twitter' => '#02b0e8', 'linkedin' => '#0077b6', 'sendtofriend' => '#8d6e63', 'mix' => '#fd8235', 'telegram' => '#27a7e5', 'whatsapp' => '#00e676', 'pinterest' => '#e60023');

			foreach ($social_networks as $social_network) {
				if ($social_network->social_network != 'none') {

					$social_network_class = $social_network->social_network;

					if ($social_network->social_network == 'email') {
						$social_network_class = 'sendtofriend';
					} else {
						if (trim($social_network->other_network) != '') {
							$social_network_class = trim(strtolower($social_network->other_network));
						}
					}

					$color = isset($default_colors[$social_network_class]) ? $default_colors[$social_network_class] : '';
					if (trim($social_network->network_color) != '' && $social_network->network_color != 'transparent') {
						$color = trim($social_network->network_color);
						if (strpos($color, '#') === FALSE) {
							$color = '#' . $color;
						}
					}

					if ($share_bgcolor && $color) {
						echo '.articledetails .info .details .detail_social a.' . $social_network_class . ' i,';
						echo '.articledetails.mobile .info .details .detail_social a.inline_svg.' . $social_network_class . ',';
						echo '.articledetails .info .details .detail_social a.inline_svg.' . $social_network_class . ' .svg_container {';
							echo 'background-color: ' . $color . ';';
						echo '}';
					}

					if ($share_color && $color) {
						echo '.articledetails .info .details .detail_social a.' . $social_network_class . ' i,';
						echo '.articledetails .info .details .detail_social a.inline_svg.' . $social_network_class . ' svg {';
							echo 'color: ' . $color . ';';
						echo '}';
					}
				}
			}
		}

		// image CSS filters

		if (isset($filter)) {
		    switch($filter) {
		        case 'sepia': echo '.articledetails-header .picture img { -webkit-filter: sepia(100%); filter: sepia(100%); }'; break;
		        case 'grayscale': echo '.articledetails-header .picture img { -webkit-filter: grayscale(100%); filter: grayscale(100%); }'; break;
		        case 'negate': echo '.articledetails-header .picture img { -webkit-filter: invert(100%); filter: invert(100%); }';
		    }
		}

		if (isset($footer_filter)) {
		    switch($footer_filter) {
		        case 'sepia': echo '.articledetails-footer .picture img { -webkit-filter: sepia(100%); filter: sepia(100%); }'; break;
		        case 'grayscale': echo '.articledetails-footer .picture img { -webkit-filter: grayscale(100%); filter: grayscale(100%); }'; break;
		        case 'negate': echo '.articledetails-footer .picture img { -webkit-filter: invert(100%); filter: invert(100%); }';
		    }
		}

		return $this->compress(ob_get_clean());
	}

}