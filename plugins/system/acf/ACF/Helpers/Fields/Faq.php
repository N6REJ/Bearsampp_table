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

class Faq
{
	/**
	 * Returns the YooTheme type.
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
                'name' => 'question',
                'metadata' => [
                    'label' => 'Question'
                ],
			],
			[
				'type' => 'String',
                'name' => 'answer',
                'metadata' => [
                    'label' => 'Answer'
                ],
			],
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		return ['listOf' => $name];
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
			$subfield->rawvalue = isset($item["field{$args['field_id']}"]) ? $item["field{$args['field_id']}"] : '';

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
		
		if (!isset($value['value']) || !is_array($value['value']))
		{
			return;
		}
		
		$value = array_values($value['value']);
		$value = array_filter($value, function($item) {
			return !empty($item['question']) && !empty($item['answer']);
		});

		return $value;
	}
}