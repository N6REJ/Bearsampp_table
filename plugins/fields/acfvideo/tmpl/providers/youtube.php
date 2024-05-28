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
	
	'autoplay' => $fieldParams->get('autoplay', '0') === '1',
	'autopause' => $fieldParams->get('autopause', '0') === '1',
	'fs' => $fieldParams->get('fs', '1') === '1',
	'controls' => $fieldParams->get('controls', '1') === '1',
	'loop' => $fieldParams->get('loop', '0') === '1',
	'mute' => $fieldParams->get('mute', '0') === '1',
	'cc_load_policy' => $fieldParams->get('cc_load_policy', '0') === '1',
	'disablekb' => $fieldParams->get('disablekb', '0') === '1',
	'start' => $fieldParams->get('start', ''),
	'end' => $fieldParams->get('end', ''),
	'modestbranding' => $fieldParams->get('modestbranding', '0') === '1',
	'rel' => $fieldParams->get('rel', '') === '1' ? '1' : '0',
	'color' => $fieldParams->get('youtube_color', 'red'),
	'coverImageType' => $fieldParams->get('coverImageType', false),
	'coverImage' => $fieldParams->get('coverImage', ''),
	
];

echo \NRFramework\Widgets\Helper::render('YouTube', $payload);