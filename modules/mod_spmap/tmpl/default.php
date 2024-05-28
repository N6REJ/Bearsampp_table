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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

if (!isset($markers) || !is_array($markers) || !count($markers))
{
	echo sprintf(Text::_('MOD_SPMAP_NO_MARKERS_FOR_X_MODUKLE'), $module->title);
	return;
}

// Get component params
$componentParams = ComponentHelper::getParams('com_smilepack');

// Find provider
$provider = $params->get('provider', 'OpenStreetMap');
$provider_key = $componentParams->get(strtolower($provider) . '_key');
$maptype = $params->get(strtolower($provider) . '_maptype');

$maptype = is_null($maptype) ? ($provider === 'GoogleMap' ? 'roadmap' : 'road') : $maptype;

$arrayParams = $params->toArray();

$payload = $params->flatten();
$payload = array_merge($payload, [
	'provider_key' => $provider_key,
	'width' => isset($arrayParams['width']) ? $arrayParams['width'] : null,
	'height' => isset($arrayParams['height']) ? $arrayParams['height'] : null,
	'markers' => $markers,
	'map_center' => $params->get('map_center.coordinates'),
	'enable_info_window' => $params->get('enable_info_window', '0') !== '0' ? $params->get('enable_info_window', '0') : false,
	
	'scale' => false,
	'view' => 'road' . ($provider === 'GoogleMap' ? 'map' : ''),
	
	
]);

echo \NRFramework\Widgets\Helper::render($provider, $payload);