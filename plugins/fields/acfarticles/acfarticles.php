<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use NRFramework\Conditions\Conditions\Component\ContentBase;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use NRFramework\Cache;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFArticles extends ACF_Field
{
	/**
	 * Update the label of the field in filters.
     * 
     * @param \Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection $options
	 * 
     * @return \Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection
     */
    public function onJFiltersOptionsAfterCreation(\Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection $options) 
    {
		// Make sure it is a field of that type
        if ($options->getFilterItem()->getAttributes()->get('type') !== $this->_name)
		{
            return $options;
        }

        $contentAssignment = new ContentBase();

        foreach ($options as $option)
		{
			if (!$article = $contentAssignment->getItem($option->getLabel()))
			{
				continue;
			}

			$option->setLabel($article->title);
        }

        return $options;
	}

	
	public function onContentBeforeSave($context, $item, $isNew, $data = [])
	{
		if (!is_array($data))
		{
			return true;
		}
		
		if (!isset($data['com_fields']))
		{
			return true;
		}
		
		// Create correct context for category
		if ($context == 'com_categories.category')
		{
			$context = $item->get('extension') . '.categories';
		}

        // Load Fields Component Helper class
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// Check the context
		$parts = FieldsHelper::extract($context, $item);

		if (!$parts)
		{
			return true;
		}

		// Compile the right context for the fields
		$context = $parts[0] . '.' . $parts[1];

		// Loading the fields
		$fields = FieldsHelper::getFields($context, $item);

		if (!$fields)
		{
			return true;
		}

		// Get the fields data
		$fieldsData = !empty($data['com_fields']) ? $data['com_fields'] : [];

		// Whether we should clean up the temp folder at the end of this process
		$should_clean = false;

		// Get the Fields Model
		if (!defined('nrJ4'))
		{
			$model = JModelLegacy::getInstance('Field', 'FieldsModel', ['ignore_request' => true]);
		}
		else
		{
			$model = Factory::getApplication()->bootComponent('com_fields')->getMVCFactory()->createModel('Field', 'Administrator', ['ignore_request' => true]);
		}

		// Cache subform fields
		$subform_fields = [];

		$error = false;

		// Loop over the fields
		foreach ($fields as $field)
		{
			$field_type = $field->type;

			/**
			 * Check whether a Gallery field is used within the Subform field.
			 */
			if ($field_type === 'subform')
			{
				// Ensure it has a value
				if (!$subform_value = json_decode($field->value, true))
				{
					continue;
				}
				
				foreach ($subform_value as $key => $value)
				{
					if (!is_array($value))
					{
						continue;
					}
					
					foreach ($value as $_key => $_value)
					{
						// Get Field ID
						$field_id = str_replace('field', '', $_key);
						
						// Get Field by ID
						$subform_field = isset($subform_fields[$field_id]) ? $subform_fields[$field_id] : $model->getItem($field_id);

						// Only proceed for this field type
						if ($subform_field->type !== $this->_name)
						{
							continue;
						}

						// Cache field
						if (!isset($subform_fields[$field_id]))
						{
							$subform_fields[$field_id] = $subform_field;
						}

						$check_value = isset($fieldsData[$field->name][$key][$_key]) ? $fieldsData[$field->name][$key][$_key] : false;

						if (!$check_value)
						{
							break;
						}
						
						$fieldParams = new Registry($subform_field->fieldparams);

						$check_value = is_array($check_value) ? $check_value : [$check_value];

						if ($min_articles = (int) $fieldParams->get('min_articles', 0))
						{
							if (count($check_value) < $min_articles)
							{
								Factory::getApplication()->enqueueMessage(sprintf(Text::_('ACF_ARTICLES_MIN_ITEMS_REQUIRED'), $subform_field->title, $min_articles), 'error');
								$error = true;
								break;
							}
						}
		
						if ($max_articles = (int) $fieldParams->get('max_articles', 0))
						{
							if (count($check_value) > $max_articles)
							{
								Factory::getApplication()->enqueueMessage(sprintf(Text::_('ACF_ARTICLES_MAX_ITEMS_REQUIRED'), $subform_field->title, $max_articles), 'error');
								$error = true;
								break;
							}
						}
					}
				}
			}
			else
			{
				// Only proceed for this field type
				if ($field_type !== $this->_name)
				{
					continue;
				}
	
				// Determine the value if it is available from the data
				$value = array_key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;
	
				if (!$value)
				{
					continue;
				}

				$value = is_array($value) ? $value : [$value];

				if ($min_articles = (int) $field->fieldparams->get('min_articles', 0))
				{
					if (count($value) < $min_articles)
					{
						Factory::getApplication()->enqueueMessage(sprintf(Text::_('ACF_ARTICLES_MIN_ITEMS_REQUIRED'), $field->title, $min_articles), 'error');
						$error = true;
						break;
					}
				}

				if ($max_articles = (int) $field->fieldparams->get('max_articles', 0))
				{
					if (count($value) > $max_articles)
					{
						Factory::getApplication()->enqueueMessage(sprintf(Text::_('ACF_ARTICLES_MAX_ITEMS_REQUIRED'), $field->title, $max_articles), 'error');
						$error = true;
						break;
					}
				}
			}
		}

		return !$error;
	}
	
	
	/**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   Form        $form    The form.
	 *
	 * @return  DOMElement
	 *
	 * @since   3.7.0
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, Joomla\CMS\Form\Form $form)
	{
		if (!$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form))
		{
			return $fieldNode;
		}

		$field_type = $field->fieldparams->get('articles_type', 'default');

		$fieldNode->setAttribute('multiple', true);

		
		$max_articles = (int) $field->fieldparams->get('max_articles', 0);
		if ($max_articles === 1)
		{
			$fieldNode->setAttribute('multiple', false);
		}
		
		// Linked
		if ($field_type === 'linked')
		{
			$fieldNode->setAttribute('multiple', false);
			$fieldNode->setAttribute('type', 'NRToggle');
			// Default state
			$fieldNode->setAttribute('default', 'false');

			// Checked if Default Value = 1/true
			if (in_array($field->value, ['1', 'true']))
			{
				$fieldNode->setAttribute('checked', 'true');
			}
		}
		

		return $fieldNode;
	}

	/**
	 * Prepares the field value for the (front-end) layout
	 *
	 * @param   string    $context  The context.
	 * @param   stdclass  $item     The item.
	 * @param   stdclass  $field    The field.
	 *
	 * @return  string
	 */
	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		// Check if the field should be processed by us
		if (!$this->isTypeSupported($field->type))
		{
			return parent::onCustomFieldsPrepareField($context, $item, $field);
		}

		$value = array_filter((array) $field->value);
		
		
		$type = $field->fieldparams->get('articles_type', 'default');
		
		// Linked articles
		if ($type === 'linked')
		{
			// Abort if no ACF Articles fields are selected
			if (!$acf_articles = array_filter($field->fieldparams->get('articles_fields', [])))
			{
				$field->value = [];
				return parent::onCustomFieldsPrepareField($context, $item, $field);
			}

			// Proceed only if Field Value = 1/true
			if (!in_array($field->value, ['1', 'true']))
			{
				$field->value = [];
				return parent::onCustomFieldsPrepareField($context, $item, $field);
			}

			$cache_key = 'ACFArticles_' . $item->id . '_' . md5(implode(',', $acf_articles));
			
			if (Cache::has($cache_key))
			{
				$field->value = Cache::get($cache_key);
			}
			else
			{
				// Grab all linked articles
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('a.*')
					->from($db->quoteName('#__fields_values', 'fv'))
					->join('LEFT', $db->quoteName('#__content', 'a') . ' ON a.id = fv.item_id')
					->where($db->quoteName('fv.field_id') . ' IN (' . implode(',', $acf_articles) . ')')
					->where($db->quoteName('fv.value') . ' = ' . $db->q($item->id));
	
				$this->prepareArticles($context, $query, $field, $value, true);

				Cache::set($cache_key, $field->value);
			}
			
			return parent::onCustomFieldsPrepareField($context, $item, $field);
		}
		

		if (!$value)
		{
			return parent::onCustomFieldsPrepareField($context, $item, $field);
		}

		$cache_key = 'ACFArticles_Auto_' . $item->id . '_' . md5(implode(',', $value));
			
		if (Cache::has($cache_key))
		{
			$field->value = Cache::get($cache_key);
		}
		else
		{
			// Default articles
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.*')
				->from($db->quoteName('#__content', 'a'))
				->where($db->quoteName('a.id') . ' IN (' . implode(',', $value) . ')');
	
			$this->prepareArticles($context, $query, $field, $value);

			Cache::set($cache_key, $field->value);
		}

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}

	/**
	 * Prepares the articles.
	 * 
	 * @param   string    $context
	 * @param   object    $query
	 * @param   object    $field
	 * @param   array     $articles
	 * @param   bool      $all_filters
	 * 
	 * @return  void
	 */
	private function prepareArticles($context, $query, &$field, $articles, $all_filters = true)
	{
		$db = Factory::getDbo();

		// Filter results
        require_once 'fields/acfarticlesfilters.php';
		$payload = $all_filters ? $field->fieldparams : ['order' => $field->fieldparams->get('order')];
        $filters = new ACFArticlesFilters($query, $payload);
		$query = $filters->apply();

		// Set query
		$db->setQuery($query);

		// Get articles
		if (!$articles = $db->loadAssocList())
		{
			$field->value = [];
			return;
		}

		
		// Get and set the articles values
		foreach ($articles as &$article)
		{
			$article['custom_fields'] = $this->fetchCustomFields($article);
		}
		

		$field->value = $articles;
	}

	
    /**
     * Returns an article's custom fields.
     *
     * @return  array
     */
    private function fetchCustomFields($article = null)
    {
        if (!$article)
        {
            return [];
        }

        $callback = function() use ($article)
        {
			\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

            if (!$fields = \FieldsHelper::getFields('com_content.article', $article, false))
            {
                return [];
            }

            $fieldsAssoc = [];

            foreach ($fields as $field)
            {
                $fieldsAssoc[$field->name] = $field->value;
            }

            return $fieldsAssoc;
        };

        return Cache::memo('ACFArticlesFetchCustomFields' . $article['id'], $callback);
    }
	
}