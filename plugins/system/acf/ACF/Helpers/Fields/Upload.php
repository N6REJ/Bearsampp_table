<?php

/**
* @author Tassos Marinos <info@tassos.gr>
* @link http://www.tassos.gr
* @copyright Copyright © 2021 Tassos Marinos All Rights Reserved
* @license GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ACF\Helpers\Fields;

defined('_JEXEC') or die;

use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Str;

class Upload
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
	* @param object $field
	* @param object $source
	*
	* @return array
	*/
	public static function getYooType($field = [], $source = [])
	{
		$limit_files = (int) $field->fieldparams->get('limit_files', 0);

		$fields = [
			[
				'type' => 'String',
				'name' => 'value',
				'metadata' => [
					'label' => 'File URL'
				],
			],
			[
				'type' => 'String',
				'name' => 'title',
				'metadata' => [
					'label' => 'Title'
				],
			],
			[
				'type' => 'String',
				'name' => 'description',
				'metadata' => [
					'label' => 'Description'
				],
			],
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		if ($limit_files === 1)
		{
			return null;
		}

		return ['listOf' => $name];
	}

	/**
	* Transforms the field value to an appropriate value that YooTheme can understand.
	*
	* @return array
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

		$value = is_string($value) ? json_decode($value, true) ?? $value : $value;

		if (!is_array($value))
		{
			return;
		}
		
		$value = array_values($value);
		
		$limit_files = (int) $field->fieldparams->get('limit_files', 0);
		if ($limit_files === 1)
		{
			$value = isset($value[0]['value']) ? $value[0]['value'] : '';
		}

		if (is_array($value))
		{
			// Fix new lines in description
			foreach ($value as $key => &$val)
			{
				if (isset($val['description']))
				{
					$val['description'] = nl2br($val['description']);
				}
			}
		}

		return $value;
	}
}