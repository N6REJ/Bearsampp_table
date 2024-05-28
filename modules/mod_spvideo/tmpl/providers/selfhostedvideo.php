<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$videoURL = $params->get('selfhostedvideo_value'))
{
	return;
}

$arrayParams = $params->toArray();

$payload = [
	'value' => $videoURL,
	'width' => isset($arrayParams['width']) ? $arrayParams['width'] : null,
	'height' => isset($arrayParams['height']) ? $arrayParams['height'] : null,
	'preload' => $params->get('preload', 'auto'),
	
	'autoplay' => $params->get('selfhostedvideo_autoplay', '0') === '1',
	'controls' => $params->get('selfhostedvideo_controls', '0') === '1',
	'loop' => $params->get('selfhostedvideo_loop', '0') === '1',
	'mute' => $params->get('selfhostedvideo_mute', '0') === '1'
];

echo \NRFramework\Widgets\Helper::render('SelfHostedVideo', $payload);