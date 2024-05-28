<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.5.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

if (!$video_url = $field->value)
{
	return;
}

if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $video_url, $match)) 
{
    $videoID = $match[5];
}

// Setup Variables
$size    		   = $fieldParams->get('size', 'fixed');
$width   		   = $fieldParams->get('width', '640');
$height   		   = $fieldParams->get('height', '360');
$width_height_atts = ($size == 'fixed') ? 'width="' . $width . '" height="' . $height . '"' : '';
$query   		   = $videoID;
$set_query_strings = '';
$url_components    = parse_url($video_url);

/**
 * Detect & set the "h" ID without it being set as a query string, required for unlisted videos.
 * 
 * Format: https://vimeo.com/VIDEO_ID/HASH
 */
$h_hash = str_replace('/' . $videoID . '/', '', $url_components['path'], $h_hash_replaced);
if ($h_hash_replaced)
{
	$set_query_strings = true;
	$query .= '?h=' . $h_hash;
}

/**
 * Detect & set the "h" query string parameter, required for unlisted videos.
 * 
 * Format: https://vimeo.com/VIDEO_ID?h=HASH
 */
if (isset($url_components['query']))
{
	parse_str($url_components['query'], $url_query_strings);
	if (count($url_query_strings) && isset($url_query_strings['h']))
	{
		$set_query_strings = true;
		$query .= '?h=' . $url_query_strings['h'];
	}
}



// Output
$buffer = '
	<iframe
		src="//player.vimeo.com/video/' . $query . '"
		frameborder="0"
		' . $width_height_atts . '
		webkitallowfullscreen
		mozallowfullscreen
		allowfullscreen>
	</iframe>
';

if ($size == 'responsive')
{
    JHtml::stylesheet('plg_system_acf/responsive_embed.css', ['relative' => true, 'version' => 'auto']);
	$buffer = '<div class="acf-responsive-embed">' . $buffer . '</div>';
}

echo $buffer;