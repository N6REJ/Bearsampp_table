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
	'preload' => $fieldParams->get('preload', 'auto'),
	
	'autopause' => $fieldParams->get('autopause', '0') === '1',
	
	'autoplay' => $fieldParams->get('selfhostedvideo_autoplay', '0') === '1',
	'controls' => $fieldParams->get('selfhostedvideo_controls', '0') === '1',
	'loop' => $fieldParams->get('selfhostedvideo_loop', '0') === '1',
	'mute' => $fieldParams->get('selfhostedvideo_mute', '0') === '1'
];

echo \NRFramework\Widgets\Helper::render('SelfHostedVideo', $payload);