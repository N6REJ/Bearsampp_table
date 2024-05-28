<?php
/**
* @title	    J51 Thumbs Gallery
* @version		1.1
* @website		http://www.joomla51.com
* @copyright	Copyright (C) 2012 Joomla51.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php

Credits: Bit Repository

Source URL: http://www.bitrepository.com/web-programming/php/resizing-an-image.html
------------------------------------------------------------------------------------
*/
class Resize_Image
{
	var $image_to_resize;
	var $new_width;
	var $new_height;
	var $save;

	function resize()
	{
		if( !file_exists( $this -> image_to_resize ) )
		{
			exit( "File " . $this -> image_to_resize . " does not exist." );
		}
		
		$info = GetImageSize( $this -> image_to_resize );
		
		if( empty( $info ) )
		{
			exit( "The file " . $this -> image_to_resize . " doesn't seem to be an image." );
		}
		
		$width = $info[ 0 ];
		$height = $info[ 1 ];
		$mime = $info[ 'mime' ];
	
		$type = substr( strrchr( $mime, '/' ), 1 );
		
		switch( $type )
		{
		case 'jpeg':
			$image_create_func = 'ImageCreateFromJPEG';
			$image_save_func = 'ImageJPEG';
			$new_image_ext = 'jpg';
			break;
		case 'png':
			$image_create_func = 'ImageCreateFromPNG';
			$image_save_func = 'ImagePNG';
			$new_image_ext = 'png';
			break;
		case 'bmp':
			$image_create_func = 'ImageCreateFromBMP';
			$image_save_func = 'ImageBMP';
			$new_image_ext = 'bmp';
			break;
		case 'gif':
			$image_create_func = 'ImageCreateFromGIF';
			$image_save_func = 'ImageGIF';
			$new_image_ext = 'gif';
			break;
		case 'vnd.wap.wbmp':
			$image_create_func = 'ImageCreateFromWBMP';
			$image_save_func = 'ImageWBMP';
			$new_image_ext = 'bmp';
			break;
		case 'xbm':
			$image_create_func = 'ImageCreateFromXBM';
			$image_save_func = 'ImageXBM';
			$new_image_ext = 'xbm';
			break;
			default: $image_create_func = 'ImageCreateFromJPEG';
			$image_save_func = 'ImageJPEG';
			$new_image_ext = 'jpg';
		}// New Image
	
		$image_c = ImageCreateTrueColor( $this -> new_width, $this -> new_height );
		
		if ($type=='png') {
			imagealphablending($image_c, false);
			imagesavealpha($image_c, true);
		}
		$new_image = $image_create_func( $this -> image_to_resize );
		if ($type=='png') {
			imagealphablending($new_image, true);
		}
		ImageCopyResampled( $image_c, $new_image, 0, 0, 0, 0, $this -> new_width, $this -> new_height, $width, $height );
		
		$process = $image_save_func( $image_c, $this->save );
		return array( 'result' => $process, 'new_file_path' => $this->save );
	}
}
?>