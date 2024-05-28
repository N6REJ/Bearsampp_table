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
	'height' => null,
	
	'fs' => $fieldParams->get('fs', '1') === '1',
	'autoplay' => $fieldParams->get('autoplay', '0') === '1',
	'autopause' => $fieldParams->get('autopause', '0') === '1',
	'show_text' => $fieldParams->get('show_text', '0') === '1',
	'show_captions' => $fieldParams->get('show_captions', '0') === '1',
	
];

echo \NRFramework\Widgets\Helper::render('FacebookVideo', $payload);