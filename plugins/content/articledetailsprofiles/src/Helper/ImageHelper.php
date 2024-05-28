<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Image as SYWImage;

class ImageHelper
{
    protected static $extension_types = array('png', 'jpg', 'gif', 'jpeg', 'webp', 'avif');

	static function getGravatar($email, $width)
	{
		$gravatar_url = 'https://www.gravatar.com/avatar/';
		$gravatar_url .= md5(strtolower(trim($email)));
		$gravatar_url .= '?d=wavatar'; // 404 | mm | identicon | monsterid | wavatar
		$gravatar_url .= '&r=g'; // g | pg | r | x
		$gravatar_url .= '&s='.$width;

		return $gravatar_url;
	}

	/**
	 * Create the thumbnail(s), if possible
	 *
	 * @param string $area [list|article_header|article_footer]
	 * @param string $item_id
	 * @param string $imagesrc
	 * @param string $tmp_path
	 * @param integer $head_width
	 * @param integer $head_height
	 * @param boolean $crop_picture
	 * @param array $quality_array
	 * @param string $filter
	 * @param boolean $create_high_resolution
	 * @param boolean $allow_remote
	 * @param string $thumbnail_mime_type
	 *
	 * @return array the original image path if errors before thumbnail creation
	 *  or no thumbnail path if errors during thumbnail creation
	 *  or thumbnail path if no error
	 */
	static function getImageFromSrc($area, $item_id, $imagesrc, $tmp_path, $head_width, $head_height, $crop_picture, $quality_array, $filter, $create_high_resolution = false, $allow_remote = true, $thumbnail_mime_type = '')
	{
		$result = array(null, null); // image link and error

		if ($head_width == 0 || $head_height == 0) {
			// keep original image
			$result[0] = $imagesrc;
			$result[1] = Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_INFO_USINGORIGINALIMAGE'); // necessary to specify thumbnail creation failed

			return $result;
		}

		if (!extension_loaded('gd') && !extension_loaded('imagick')) {
			// missing gd library
			$result[0] = $imagesrc;
			$result[1] = Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_WARNING_NOIMAGELIBRARYLOADED');

			return $result;
		}

		$original_imagesrc = $imagesrc;

		// there may be extra info in the path
		// example: http://www.tada.com/image.jpg?x=3
		// thubmnails cannot be created if ? in the path

		$url_array = explode("?", $imagesrc);
		$imagesrc = $url_array[0];

		$imageext = strtolower(File::getExt($imagesrc));
		$original_imageext = $imageext;

		if (!in_array($imageext, self::$extension_types)) {

			// case where image is a URL with no extension (generated image)
			// example: http://argos.scene7.com/is/image/Argos/7491801_R_Z001A_UC1266013?$TMB$&wid=312&hei=312
			// thubmnails cannot be created from generated images external paths
			// or image has another file type like .tiff

			$result[0] = $original_imagesrc;
			$result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_UNSUPPORTEDFILETYPE', $original_imagesrc);

			return $result;
		}

		// URL works only if 'allow url fopen' is 'on', which is a security concern
		// retricts images to the ones found on the site, external URLs are not allowed (for security purposes)
		if (substr_count($imagesrc, 'http') <= 0) { // if the image is internal
			if (substr($imagesrc, 0, 1) == '/') {
				// take the slash off
				$imagesrc = ltrim($imagesrc, '/');
			}
		} else {
			$base = Uri::base(); // JURI::base() is http://www.mysite.com/subpath/
			$imagesrc = str_ireplace($base, '', $imagesrc);
		}

		// we end up with all $imagesrc paths as 'images/...'
		// if not, the URL was from an external site

		if (substr_count($imagesrc, 'http') > 0) {
			// we have an external URL
		    if (/*!ini_get('allow_url_fopen') || */!$allow_remote) {
				$result[0] = $original_imagesrc;
				$result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_EXTERNALURLNOTALLOWED', $imagesrc);

				return $result;
			}
		}
		
		switch ($thumbnail_mime_type) {
		    case 'image/jpg': $imageext = 'jpg'; break;
		    case 'image/png': $imageext = 'png'; break;
		    case 'image/webp': $imageext = 'webp'; break;
		    case 'image/avif': $imageext = 'avif';
		}

		$filename = $tmp_path.'/thumb_'.$area.'_'.$item_id.'.'.$imageext;

		// create the thumbnail

		$image = new SYWImage($imagesrc);

		if (is_null($image->getImagePath())) {
			$result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_IMAGEFILEDOESNOTEXIST', $imagesrc);
		} else if (is_null($image->getImageMimeType())) {
			$result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_UNABLETOGETIMAGEPROPERTIES', $imagesrc);
		} else if (is_null($image->getImage()) || $image->getImageWidth() == 0) {
			$result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_UNSUPPORTEDFILETYPE', $imagesrc);
		} else {

		    $quality = self::getImageQualityFromExt($imageext, $quality_array);

			// negative values force the creation of the thumbnails with size of original image
			// great to create high-res of original image and/or to use quality parameters to create an image with smaller file size
			if ($head_width < 0 || $head_height < 0) {
				$head_width = $image->getImageWidth();
				$head_height = $image->getImageHeight();
			}

			if ($image->toThumbnail($filename, $thumbnail_mime_type, $head_width, $head_height, $crop_picture, $quality, $filter, $create_high_resolution)) {
    
    			if ($image->getImageMimeType() === 'image/webp' || $thumbnail_mime_type === 'image/webp' || $image->getImageMimeType() === 'image/avif' || $thumbnail_mime_type === 'image/avif') { // create fallback
    				
    			    $fallback_extension = 'png';
    			    $fallback_mime_type = 'image/png';
    			    
    			    // create fallback with original image mime type when the original is not webp or avif
    			    if ($image->getImageMimeType() !== 'image/webp' && $image->getImageMimeType() !== 'image/avif') {
    			        $fallback_extension = $original_imageext;
    			        $fallback_mime_type = $image->getImageMimeType();
    			    }
    			    
    			    $quality = self::getImageQualityFromExt($fallback_extension, $quality_array);
    			    
    			    if (!$image->toThumbnail($tmp_path . '/thumb_' . $area . '_' . $item_id . '.' . $fallback_extension, $fallback_mime_type, $head_width, $head_height, $crop_picture, $quality, $filter, $create_high_resolution)) {
    				    $result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_THUMBNAILCREATIONFAILED', $imagesrc);
    				}
    			}
			} else {
			    $result[1] = Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_ERROR_THUMBNAILCREATIONFAILED', $imagesrc);
			}
		}

		$image->destroy();

		if (empty($result[1])) {
			$result[0] = $filename;
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
	 * Check if thumbnail already exists for an item
	 * When including high resolution thumbnails, both images need to exist
	 * Since there is no way to know what extension has been previously used, it needs to iterate through the valid extension types
	 *
	 * @param string $area [list|article_header|article_footer]
	 * @param string $item_id
	 * @param string $tmp_path
	 * @param boolean $include_highres
	 *
	 * @return string|boolean the thumbnail filename if found, false otherwise
	 */
	static function thumbnailExists($area, $item_id, $tmp_path, $include_highres = false)
	{
		$existing_thumbnail_path = null;
		foreach (self::$extension_types as $thumbnail_extension_type) {
			$thumbnail_path = $tmp_path.'/thumb_'.$area.'_'.$item_id.'.'.$thumbnail_extension_type;
			if (is_file(JPATH_ROOT.'/'.$thumbnail_path)) {
				$existing_thumbnail_path = $thumbnail_path; // uses the first file found, but could be several with different extensions
			}
		}

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

}