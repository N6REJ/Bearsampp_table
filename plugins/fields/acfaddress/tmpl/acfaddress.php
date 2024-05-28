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

$value = $field->value;

if (is_string($value) && !$value = json_decode($value, true))
{
    return;
}

$payload = $value + $fieldParams->flatten();
$payload['value'] = !empty($payload['address']['latitude']) && !empty($payload['address']['longitude']) ? $payload['address']['latitude'] . ',' . $payload['address']['longitude'] : null;
$payload['show_map'] = $payload['show_map'] === '0' ? false : $payload['show_map'];
$payload['showAddressDetails'] = $fieldParams->get('address_details_info', []);

// Set custom layout
if ($field->params->get('acf_layout_override'))
{
	$payload['layout'] = $field->params->get('acf_layout_override');
}

echo \NRFramework\Widgets\Helper::render('MapAddress', $payload);