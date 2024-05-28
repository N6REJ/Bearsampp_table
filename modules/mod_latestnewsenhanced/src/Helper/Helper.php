<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Module\LatestNewsEnhanced\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use SYW\Library\Image as SYWImage;
use SYW\Library\Fields as SYWFields;
use SYW\Library\K2 as SYWK2;
use SYW\Library\Libraries as SYWLibraries;
use SYW\Library\Utilities as SYWUtilities;

/**
 * Class Helper
 */
class Helper
{
	protected static $config_params;

	protected static $video_types_fields = null;

	protected static $image_extension_types = array('png', 'jpg', 'gif', 'jpeg', 'webp', 'avif', 'svg');

	/**
	 * Look for images in content
	 *
	 * @param string $introtext
	 * @param string $fulltext
	 *
	 * @return string|null the image source if found one, null otherwise
	 */
	static function getImageSrcFromContent($introtext, $fulltext = '')
	{
		preg_match_all('#<img[^>]*>#iU', $introtext, $img_result); // finds all images in the introtext
		if (empty($img_result[0][0]) && !empty($fulltext)) {	// maybe there are images in the fulltext...
			preg_match_all('#<img[^>]*>#iU', $fulltext, $img_result); // finds all images in the fulltext
		}

		// TODO: if image too small, discard it (like a dot for empty space)

// 		var_dump($img_result);
// 		foreach ($img_result[0] as $img_result) {

// 			preg_match('/(src)=("[^"]*")/i', $img_result, $src_result); // get the src attribute

// 			$imagesize = getimagesize(trim($src_result[2], '"')); // needs allow_url_fopen for http images and open_ssl for https images
// 			if ($imagesize[0] > 10 && $imagesize[1] > 10) {
// 				return trim($src_result[2], '"');
// 			}

// 		}

		if (!empty($img_result[0][0])) { // $img_result[0][0] is the first image found
			preg_match('/(src)=("[^"]*")/i', $img_result[0][0], $src_result); // get the src attribute
			return trim($src_result[2], '"');
		}

		return null;
	}

	/**
	* Create the thumbnail(s), if possible
	*
	* @param string $module_id
	* @param string $item_id
	* @param string $imagesrc
	* @param string $tmp_path
	* @param integer $head_width
	* @param integer $head_height
	* @param boolean $crop_picture
	* @param array $image_quality_array
	* @param string $filter
	* @param boolean $create_high_resolution
	* @param boolean $allow_remote
	* @param string $thumbnail_mime_type
	*
	* @return array the original image path if errors before thumbnail creation
	*  or no thumbnail path if errors during thumbnail creation
	*  or thumbnail path if no error
	*/
	static function getImageFromSrc($module_id, $item_id, $imagesrc, $tmp_path, $head_width, $head_height, $crop_picture, $image_quality_array, $filter, $create_high_resolution = false, $allow_remote = true, $thumbnail_mime_type = '')
	{
		$result = [];

		if ($head_width == 0 || $head_height == 0) {
			// keep original image

			return [
			    'url' => $imagesrc,
			    'error' => Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_INFO_USINGORIGINALIMAGE'), // necessary to specify thumbnail creation failed
			];
		}

		if (!extension_loaded('gd') && !extension_loaded('imagick')) {
			// missing image library
			
		    return [
		        'url' => $imagesrc,
		        'error' => Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_WARNING_NOIMAGELIBRARYLOADED'),
		    ];
		}

		$original_imagesrc = $imagesrc;

		// there may be extra info in the path
		// example: http://www.tada.com/image.jpg?x=3
		// thubmnails cannot be created if ? in the path

		$url_array = explode("?", $imagesrc);
		$imagesrc = $url_array[0];

		$imageext = strtolower(File::getExt($imagesrc));
		$original_imageext = $imageext;

		if (!in_array($imageext, self::$image_extension_types)) {

			// case where image is a URL with no extension (generated image)
			// example: http://argos.scene7.com/is/image/Argos/7491801_R_Z001A_UC1266013?$TMB$&wid=312&hei=312
			// thubmnails cannot be created from generated images external paths
			// or image has another file type like .tiff
			
		    return [
		        'url' => $original_imagesrc,
		        'error' => Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_UNSUPPORTEDFILETYPE', $original_imagesrc),
		    ];
		}
		
		// Special case with SVG: no creation of thumbnails
		if ($imageext === 'svg') {
		    return [
		        'url' => $original_imagesrc, 
		        'error' => '',
		    ];
		}

		// URL works only if 'allow url fopen' is 'on', which is a security concern
		// retricts images to the ones found on the site, external URLs are not allowed (for security purposes)
		if (substr_count($imagesrc, 'http') <= 0) { // if the image is internal
			if (substr($imagesrc, 0, 1) == '/') {
				// take the slash off
				$imagesrc = ltrim($imagesrc, '/');
			}
		} else {
			$base = Uri::base(); // Uri::base() is http://www.mysite.com/subpath/
			$imagesrc = str_ireplace($base, '', $imagesrc);
		}

		// we end up with all $imagesrc paths as 'images/...'
		// if not, the URL was from an external site

		if (substr_count($imagesrc, 'http') > 0) {
			// we have an external URL
			if (/*!ini_get('allow_url_fopen') || */!$allow_remote) {
				return [
				    'url' => $original_imagesrc,
				    'error' => Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_EXTERNALURLNOTALLOWED', $imagesrc),
				];
			}
		}

		switch ($thumbnail_mime_type) {
			case 'image/jpg': $imageext = 'jpg'; break;
			case 'image/png': $imageext = 'png'; break;
			case 'image/webp': $imageext = 'webp'; break;
			case 'image/avif': $imageext = 'avif';
		}

		$filename = $tmp_path . '/thumb_' . $module_id . '_' . $item_id . '.' . $imageext;

		// create the thumbnail

		$image = new SYWImage($imagesrc);

		if (is_null($image->getImagePath())) {
			$result['error'] = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_IMAGEFILEDOESNOTEXIST', $imagesrc);
		} else if (is_null($image->getImageMimeType())) {
			$result['error'] = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_UNABLETOGETIMAGEPROPERTIES', $imagesrc);
		} else if (is_null($image->getImage()) || $image->getImageWidth() == 0) {
			$result['error'] = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_UNSUPPORTEDFILETYPE', $imagesrc);
		} else {

			$quality = self::getImageQualityFromExt($imageext, $image_quality_array);

			// negative values force the creation of the thumbnails with size of original image
			// great to create high-res of original image and/or to use quality parameters to create an image with smaller file size
			if ($head_width < 0 || $head_height < 0) {
				$head_width = $image->getImageWidth();
				$head_height = $image->getImageHeight();
			}

			if ($image->toThumbnail($filename, $thumbnail_mime_type, $head_width, $head_height, $crop_picture, $quality, $filter, $create_high_resolution)) {

			    $result['thumb_width'] = $image->getThumbnailWidth();
			    $result['thumb_height'] = $image->getThumbnailHeight();
			    
				if ($image->getImageMimeType() === 'image/webp' || $thumbnail_mime_type === 'image/webp' || $image->getImageMimeType() === 'image/avif' || $thumbnail_mime_type === 'image/avif') { // create fallback

					$fallback_extension = 'png';
					$fallback_mime_type = 'image/png';

					// create fallback with original image mime type when the original is not webp or avif
					if ($image->getImageMimeType() !== 'image/webp' && $image->getImageMimeType() !== 'image/avif') {
						$fallback_extension = $original_imageext;
						$fallback_mime_type = $image->getImageMimeType();
					}

					$quality = self::getImageQualityFromExt($fallback_extension, $image_quality_array);

					if (!$image->toThumbnail($tmp_path . '/thumb_' . $module_id . '_' . $item_id . '.' . $fallback_extension, $fallback_mime_type, $head_width, $head_height, $crop_picture, $quality, $filter, $create_high_resolution)) {
						$result['error'] = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_THUMBNAILCREATIONFAILED', $imagesrc);
					}
				}
			} else {
				$result['error'] = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_THUMBNAILCREATIONFAILED', $imagesrc);
			}
		}

		$image->destroy();

		if (empty($result['error'])) {
			$result['url'] = $filename;
		}

		return $result;
	}

	static protected function getImageQualityFromExt($image_extension, $qualities = array('jpg' => 75, 'png' => 3, 'webp' => 80, 'avif' => 80))
	{
		$quality = -1;

		switch ($image_extension){
			case 'jpg': case 'jpeg': $quality = $qualities['jpg']; break; // 0 to 100
			case 'png': $quality = round(11.111111 * (9 - $qualities['png'])); break; // compression: 0 to 9
			case 'webp': $quality = $qualities['webp']; break; // 0 to 100
			case 'avif': $quality = $qualities['avif']; // 0 to 100
		}

		return $quality;
	}

	/**
	 * Delete all thumbnails for a module instance
	 *
	 * @param string $module_id
	 * @param string $tmp_path
	 *
	 * @return false if the glob function failed, true otherwise
	 */
	static function clearThumbnails($module_id, $tmp_path)
	{
		Log::addLogger(array('text_file' => 'syw.errors.php'), Log::ALL, array('syw'));

		if (function_exists('glob')) {
			$filenames = glob(JPATH_ROOT.'/'.$tmp_path.'/thumb_'.$module_id.'_*.*');
			if ($filenames == false) {
				Log::add('modLatestNewsEnhancedHelper:clearThumbnails() - Error on glob - No permission on files/folder or old system', Log::ERROR, 'syw');
				return false;
			}

			foreach ($filenames as $filename) {
				File::delete($filename); // returns false if deleting failed - won't log to avoid making the log file huge
			}

			return true;
		} else {
			Log::add('modLatestNewsEnhancedHelper:clearThumbnails() - glob - function does not exist', Log::ERROR, 'syw');
		}

		return false;
	}

	/**
	 * Check if thumbnail already exists for an item
	 * When including high resolution thumbnails, both images need to exist
	 * Since there is no way to know what extension has been previously used, it needs to iterate through the valid extension types
	 *
	 * @param string $module_id
	 * @param string $item_id
	 * @param string $tmp_path
	 * @param boolean $include_highres
	 *
	 * @return string|boolean the thumbnail filename if found, false otherwise
	 */
	static function thumbnailExists($module_id, $item_id, $tmp_path, $include_highres = false)
	{
		$existing_thumbnail_path = null;
		foreach (self::$image_extension_types as $thumbnail_extension_type) {
			$thumbnail_path = $tmp_path.'/thumb_'.$module_id.'_'.$item_id.'.'.$thumbnail_extension_type;
			if (is_file(JPATH_ROOT.'/'.$thumbnail_path)) {
				$existing_thumbnail_path = $thumbnail_path; // uses the first file found, but could be several with different extensions
			}
		}

		// glob may not work with cURL on some php versions (like 5.4.14)
// 		$result = glob("'.$tmp_path.'/thumb_'.$module_id.'_'.$item_id.'.{jpg,jpeg,png,gif}", GLOB_BRACE);
// 		if ($result == false || empty($result)) {
// 			return false;
// 		} else {
// 			$existing_thumbnail_path = $result[0]; // uses the first file found, but could be several with different extensions
// 			// use filemtime() to get the most recent file? worth the trouble?
// 		}

		if (!empty($existing_thumbnail_path)) {
			if ($include_highres) {
				$thumbnail_path_highres = str_replace('.', '@2x.', $existing_thumbnail_path);
				if (is_file(JPATH_ROOT.'/'.$thumbnail_path_highres)) {
					return $existing_thumbnail_path;
				}
			} else {
				return $existing_thumbnail_path;
			}
		}

		return false;
	}

	/**
	* Create the first part of the <a> tag
	*/
	static function getHtmlATag($module, $item, $follow = true, $tooltip = true, $popup_width = '600', $popup_height = '500', $css_classes = '', $anchors = '', $add_aria_label = true)
	{
		$module_params = json_decode($module->params);

		$bootstrap_version = isset($module_params->bootstrap_version) ? $module_params->bootstrap_version : 5;
		if (empty($bootstrap_version)) {
			$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
			$bootstrap_version = $config_params->get('bootstrap_version', 'joomla');
		}

		if ($bootstrap_version === 'joomla') {
			$bootstrap_version = 5;
		} else {
			$bootstrap_version = intval($bootstrap_version);
		}

		return self::getATag($item, $follow, $tooltip, $popup_width, $popup_height, $css_classes, $anchors, $module->id, $add_aria_label, $bootstrap_version);
	}

	/*
	 * for B/C
	 */
	static function getATag($item, $follow = true, $tooltip = true, $popup_width = '600', $popup_height = '500', $css_classes = '', $anchors = '', $module_id = 0, $add_aria_label = true, $bootstrap_version = 5)
	{
		$attribute_title = '';
		$attribute_class = '';
		if ($item->linktarget == 3) {
			$attribute_class = 'lnepmodal_'.$module_id;
		} else if ($item->linktarget == 4) {
			$attribute_class = 'inline-link';
		}

		if ($tooltip) {
			$attribute_title = ' title="'.htmlspecialchars($item->linktitle, ENT_COMPAT, 'UTF-8').'"';
			$attribute_class .= empty($attribute_class) ? 'hasTooltip' : ' hasTooltip';
		}

		if (!empty($css_classes)) {
			$attribute_class .= empty($attribute_class) ? $css_classes : ' '.$css_classes;
		}

		if (!empty($attribute_class)) {
			$attribute_class = ' class="'.$attribute_class.'"';
		}

		$nofollow = '';
		if (!$follow) {
			$nofollow = ' rel="nofollow"';
		}

		$attribute_aria_label = '';
		if ($add_aria_label) {
			$readmore_text = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_READMOREABOUT_LABEL', htmlspecialchars($item->linktitle, ENT_COMPAT, 'UTF-8')); // default
			if (!$item->authorized) {
				$readmore_text = Text::sprintf('MOD_LATESTNEWSENHANCEDEXTENDED_UNAUTHORIZEDREADMOREABOUT_LABEL', htmlspecialchars($item->linktitle, ENT_COMPAT, 'UTF-8'));
			}
			$attribute_aria_label = ' aria-label="'.$readmore_text.'"';
		}

		switch ($item->linktarget) {
			case 1:	// open in a new window
				return '<a href="'.$item->link.$anchors.'" target="_blank"'.$attribute_class.$attribute_title.$attribute_aria_label.$nofollow.'>';
				break;
			case 2:	// open in a popup window
				$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$popup_width.',height='.$popup_height;
				return '<a href="'.$item->link.$anchors.'"'.$attribute_class.$attribute_title.$attribute_aria_label.' onclick="window.open(this.href, \'targetWindow\', \''.$attribs.'\'); return false;">';
				break;
			case 3:	// open in a modal window
				$extra_url = '';
				if ($item->isinternal) {
					if (strpos($item->link, "?") !== false) {
						$extra_url .= '&';
					} else {
						$extra_url .= '?';
					}
					$extra_url .= 'tmpl=component&print=1';
				}

				$link_attributes = ' onclick="return false;" data-modaltitle="'.htmlspecialchars($item->linktitle, ENT_COMPAT, 'UTF-8').'"';
				if ($bootstrap_version > 0) {
					$link_attributes .= ' data-' . ($bootstrap_version >= 5 ? 'bs-' : '') . 'toggle="modal" data-' . ($bootstrap_version >= 5 ? 'bs-' : '') . 'target="#lnepmodal_'.$module_id.'"';
				}

				return '<a href="'.$item->link.$extra_url.$anchors.'"'.$attribute_class.$attribute_title.$attribute_aria_label.$link_attributes.'>';
				break;
			case 4: // inline article
				return '<a onclick="return false;" href=""'.$attribute_class.$attribute_title.$attribute_aria_label.$nofollow.'>';
				break;
			default: // open in parent window
				return '<a href="'.$item->link.$anchors.'"'.$attribute_class.$attribute_title.$attribute_aria_label.$nofollow.'>';
		}
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

	static function date_to_counter($date, $date_in_future = false)
	{
		$date_origin = new Date($date);
		$now = new Date(); // now

		$difference = $date_origin->diff($now); // DateInterval object PHP 5.3 [y] => 0 [m] => 0 [d] => 26 [h] => 23 [i] => 11 [s] => 32 [invert] => 0 [days] => 26

		return array('years' => $difference->y, 'months' => $difference->m, 'days' => $difference->d, 'hours' => $difference->h, 'mins' => $difference->i, 'secs' => $difference->s);
	}

	static function isInfoTypeRequired($info_type, $params)
	{
		if (in_array($info_type, self::getDetailsInfoTypes($params, 'over_head_', 'information_blocks'))) {
			return true;
		}

		if (in_array($info_type, self::getDetailsInfoTypes($params, 'before_title_', 'information_blocks'))) {
			return true;
		}

		if (in_array($info_type, self::getDetailsInfoTypes($params, 'after_title_', 'information_blocks'))) {
			return true;
		}

		if (in_array($info_type, self::getDetailsInfoTypes($params, 'before_', 'information_blocks'))) {
			return true;
		}

		if (in_array($info_type, self::getDetailsInfoTypes($params, 'after_', 'information_blocks'))) {
			return true;
		}

		if (in_array($info_type, self::getDetailsInfoTypes($params, 'inline_', 'information_blocks'))) {
			return true;
		}

		return false;
	}

	/**
	 *
	 * @param object $params
	 * @param string $prefix
	 * @param string $subform
	 * @return array
	 */
	static function getDetailsInfoTypes($params, $prefix = '', $subform = '')
	{
		$info_types = array();

		// get data from subform items

		if ($prefix.$subform) {
			$information_blocs = $params->get($prefix.$subform); // array of objects
			if (!empty($information_blocs) && is_object($information_blocs)) {
				foreach ($information_blocs as $information_bloc) {
					if ($information_bloc->info != 'none') {
						$info_types[] = $information_bloc->info;
					}
				}
			}
		}

		return $info_types;
	}

	/**
	 * Get detail parameters
	 *
	 * @param object $params
	 * @param string $prefix a prefix for the fields names
	 * @return array
	 */
	static function getDetails($params, $prefix = '', $subform = '')
	{
		$infos = array();

		$user = Factory::getUser();
		$groups	= $user->getAuthorisedViewLevels();

		// get data from subform items

		if ($prefix.$subform) {
			$information_blocs = $params->get($prefix.$subform); // array of objects
			if (!empty($information_blocs) && is_object($information_blocs)) {
				foreach ($information_blocs as $information_bloc) {
					if ($information_bloc->info != 'none' && in_array($information_bloc->access, $groups)) {

						$details = array();
						$details['info'] = $information_bloc->info;
						$details['prepend'] = $information_bloc->prepend;
						$details['show_icons'] = $information_bloc->show_icons == 1 ? true : false;
						$details['icon'] = SYWUtilities::getIconFullName($information_bloc->icon);
						$details['extra_classes'] = isset($information_bloc->extra_classes) ? trim($information_bloc->extra_classes) : '';

						$infos[] = $details;

						if ($information_bloc->new_line == 1) {
							$infos[] = array('info' => 'newline', 'prepend' => '', 'show_icons' => false, 'icon' => '', 'extra_classes' => '');
						}
					}
				}
			}
		}

		return $infos;
	}

	/**
	 * Get block information
	 *
	 * @param array $infos
	 * @param object $params
	 * @param object $item
	 * @param object $item_params
	 * @return string
	 */
	static function getInfoBlock($infos, $params, $item, $item_params = null)
	{
		$info_block = '';

		if (empty($infos)) {
			return $info_block;
		}

		$show_date = $params->get('show_d', 'date'); // kept for backward compatibility

		$layout_suffix = trim($params->get('layout_suffix', ''));

		$separator = htmlspecialchars($params->get('separator', ''));
		$separator = empty($separator) ? ' ' : $separator;

		$info_block .= '<dt>'.Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_INFORMATION_LABEL').'</dt>';

		$info_block .= '<dd class="newsextra">';
		$has_info_from_previous_detail = false;

		foreach ($infos as $key => $value) {

			switch ($value['info']) {
				case 'newline':
					$info_block .= '</dd><dd class="newsextra">';
					$has_info_from_previous_detail = false;
				break;

				case 'readmore':

					if (isset($item->link) && !empty($item->link) && $item->cropped) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_readmore', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'hits':

					//if ($item_params->get('show_hits')) {
					if (isset($item->hits)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_hits', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'rating':

					//if ($item_params->get('show_vote')) {
					if (isset($item->vote)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_rating', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'author':

					//if ($item_params->get('show_author')) {
					if (isset($item->author)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_author', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'keywords':
					if (isset($item->metakey) && !empty($item->metakey)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_keywords', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'category':
				case 'linkedcategory':

					//if ($item_params->get('show_category')) {
					if (isset($item->category_title)) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_category', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}

						// TEMPORARY FIX (better than messing up with layout)
						$params->set('cat_link_lbl', $params->get('cat_link', ''));
						$params->set('unauthorized_cat_link_lbl', $params->get('unauthorized_cat_link', ''));

						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes'], 'linked' => ($value['info'] == 'category') ? false : true);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'date':
				case 'ago':
				case 'agomhd':
				case 'agohm':
					if (isset($item->date)) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						if ($show_date != 'date' && $value['info'] != 'time') { // for backward compatibility until re-saved
							$value['info'] = $show_date;
						}

						$layout = new FileLayout('details.lnep_detail_'.$value['info'], null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'time':
					if (isset($item->date)) {
						if (!isset($item->show_time) || (isset($item->show_time) && $item->show_time)) {
							if ($has_info_from_previous_detail) {
								$info_block .= '<span class="delimiter">'.$separator.'</span>';
							}

							$layout = new FileLayout('details.lnep_detail_time', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
							if ($layout_suffix) {
								$layout->setSuffixes(array($layout_suffix));
							}
							$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
							$info_block .= $layout->render($data);

							$has_info_from_previous_detail = true;
						}
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

// 							$globalparams = \Joomla\CMS\Component\ComponentHelper::getParams('com_content');

// 							$targeta = $globalparams->get('targeta', 0);
// 							if (!empty($urls->targeta)) {
// 								$targeta = $urls->targeta;
// 							}

// 							$targetb = $globalparams->get('targetb', 0);
// 							if (!empty($urls->targetb)) {
// 								$targetb = $urls->targetb;
// 							}

// 							$targetc = $globalparams->get('targetc', 0);
// 							if (!empty($urls->targetc)) {
// 								$targetc = $urls->targetc;
// 							}

							// if all links a b c
							if ($value['info'] == 'links' || $value['info'] == 'linksnl') {

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_linksabc', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'urls' => $urls, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								//$data['alllinks'] = ($value['info'] == 'links' || $value['info'] == 'linksnl') ? true : false;
								$data['link'] = 'links';
								$data['separated'] = ($value['info'] == 'linksnl') ? true : false;
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							} // end all links a b c

							// link a
							if ($value['info'] == 'linka' && !empty($urls->urla)) {

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_linksabc', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'urls' => $urls, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								//$data['alllinks'] = ($value['info'] == 'links' || $value['info'] == 'linksnl') ? true : false;
								$data['link'] = 'linka';
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}

							// link b
							if ($value['info'] == 'linkb' && !empty($urls->urlb)) {

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_linksabc', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'urls' => $urls, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								//$data['alllinks'] = ($value['info'] == 'links' || $value['info'] == 'linksnl') ? true : false;
								$data['link'] = 'linkb';
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}

							// link c
							if ($value['info'] == 'linkc' && !empty($urls->urlc)) {

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_linksabc', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'urls' => $urls, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								//$data['alllinks'] = ($value['info'] == 'links' || $value['info'] == 'linksnl') ? true : false;
								$data['link'] = 'linkc';
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							}
						}
					}
				break;

				case 'tags':
				case 'linkedtags':
				case 'selectedtags':
				case 'linkedselectedtags':

					if (/*$item_params->get('show_tags') && */isset($item->tags)) {

						$datasource = $params->get('datasource', 'articles');

						if (SYWK2::exists() && $datasource == 'k2') {

							// K2 tags

							// remove tags to hide
							$tags_to_hide = $params->get('hide_k2tags');
							if (!empty($tags_to_hide) && !empty($item->tags)) {
								foreach ($item->tags as $key => $item_tag) {
									if (in_array($item_tag->id, $tags_to_hide)) {
										unset($item->tags[$key]);
									}
								}
							}

							if (!empty($item->tags)) {

								// order tags
								switch ($params->get('order_k2tags', 'none')) {
									case 'alpha': usort($item->tags, array(__CLASS__, 'compare_k2tags_by_name')); break;
								}

// 								if ($value['info'] == 'linkedtags' || $value['info'] == 'linkedselectedtags') {
// 									require_once (JPATH_SITE.'/components/com_k2/helpers/route.php');
// 								}

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_tags', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								$data['linked'] = ($value['info'] == 'linkedtags' || $value['info'] == 'linkedselectedtags') ? true : false;
								$data['selected'] = ($value['info'] == 'selectedtags' || $value['info'] == 'linkedselectedtags') ? true : false;
								$data['datasource'] = $datasource;
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							} // end not empty tags
						} else {

							// Joomla tags

							// remove tags to hide
							$tags_to_hide = $params->get('hide_tags');
							if (!empty($tags_to_hide) && !empty($item->tags)) {
								foreach ($item->tags as $key => $item_tag) {
									if (in_array($item_tag->id, $tags_to_hide)) {
										unset($item->tags[$key]);
									}
								}
							}

							if (!empty($item->tags)) {

								// order tags
								switch ($params->get('order_tags', 'none')) {
									case 'console': usort($item->tags, array(__CLASS__, 'compare_tags_by_console')); break;
									case 'alpha': usort($item->tags, array(__CLASS__, 'compare_tags_by_name')); break;
								}

// 								if ($value['info'] == 'linkedtags' || $value['info'] == 'linkedselectedtags') {
// 									JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
// 								}

								if ($has_info_from_previous_detail) {
									$info_block .= '<span class="delimiter">'.$separator.'</span>';
								}

								$layout = new FileLayout('details.lnep_detail_tags', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
								if ($layout_suffix) {
									$layout->setSuffixes(array($layout_suffix));
								}
								$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
								$data['linked'] = ($value['info'] == 'linkedtags' || $value['info'] == 'linkedselectedtags') ? true : false;
								$data['selected'] = ($value['info'] == 'selectedtags' || $value['info'] == 'linkedselectedtags') ? true : false;
								$data['datasource'] = $datasource;
								$info_block .= $layout->render($data);

								$has_info_from_previous_detail = true;
							} // end not empty tags
						}
					}
				break;

				case 'share':
					if (isset($item->link) && !empty($item->link) && $item->authorized) {
						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_share', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				case 'k2commentscount':
				case 'linkedk2commentscount':
					if (SYWK2::exists()) {

						if ($has_info_from_previous_detail) {
							$info_block .= '<span class="delimiter">'.$separator.'</span>';
						}

						$layout = new FileLayout('details.lnep_detail_k2commentscount', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
						if ($layout_suffix) {
							$layout->setSuffixes(array($layout_suffix));
						}
						$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);
						$data['linked'] = ($value['info'] == 'linkedk2commentscount') ? true : false;
						$info_block .= $layout->render($data);

						$has_info_from_previous_detail = true;
					}
				break;

				default:

					$type_temp = explode(':', $value['info']); // $value['info'] is jfield:type:field|k2field:type:field|<plugin>:field

					if (count($type_temp) < 2) {
						break;
					}

					if ($type_temp[0] == 'jfield') { // Joomla fields

						$field_id = $type_temp[2];
						$field_type = $type_temp[1];
						
						$results = SYWFields::getCustomFieldValues($field_id, $item->id, true, true);

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
									
									$text_values = array();
									
									foreach ($items as $item) {
										$text_values[] = $item->text;
									}
									
									$field_values = $text_values;
								} catch (ExecutionFailureException $e) {
									// return the raw values
								}
							}

							$supported_types = array('calendar', 'textarea', 'url', 'editor');
							// we keep the ones with actual layouts, not going through the generic one

							// additional fields - need a layout to show
							// if no layout in template, will show nothing (will not go to generic layout)

							$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
							$additional_types = $config_params->get('additional_supported_fields', array());
							if (!empty($additional_types)) {
								$supported_types = array_merge($additional_types, $supported_types);
							}

							if (in_array($field_type, $supported_types)) {
								$layout = new FileLayout('details.lnep_detail_jfield_'.$field_type, null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
							} else {
								$layout = new FileLayout('details.lnep_detail_jfield_generic', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
							}

							if ($layout_suffix) {
								$layout->setSuffixes(array($layout_suffix));
							}

							$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							$data['field_id'] = $field_id;
							$data['field_type'] = $field_type;
							$data['field_label'] = $results[0]['title'];
							$data['field_values'] = $field_values;
							$data['field_options'] = json_decode($results[0]['fieldoptions']);
							$data['field_params'] = $field_params;

							$info_block .= $layout->render($data);

							$has_info_from_previous_detail = true;
						}

					} else if ($type_temp[0] == 'k2field') { // K2 extra fields

						$field_id = $type_temp[2];
						$field_type = $type_temp[1];

						$field_values = '';

						if (isset($item->extra_fields) && $item->extra_fields) {
							$item_extra_fields = json_decode($item->extra_fields); // 'extra_fields' are retrieved in K2 helper

							foreach ($item_extra_fields as $item_extra_field) {
								if ($item_extra_field->id == $field_id) {
									$field_values = $item_extra_field->value; // can be array or string
								}
							}
						}

						if ($field_values) {
							if ($has_info_from_previous_detail) {
								$info_block .= '<span class="delimiter">'.$separator.'</span>';
							}

							$supported_types = array(/*'textfield',*/ 'textarea', 'select', 'multipleSelect', 'radio', 'link', 'date');
							if (in_array($field_type, $supported_types)) {
								$layout = new FileLayout('details.lnep_detail_k2field_'.$field_type, null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
							} else {
								$layout = new FileLayout('details.lnep_detail_k2field_generic', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component
							}

							if ($layout_suffix) {
								$layout->setSuffixes(array($layout_suffix));
							}

							$data = array('item' => $item, 'params' => $params, 'label' => $value['prepend'], 'show_icon' => $value['show_icons'], 'icon' => $value['icon'], 'extraclasses' => $value['extra_classes']);

							$data['field_id'] = $field_id;
							$data['field_type'] = $field_type;
							$data['field_values'] = $field_values;

							$info_block .= $layout->render($data);

							$has_info_from_previous_detail = true;
						}

					} else { // plugins

						PluginHelper::importPlugin('latestnewsenhanced');

						$value['info'] = $type_temp[1];

						$results = Factory::getApplication()->triggerEvent('onLatestNewsEnhancedGetDetailData', array($value, $params, $item, $item_params));
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

		// remove potential 'newsextra' block when no data is available
		$info_block = str_replace('<dd class="newsextra"></dd>', '', $info_block);

		if (strpos($info_block, 'dd') === false) {
			return ''; // accessibility rule: if no dd then no dt is allowed
		}

		return $info_block;
	}

	/**
	 * Get K2 extra fields values
	 *
	 * @param string $field_id the K2 extra field id
	 * @param array $field_values the list of row values (ex: '1', we want to get 'high')
	 * @return string the values, comma separated
	 */
	static function getK2ExtraFieldValues($field_id, $field_values)
	{
		$field_info = self::getK2ExtraFieldInfo($field_id);

		if (!empty($field_info)) {
			$values = array();
			foreach (json_decode($field_info['value']) as $default_value) {
				if (in_array((string)$default_value->value, $field_values)) {
					$values[] = $default_value->name;
				}
			}
			if (!empty($values)) {
				return implode(', ', $values); // TODO use separator Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_FIELDSSEPARATOR')
			}
		}

		return '';
	}

	/**
	 * Get K2 extra field default values
	 * @param string $field_id the K2 extra field id
	 * @return NULL|array
	 */
	static function getK2ExtraFieldInfo($field_id)
	{
		static $k2extrafields = array();

		if (isset($k2extrafields[$field_id])) {
			return $k2extrafields[$field_id];
		}

		$db = Factory::getDBO();

		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('name', 'value', 'type')));
		$query->from($db->quoteName('#__k2_extra_fields'));
		$query->where($db->quoteName('id') . '=' . $field_id);
		$query->where($db->quoteName('published').'= 1');

		$db->setQuery($query);

		try {
			$k2extrafields[$field_id] = $db->loadAssoc();
		} catch (ExecutionFailureException $e) {
			return null;
		}

		return $k2extrafields[$field_id];
	}

	static function getVideoCustomFields()
	{
		if (isset(self::$video_types_fields)) {
			return self::$video_types_fields;
		}

		$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');

		self::$video_types_fields = array();

		self::$video_types_fields['dailymotion'] = $config_params->get('supported_dailymotion_fields', array());
		self::$video_types_fields['facebookvideo'] = $config_params->get('supported_facebookvideo_fields', array());
		self::$video_types_fields['html5video'] = $config_params->get('supported_html5video_fields', array());
		self::$video_types_fields['vimeo'] = $config_params->get('supported_vimeo_fields', array());
		self::$video_types_fields['youtube'] = $config_params->get('supported_youtube_fields', array());

		return self::$video_types_fields;
	}

	static function isHeadVideo($head_value)
	{
		foreach (self::getVideoCustomFields() as $video_type => $video_fields) {
			foreach ($video_fields as $video_field) {
				if (substr($head_value, 0, strlen('jfield:'.$video_field)) === 'jfield:'.$video_field) {
					return $video_type;
				}
			}
		}

		return false;
	}

	/**
	* Load plugin if needed by animation
	*/
	static function loadAnimationLibrary($animation, $remote = false)
	{
		if ($animation === 'cover' || $animation === 'fade' || $animation === 'scroll') {

			HTMLHelper::_('jquery.framework');
			SYWLibraries::loadCarousel(true, true, false, false, false, false, $remote);

		} else if ($animation === 'purescroll') {

			SYWLibraries::loadTinySlider($remote);

		} else if ($animation === 'justpagination') {

			SYWLibraries::loadPurePagination($remote);

		} else {
			require_once (dirname(__FILE__).'/helper_'.$animation.'.php');

			$class = 'modLatestNewsEnhancedExtendedHelper'.ucfirst($animation);
			$instance = new $class();
			$instance->load_library(); // TODO add remote parameter ! B/C
		}
	}

	/**
	 * Load common stylesheet to all module instances
	 */
	static function loadCommonStylesheet()
	{
		$wam = Factory::getApplication()->getDocument()->getWebAssetManager();

		$wam->registerAndUseStyle('lne.common_styles', 'mod_latestnewsenhanced/common_styles.min.css', ['relative' => true, 'version' => 'auto']);
	}

	/**
	 * Load user stylesheet to all module instances
	 * if the file has 'substitute' in the name, it will replace all module styles
	 */
	static function loadUserStylesheet($styles_substitute = false)
	{
		$wam = Factory::getApplication()->getDocument()->getWebAssetManager();

		$prefix = 'common_user';
		if ($styles_substitute) {
			$prefix = 'substitute';
		}

		if (File::exists(JPATH_ROOT . '/media/mod_latestnewsenhanced/css/' . $prefix . '_styles-min.css')) {
			if (JDEBUG && File::exists(JPATH_ROOT . '/media/mod_latestnewsenhanced/css/' . $prefix . '_styles.css')) {
				$wam->registerAndUseStyle('lne.' . $prefix . '_styles', 'mod_latestnewsenhanced/' . $prefix . '_styles.css', ['relative' => true, 'version' => 'auto']);
			} else {
				$wam->registerAndUseStyle('lne.' . $prefix . '_styles', 'mod_latestnewsenhanced/' . $prefix . '_styles-min.css', ['relative' => true, 'version' => 'auto']);
			}
		} else {
			$wam->registerAndUseStyle('lne.' . $prefix . '_styles', 'mod_latestnewsenhanced/' . $prefix . '_styles.min.css', ['relative' => true, 'version' => 'auto']);
		}
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

	/*
	 * Check if a tag is part of the module's array of selected ones
	 * $include_children only for com_content
	 */
	static function isTagSelected($tag_id, $tag_ids = array(), $include_children = false)
	{
		if (!empty($tag_ids)) {
			$array_of_tag_values = array_count_values($tag_ids);
			if (isset($array_of_tag_values['all']) && $array_of_tag_values['all'] > 0) { // 'all' was selected
				return true;
			} else {
				if ($include_children) {
					$tagTreeArray = array();
					$helper_tags = new TagsHelper();

					foreach ($tag_ids as $tag) {
						$helper_tags->getTagTreeArray($tag, $tagTreeArray);
					}

					$tag_ids = array_unique(array_merge($tag_ids, $tagTreeArray));
				}
				return in_array($tag_id, $tag_ids);
			}
		}

		return true; // if there are no tags selected, return true to compensate possible user error
	}

	static function compare_tags_by_name($tag1, $tag2)
	{
		return strcmp($tag1->title, $tag2->title);
	}

	static function compare_k2tags_by_name($tag1, $tag2)
	{
		return strcmp($tag1->name, $tag2->name);
	}

	static function compare_tags_by_console($tag1, $tag2)
	{
		return (intval($tag1->lft) > intval($tag2->lft) ) ? 1 : -1;
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
	 * Are white spaces removed ?
	 * @return boolean
	 */
	public static function isRemoveWhitespaces($params)
	{
		if (self::getSiteMode($params) == 'dev') {
			return false;
		}
		if (self::getSiteMode($params) == 'prod') {
			return true;
		}
		return boolval(($params->get('remove_whitespaces', '') == '') ? self::getConfig()->get('remove_whitespaces', false) : $params->get('remove_whitespaces', ''));
	}

	/**
	* Get the component's configuration parameters
	* @return \Joomla\Registry\Registry
	*/
	public static function getConfig()
	{
		if (!isset(self::$config_params)) {

			self::$config_params = new Registry();

			if (File::exists(JPATH_ADMINISTRATOR . '/components/com_latestnewsenhancedpro/config.xml')) {
				self::$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
			}
		}

		return self::$config_params;
	}

}
?>