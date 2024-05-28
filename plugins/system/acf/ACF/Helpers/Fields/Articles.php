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
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use Joomla\CMS\Factory;

class Articles
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
		if ($field->only_use_in_subform === 1)
		{
			return;
		}

		$max_articles = (int) $field->fieldparams->get('max_articles', 0);
		$multiple = $max_articles === 0 || $max_articles > 1;
		return $multiple ? ['listOf' => 'Article'] : 'Article';
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

        $ids = $field->rawvalue;

		$max_articles = 0;
		$multiple = true;

		// Handle Linked Articles
		$field_type = $field->fieldparams->get('articles_type', 'default');
		if ($field_type === 'linked')
		{
			
			if (in_array($ids, ['1', 'true']))
			{
				$ids = self::getLinkedArticlesIDs($item->id, $field);
			}
			
		}
		else
		{
			$max_articles = (int) $field->fieldparams->get('max_articles', 0);
			$multiple = $max_articles === 0 || $max_articles > 1;
		}

		if (is_scalar($ids))
		{
			$ids = [(int) $ids];
		}

        if (!is_array($ids))
        {
            return;
        }

		$value = ArticleHelper::get($ids);

		if ($field->only_use_in_subform === 1)
		{
			$data = array_values(array_map(function($article) {
				return $article->title;
			}, $value));

			return implode(', ', $data);
		}
		
		if ($multiple)
		{
            return array_values(array_map(function ($value) {
                return is_scalar($value) ? compact('value') : $value;
            }, $value));
        }
		else
		{
            return array_shift($value);
        }

	}

	
	public static function getLinkedArticlesIDs($item_id = null, $field = [])
	{
		if (!$acf_articles = array_filter($field->fieldparams->get('articles_fields', [])))
		{
			return;
		}

		// Grab all linked articles
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id')
			->from($db->quoteName('#__fields_values', 'fv'))
			->join('LEFT', $db->quoteName('#__content', 'a') . ' ON a.id = fv.item_id')
			->where($db->quoteName('fv.field_id') . ' IN (' . implode(',', $acf_articles) . ')')
			->where($db->quoteName('fv.value') . ' = ' . $db->q($item_id));
		
		// Filter results
        require_once JPATH_PLUGINS . '/fields/acfarticles/fields/acfarticlesfilters.php';
		$payload = $field->fieldparams;
        $filters = new \ACFArticlesFilters($query, $payload);
		$query = $filters->apply();

		// Set query
		$db->setQuery($query);

		// Get articles
		if (!$articles = $db->loadAssocList())
		{
			return;
		}

		// Return article IDs
		$articles = array_map(function($article) {
			return $article['id'];
		}, $articles);

        return $articles;
	}
	
}