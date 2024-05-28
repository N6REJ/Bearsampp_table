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

if (!$videoURL = $params->get('facebook_value'))
{
	return;
}

$arrayParams = $params->toArray();

$payload = [
	'value' => $videoURL,
	'width' => isset($arrayParams['width']) ? $arrayParams['width'] : null,
	'height' => null,
	
];

echo \NRFramework\Widgets\Helper::render('FacebookVideo', $payload);