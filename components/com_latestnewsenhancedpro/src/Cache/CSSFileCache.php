<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Site\Cache;

defined('_JEXEC') or die;

use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\HeaderFilesCache;

class CSSFileCache extends HeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

// 		$view = $params->get('view', 'articles');
// 		$variables[] = 'view';

		$layout = $params->get('layout', 'blog');
		$variables[] = 'layout';

		$suffix = $params->get('css_prefix', '.lnep').'_'.$layout.' form'; // use 'form' to isolate the module styles from the view when the modules are present in after or before articles
		$variables[] = 'suffix';

		$true_suffix = $params->get('css_prefix', '.lnep').'_'.$layout;
		$variables[] = 'true_suffix';

		$items_align = $params->get('items_align', 'sa');
		$variables[] = 'items_align';

		$items_valign = $params->get('items_valign', 'fs');
		$variables[] = 'items_valign';

		if ($layout == 'list') {
		    $table_layout = $params->get('table_layout', 'auto');
		    $variables[] = 'table_layout';

		    $min_row_width = $params->get('min_row_w', '640');
		    $variables[] = 'min_row_width';

		    $header_width = $params->get('header_w', '80');
		    $variables[] = 'header_width';

		    $heading_bgcolor = trim($params->get('thbgcolor', '')) != '' ? trim($params->get('thbgcolor')) : 'transparent';
		    $variables[] = 'heading_bgcolor';

		    $heading_color = trim($params->get('thcolor', ''));
		    if ($heading_color == 'transparent') {
		        $heading_color = '';
		    }
		    $variables[] = 'heading_color';
		}

		// leading items

		$leading_item_width = $params->get('leading_item_width', 100);
		$variables[] = 'leading_item_width';

		$leading_item_minwidth = $params->get('leading_item_minwidth', 400);
		$variables[] = 'leading_item_minwidth';

		$leading_item_maxwidth = $params->get('leading_item_maxwidth', '');
		$variables[] = 'leading_item_maxwidth';

		$leading_head_width = $params->get('leading_head_w', 128);
		$variables[] = 'leading_head_width';

		$leading_head_height = $params->get('leading_head_h', 128);
		$variables[] = 'leading_head_height';

// 		$leading_maintain_height = $params->get('leading_maintain_height', 0);
// 		$variables[] = 'leading_maintain_height';

		$leading_item_space_between = $params->get('leading_item_space_between', 0);
		$variables[] = 'leading_item_space_between';

		$leading_item_space_after = $params->get('leading_item_space_after', 0);
		$variables[] = 'leading_item_space_after';

		// items

		$item_width = $params->get('item_width', 48);
		$variables[] = 'item_width';

		$item_minwidth = $params->get('item_minwidth', 200);
		$variables[] = 'item_minwidth';

		$item_maxwidth = $params->get('item_maxwidth', '');
		$variables[] = 'item_maxwidth';

		$head_width = $params->get('head_w', 64);
		$variables[] = 'head_width';

		$head_height = $params->get('head_h', 64);
		$variables[] = 'head_height';

// 		$maintain_height = $params->get('maintain_height', 0);
// 		$variables[] = 'maintain_height';

		$item_space_between = $params->get('item_space_between', 0);
		$variables[] = 'item_space_between';

		$item_space_after = $params->get('item_space_after', 0);
		$variables[] = 'item_space_after';

		// theme

		$theme = $params->get('theme_style', 'original');
		$variables[] = 'theme';

		$colortheme = $params->get('color_theme', '');
		$variables[] = 'colortheme';

		// body parameters

		$bgcolor_body = trim($params->get('bgcolor', '')) != '' ? trim($params->get('bgcolor')) : 'transparent';
		$variables[] = 'bgcolor_body';

		$border_width_body = $params->get('item_border_w', 0);
		$variables[] = 'border_width_body';

		$border_color_body = trim($params->get('item_border_c', ''));
		$variables[] = 'border_color_body';

		$border_radius_body = $params->get('item_border_r', 0);
		$variables[] = 'border_radius_body';

		$shadow_body = $params->get('item_shadow', 'none');
		$variables[] = 'shadow_body';

		$padding_body = $params->get('d_to_b', 0);
		$variables[] = 'padding_body';

		$padding_head = trim($params->get('space_head', ''));
		if ($padding_head !== '' && is_numeric($padding_head)) {
		    $padding_head = intval($padding_head); // make sure we have values
		}
		$variables[] = 'padding_head';

		$padding_info = trim($params->get('space_body', ''));
		if ($padding_info !== '' && is_numeric($padding_info)) {
		    $padding_info = intval($padding_info); // make sure we have values
		}
		$variables[] = 'padding_info';

		$leading_content_align = $params->get('leading_content_align', 'default');
		if ($leading_content_align == 'default') {
			$leading_content_align = '';
		}
		$variables[] = 'leading_content_align';

		$content_align = $params->get('content_align', 'default');
		if ($content_align == 'default') {
			$content_align = '';
		}
		$variables[] = 'content_align';

		$font_color_body = trim($params->get('item_color', ''));
		if ($font_color_body == 'transparent') {
			$font_color_body = '';
		}
		$variables[] = 'font_color_body';

		$link_color_body = trim($params->get('item_l_color', ''));
		if ($link_color_body == 'transparent') {
			$link_color_body = '';
		}
		$variables[] = 'link_color_body';

		$link_color_hover_body = trim($params->get('item_l_color_h', ''));
		if ($link_color_hover_body == 'transparent') {
			$link_color_hover_body = '';
		}
		$variables[] = 'link_color_hover_body';

		$leading_font_size_reference = $params->get('leading_f_r_body', 100);
		if ($leading_font_size_reference == '') {
		    $leading_font_size_reference = 100;
		}
		$variables[] = 'leading_font_size_reference';

		$font_size_reference = $params->get('f_r_body', 90);
		if ($font_size_reference == '') {
		    $font_size_reference = 90;
		}
		$variables[] = 'font_size_reference';

		$leading_force_title_one_line = $params->get('leading_force_one_line', 0);
		$variables[] = 'leading_force_title_one_line';

		$force_title_one_line = $params->get('force_one_line', 0);
		$variables[] = 'force_title_one_line';

		$leading_wrap = $params->get('leading_wrap', 0);
		$variables[] = 'leading_wrap';

		$wrap = $params->get('wrap', 0);
		$variables[] = 'wrap';

		// extra details

		$font_size_details = $params->get('details_fontsize', 80);
		$variables[] = 'font_size_details';

		$details_line_spacing = $params->get('details_line_spacing', array('', 'px'));
		$variables[] = 'details_line_spacing';

		$details_font_color = trim($params->get('details_color', ''));
		if ($details_font_color == 'transparent') {
			$details_font_color = '';
		}
		$variables[] = 'details_font_color';

		$details_font_color_overhead = trim($params->get('details_color_overhead', ''));
		if ($details_font_color_overhead == 'transparent') {
			$details_font_color_overhead = '';
		}
		$variables[] = 'details_font_color_overhead';

		$iconfont_color = trim($params->get('iconscolor', ''));
		if ($iconfont_color == 'transparent') {
			$iconfont_color = '';
		}
		$variables[] = 'iconfont_color';

		$iconfont_color_overhead = trim($params->get('iconscolor_overhead', ''));
		if ($iconfont_color_overhead == 'transparent') {
			$iconfont_color_overhead = '';
		}
		$variables[] = 'iconfont_color_overhead';

		$over_head_contrast = $params->get('up_contrast', 0);
		$variables[] = 'over_head_contrast';

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

		$share_size = $params->get('share_size', array('', 'px'));
		$variables[] = 'share_size';

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

		// head

		$leading_head_align = $params->get('leading_head_align', 'default');

		if ($leading_head_align == 'default' || in_array($params->get('leading_text_align', 'r'), array('l', 'r', 'lr', 'rl'))) {
			$leading_head_align = '';
		}
		$variables[] = 'leading_head_align';

		$head_align = $params->get('head_align', 'default');

		if ($head_align == 'default' || in_array($params->get('text_align', 'r'), array('l', 'r', 'lr', 'rl'))) {
			$head_align = '';
		}
		$variables[] = 'head_align';

		// image

		$image_types = array('image', 'imageintro', 'imagefull', 'authorcontact', 'authork2user', 'allimagesasc', 'allimagesdesc', 'categoryimage');

		$leading_image = false;
		if (in_array($params->get('leading_head_type', 'none'), $image_types) || substr($params->get('leading_head_type', 'none'), 0, strlen('jfield:media')) === 'jfield:media' || substr($params->get('leading_head_type', 'none'), 0, strlen('jfieldusers:media')) === 'jfieldusers:media' || substr($params->get('leading_head_type', 'none'), 0, strlen('k2field:image')) === 'k2field:image') {
			$leading_image = true;
		}
		$variables[] = 'leading_image';

		$image = false;
		if (in_array($params->get('head_type', 'none'), $image_types) || substr($params->get('head_type', 'none'), 0, strlen('jfield:media')) === 'jfield:media' || substr($params->get('head_type', 'none'), 0, strlen('jfieldusers:media')) === 'jfieldusers:media' || substr($params->get('head_type', 'none'), 0, strlen('k2field:image')) === 'k2field:image') {
			$image = true;
		}
		$variables[] = 'image';

		//if ($leading_image || $image) {
		// make sure the variables are available in case no head is selected but the theme is image-only

			$bgcolor = trim($params->get('imagebgcolor', '')) != '' ? trim($params->get('imagebgcolor')) : 'transparent';
			$variables[] = 'bgcolor';

			$pic_shadow_width = $params->get('sh_w_pic', 0);
			$variables[] = 'pic_shadow_width';

			$pic_border_width = $params->get('border_w', 0);
			$variables[] = 'pic_border_width';

			$pic_border_radius = $params->get('border_r_pic', 0);
			$variables[] = 'pic_border_radius';

			$pic_border_color = trim($params->get('border_c_pic', '#FFFFFF'));
			$variables[] = 'pic_border_color';
		//}

		$filter = $params->get('filter', 'none');
		if (!$params->get('create_thumb', 1)) {
			$filter = $params->get('filter_original', 'none');
		}

		if (strpos($filter, '_css') !== false) {
			$filter = str_replace('_css', '', $filter);
			$variables[] = 'filter';
		}

		// calendar

		$leading_calendar = false;
		if ($params->get('leading_head_type', 'none') == 'calendar' || substr($params->get('leading_head_type', 'none'), 0, strlen('jfield:calendar')) === 'jfield:calendar' || substr($params->get('leading_head_type', 'none'), 0, strlen('k2field:date')) === 'k2field:date') {
			$leading_calendar = true;
		}
		$variables[] = 'leading_calendar';

		$calendar = false;
		if ($params->get('head_type', 'none') == 'calendar' || substr($params->get('head_type', 'none'), 0, strlen('jfield:calendar')) === 'jfield:calendar' || substr($params->get('head_type', 'none'), 0, strlen('k2field:date')) === 'k2field:date') {
			$calendar = true;
		}
		$variables[] = 'calendar';

		$calendar_style = '';
		if ($leading_calendar || $calendar) {

			$calendar_style = $params->get('cal_style', 'original');

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
			$cal_border_width = $params->get('border_w_cal', 0);
			$variables[] = 'cal_border_width';
			$cal_border_radius = $params->get('border_r', 0);
			$variables[] = 'cal_border_radius';
			$cal_border_color = trim($params->get('border_c_cal', '#000000'));
			$variables[] = 'cal_border_color';

			$font_ref_cal = $params->get('f_r', 14);
			$variables[] = 'font_ref_cal';
			$font_ratio = 1; // floatval($head_height) / 80; // 1em base for a height of 80px
			$variables[] = 'font_ratio';
		}
		$variables[] = 'calendar_style';

		// icon

		$leading_icon = false;
		if (substr($params->get('leading_head_type', 'none'), 0, strlen('jfield:sywicon')) === 'jfield:sywicon') {
			$leading_icon = true;
		}
		$variables[] = 'leading_icon';

		$icon = false;
		if (substr($params->get('head_type', 'none'), 0, strlen('jfield:sywicon')) === 'jfield:sywicon') {
			$icon = true;
		}
		$variables[] = 'icon';

		if ($leading_icon || $icon) {

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
		}

		// video

		$leading_video = false;
		if (Helper::isHeadVideo($params->get('leading_head_type', 'none')) !== false) {
		    $leading_video = true;
		}
		$variables[] = 'leading_video';

		$video = false;
		if (Helper::isHeadVideo($params->get('head_type', 'none')) !== false) {
		    $video = true;
		}
		$variables[] = 'video';

		if ($leading_video || $video) {

		    $video_bgimage = $params->get('video_bgimage', '');
		    $variables[] = 'video_bgimage';

		    $video_bgcolor = trim($params->get('video_bgcolor', '')) != '' ? trim($params->get('video_bgcolor')) : 'transparent';
		    $variables[] = 'video_bgcolor';

		    $video_shadow_width = $params->get('sh_w_video', 0);
		    $variables[] = 'video_shadow_width';

		    $video_border_width = $params->get('border_w_video', 0);
		    $variables[] = 'video_border_width';

		    $video_border_color = trim($params->get('border_c_video', '#FFFFFF'));
		    $variables[] = 'video_border_color';

		    $video_border_radius = $params->get('border_r_video', 0);
		    $variables[] = 'video_border_radius';

		    $video_ratio = '';
		    if ($params->get('force_ratio', 0)) {
		        $video_ratio = (float)$head_height / (float)$head_width * 100.0;
		    }
		    $variables[] = 'video_ratio';

		    $video = true;
		}

		// set all necessary parameters
		$this->params = compact($variables);
	}

	protected function getBuffer()
	{
		// get all necessary parameters
		extract($this->params);

		ob_start();

		// set the header
		//$this->sendHttpHeaders('css');

		if ($colortheme) {
			include JPATH_ROOT . '/media/mod_latestnewsenhanced/styles/colors/'.$colortheme.'/style.css.php';
		}
		include JPATH_ROOT . '/media/com_latestnewsenhancedpro/styles/style.css.php';
		include JPATH_ROOT . '/media/com_latestnewsenhancedpro/styles/layouts/'.$layout.'/style.css.php';
		if ($theme !== 'none') {
		    include JPATH_ROOT . '/media/com_latestnewsenhancedpro/styles/themes/'.$theme.'/style.css.php';
		}
		if ($calendar_style) {
			include JPATH_ROOT . '/media/mod_latestnewsenhanced/styles/calendars/'.$calendar_style.'/style.css.php';
		}

		// social networks

		if (!empty($social_networks) && is_array($social_networks)) {

			$default_colors = array('facebook' => '#43609c', 'twitter' => '#02b0e8', 'linkedin' => '#0077b6', 'sendtofriend' => '#8d6e63', 'mix' => '#fd8235', 'telegram' => '#27a7e5', 'whatsapp' => '#00e676', 'pinterest' => '#e60023');

			foreach ($social_networks as $social_network) {

				$social_network = (object) $social_network;

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
						echo $suffix . ' .detail_social a.' . $social_network_class . ' i,';
						echo $suffix . ' .detail_social a.inline_svg.' . $social_network_class . ' .svg_container {';
						echo 'background-color: ' . $color . ';';
						echo '}';
					}

					if ($share_color && $color) {
						echo $suffix . ' .detail_social a.' . $social_network_class . ' i,';
						echo $suffix . ' .detail_social a.inline_svg.' . $social_network_class . ' svg {';
						echo 'color: ' . $color . ';';
						echo '}';
					}
				}
			}
		}

		// image CSS filters

		if (isset($filter)) {
			switch($filter) {
				case 'sepia': echo $suffix . ' .newshead .picture img { -webkit-filter: sepia(100%); filter: sepia(100%); }'; break;
				case 'grayscale': echo $suffix . ' .newshead .picture img { -webkit-filter: grayscale(100%); filter: grayscale(100%); }'; break;
				case 'negate': echo $suffix . ' .newshead .picture img { -webkit-filter: invert(100%); filter: invert(100%); }';
			}
		}

		return $this->compress(ob_get_clean());
	}

}