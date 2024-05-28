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

defined('_JEXEC') or die('Restricted access');

if (!$value = $params->get('value'))
{
	return;
}

$value = json_decode(json_encode($value), true);

$gallery_items = isset($value['items']) ? $value['items'] : [];
if (!$gallery_items)
{
	return;
}

require_once dirname(__DIR__) . '/fields/helper.php';


$style = 'grid';
$widgetName = 'Gallery';
$tags = [];




$arrayParams = $params->toArray();

$payload = [
    'items' => SPGalleryHelper::prepareItems($gallery_items, $tags),
    
    'style' => 'grid',
    'limit_files' => 8,
    'ordering' => 'default',
    
    
    'thumb_width' => $params->get('thumb_width', ''),
    'thumb_height' => $style === 'grid' ? $params->get('thumb_height', '0') : null,
    'columns' => isset($arrayParams['columns']) ? $arrayParams['columns'] : null,
    'gap' => isset($arrayParams['gap']) ? $arrayParams['gap'] : null
];



echo \NRFramework\Widgets\Helper::render($widgetName, $payload);