<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$videoURL = $field->value)
{
	return;
}

$payload = [
	'value' => $videoURL,
	'width' => $fieldParams->get('width', '480px'),
	'height' => $fieldParams->get('height', '270px'),
	'privacy' => $fieldParams->get('privacyMode', '0') === '1',
	
	'controls' => $fieldParams->get('controls', '1') === '1',
	'loop' => $fieldParams->get('loop', '0') === '1',
	'mute' => $fieldParams->get('mute', '0') === '1',
	'autoplay' => $fieldParams->get('autoplay', '0') === '1',
	'autopause' => $fieldParams->get('autopause', '0') === '1',
	'title' => $fieldParams->get('title', '0') === '1',
	'byline' => $fieldParams->get('byline', '0') === '1',
	'portrait' => $fieldParams->get('portrait', '0') === '1',
	'pip' => $fieldParams->get('pip', '0') === '1',
	'speed' => $fieldParams->get('speed', '0') === '1',
	'color' => $fieldParams->get('vimeo_color'),
	'keyboard' => $fieldParams->get('disablekb', '0') === '0',
	'start' => $fieldParams->get('start', ''),
	'end' => $fieldParams->get('end', ''),
	'coverImageType' => $fieldParams->get('coverImageType', false),
	'coverImage' => $fieldParams->get('coverImage', ''),
	
];

echo \NRFramework\Widgets\Helper::render('Vimeo', $payload);