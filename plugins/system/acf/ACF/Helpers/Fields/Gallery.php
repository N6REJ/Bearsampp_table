<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace ACF\Helpers\Fields;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\TagsHelper;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Str;

class Gallery
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
                'name' => 'thumb',
                'metadata' => [
                    'label' => 'Thumbnail Image URL'
                ],
			],
			[
				'type' => 'String',
                'name' => 'value',
                'metadata' => [
                    'label' => 'Full Image URL'
                ],
			],
			[
				'type' => 'String',
                'name' => 'source',
                'metadata' => [
                    'label' => 'Source Image URL'
                ],
			],
			[
				'type' => 'String',
                'name' => 'caption',
                'metadata' => [
                    'label' => 'Caption'
                ],
			],
			[
				'type' => 'String',
                'name' => 'alt',
                'metadata' => [
                    'label' => 'Alt'
                ],
			],
			[
				'type' => 'String',
                'name' => 'tags',
                'metadata' => [
                    'label' => 'Tags'
                ],
			]
		];
		$name = Str::camelCase(['Field', $field->name], true);
		$source->objectType($name, compact('fields'));

		$limit_files = (int) $field->fieldparams->get('limit_files', 0);
		if ($limit_files === 1)
		{
			return $name;
		}

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

		if (!isset($value['items']))
		{
			return;
		}

		$tagsHelper = new TagsHelper();

		// Apply ordering
		$ordering = $field->fieldparams->get('ordering', 'default');
		self::randomize($value['items'], $ordering);

		$limit_files = (int) $field->fieldparams->get('limit_files', 0);
		if ($limit_files === 1)
		{
			$uploaded_data = array_values($value['items']);

			$tags = isset($uploaded_data[0]['tags']) && is_array($uploaded_data[0]['tags']) ? $tagsHelper->getTagNames($uploaded_data[0]['tags']) : [];
			
			
			return [
				'caption' => isset($uploaded_data[0]['caption']) ? $uploaded_data[0]['caption'] : '',
				'source' => isset($uploaded_data[0]['source']) ? $uploaded_data[0]['source'] : '',
				'thumb' => isset($uploaded_data[0]['thumbnail']) ? $uploaded_data[0]['thumbnail'] : '',
				'alt' => isset($uploaded_data[0]['alt']) ? $uploaded_data[0]['alt'] : '',
				'value' => isset($uploaded_data[0]['image']) ? $uploaded_data[0]['image'] : '',
				'tags' => implode(', ', $tags)
			];
		}

		$data = array_values(array_map(function($item) use ($tagsHelper) {
			$tags = isset($item['tags']) && is_array($item['tags']) ? $tagsHelper->getTagNames($item['tags']) : [];

			return [
				'value' => $item['image'],
				'thumb' => $item['thumbnail'],
				'source' => isset($item['source']) ? $item['source'] : '',
				'alt' => isset($item['alt']) ? $item['alt'] : '',
				'caption' => $item['caption'],
				'tags' => implode(', ', $tags)
			];
		}, $value['items']));

		return $data;
	}

	public static function randomize(&$data = [], $ordering = 'default')
	{
		if (!is_array($data))
		{
			return;
		}

		switch ($ordering)
		{
			case 'random':
				shuffle($data);
				break;
			case 'alphabetical':
				usort($data, [__CLASS__, 'compareByThumbnailASC']);
				break;
			case 'reverse_alphabetical':
				usort($data, [__CLASS__, 'compareByThumbnailDESC']);
				break;
		}
	}

	/**
	 * Compares thumbnail file names in ASC order
	 * 
	 * @param   array  $a
	 * @param   array  $b
	 * 
	 * @return  bool
	 */
	public static function compareByThumbnailASC($a, $b)
	{
		return strcmp(basename($a['thumbnail']), basename($b['thumbnail']));
	}

	/**
	 * Compares thumbnail file names in DESC order
	 * 
	 * @param   array  $a
	 * @param   array  $b
	 * 
	 * @return  bool
	 */
	public static function compareByThumbnailDESC($a, $b)
	{
		return strcmp(basename($b['thumbnail']), basename($a['thumbnail']));
	}
}