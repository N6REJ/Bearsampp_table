<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace ACF\Helpers\Fields;

defined('_JEXEC') or die;

use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Str;

class Address
{
	/**
	 * Returns the YooTheme type.
	 * 
	 * If this accepts one image:
	 * - Tells YooTheme to use the default type for the dropdown mapping option.
	 * 
	 * If this accepts multiple images:
	 * - Tells YooTheme to only return the value of this field in the dropdown mapping option.
	 * 
	 * @param   object  $field
	 * @param   object  $source
	 * 
	 * @return  array
	 */
	public static function getYooType($field = [], $source = [])
	{
        $fields = [
			[
				'type' => 'String',
                'name' => 'latitude',
                'metadata' => [
                    'label' => 'Latitude'
                ],
			],
			[
				'type' => 'String',
                'name' => 'longitude',
                'metadata' => [
                    'label' => 'Longitude'
                ],
			],
			[
				'type' => 'String',
                'name' => 'value',
                'metadata' => [
                    'label' => 'Coordinates'
                ],
			],
			[
				'type' => 'String',
                'name' => 'address',
                'metadata' => [
                    'label' => 'Address'
                ],
			],
			[
				'type' => 'String',
                'name' => 'postal_code',
                'metadata' => [
                    'label' => 'Postal Code'
                ],
			],
			[
				'type' => 'String',
                'name' => 'country_code',
                'metadata' => [
                    'label' => 'Country Code'
                ],
			],
			[
				'type' => 'String',
                'name' => 'country',
                'metadata' => [
                    'label' => 'Country'
                ],
			],
			[
				'type' => 'String',
                'name' => 'state',
                'metadata' => [
                    'label' => 'State'
                ],
			],
			[
				'type' => 'String',
                'name' => 'municipality',
                'metadata' => [
                    'label' => 'Municipality'
                ],
			],
			[
				'type' => 'String',
                'name' => 'town',
                'metadata' => [
                    'label' => 'Town'
                ],
			],
			[
				'type' => 'String',
                'name' => 'city',
                'metadata' => [
                    'label' => 'City'
                ],
			],
			[
				'type' => 'String',
                'name' => 'road',
                'metadata' => [
                    'label' => 'Road'
                ],
			]
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		return $name;
	}

	/**
	 * Transforms the field value to an appropriate value that YooTheme can understand.
	 * 
	 * @return  array
	 */
	public static function yooResolve($item, $args, $ctx, $info)
	{
		$name = str_replace('String', '', strtr($info->fieldName, '_', '-'));
        
		// Check if it's a subform field
        $subfield = clone \ACF\Helpers\Yoo::getSubfield($args['field_id'], $args['context']);

		// When we have a subform field, the $item is an array of values
		if (!$subfield || !is_array($item))
		{
			if (!isset($item->id) || !($field = FieldsType::getField($name, $item, $args['context'])))
			{
				return;
			}
		}
		else
		{
			// Set rawvalue
			$subfield->rawvalue = isset($item["field{$args['field_id']}"]) ? json_encode($item["field{$args['field_id']}"]) : '';

			// Use the subform field
			$field = $subfield;
		}

        $value = $field->rawvalue;

        if (is_string($value))
        {
			if (!$value = json_decode($value, true))
			{
				return;
			}
        }

		if (!is_array($value))
		{
			return;
		}

		$lat = isset($value['address']['latitude']) ? $value['address']['latitude'] : null;
		$lng = isset($value['address']['longitude']) ? $value['address']['longitude'] : null;

		return [
			'latitude' => $lat,
			'longitude' => $lng,
			'value' => $lat && $lng ? implode(',', [$lat, $lng]) : null,
			'address' => isset($value['address']['address']) ? $value['address']['address'] : null,
			'postal_code' => isset($value['address']['postal_code']) ? $value['address']['postal_code'] : null,
			'country_code' => isset($value['address']['country_code']) ? $value['address']['country_code'] : null,
			'country' => isset($value['address']['country']) ? $value['address']['country'] : null,
			'state' => isset($value['address']['state']) ? $value['address']['state'] : null,
			'municipality' => isset($value['address']['municipality']) ? $value['address']['municipality'] : null,
			'town' => isset($value['address']['town']) ? $value['address']['town'] : null,
			'city' => isset($value['address']['city']) ? $value['address']['city'] : null,
			'road' => isset($value['address']['road']) ? $value['address']['road'] : null
		];
	}
}