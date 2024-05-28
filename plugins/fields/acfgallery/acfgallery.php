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

use NRFramework\Helpers\Widgets\GalleryManager;
use NRFramework\Functions;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\Language\Text;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFGallery extends ACF_Field
{
	/**
	 *  The validation rule will be used to validate the field on saving
	 *
	 *  @var  string
	 */
	protected $validate = 'acfrequired';

    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        // Load Fields Component Helper class
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		$fields = FieldsHelper::getFields('com_users.user', $user, true);

		if (!$fields)
		{
			return true;
		}
		
		// Get the fields data
		$fieldsData = !empty($user['com_fields']) ? $user['com_fields'] : [];

		$this->fixUploadedItems($fields, $fieldsData, (object) $user);
	}

	public function onContentAfterSave($context, $item, $isNew, $data = [])
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

		$this->fixUploadedItems($fields, $fieldsData, $item);
	}

	private function fixUploadedItems($fields = [], $fieldsData = [], $item = [])
	{
		if (!$fields || !$fieldsData || !$item)
		{
			return;
		}

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

		// Loop over the fields
		foreach ($fields as $field)
		{
			$field_type = $field->type;
			
			/**
			 * Check whether a Gallery field is used within the Subform field.
			 */
			if ($field_type === 'subform')
			{
				$submitted_subform_value = array_key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;

				// Ensure it has a value
				if (!$submitted_subform_value || !$subform_value = json_decode($field->rawvalue, true))
				{
					// Update subform field
					$model->setFieldValue($field->id, $item->id, json_encode([]));
					
					continue;
				}

				$update = false;
				$is_subform_non_repeatable = false;

				// Make non-repeatable subform fields a multi array so we can parse them
				if (Functions::startsWith(array_key_first($subform_value), 'field') && $field->fieldparams->get('repeat', '0') === '0')
				{
					$is_subform_non_repeatable = true;
					$subform_value = [$subform_value];
				}

				foreach ($subform_value as $key => &$value)
				{
					foreach ($value as $_key => &$_value)
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

						// $_value is a string when batching an item
						if (!is_array($_value))
						{
							$_value = [];
						}

						// We should run our cleanup routine at the end
						$should_clean = true;
						
						// Move to final folder
						$items = GalleryManager::moveTempItemsToDestination($_value, $subform_field, $this->getDestinationFolder($subform_field, $item));

						// Save item tags
						$items = GalleryManager::saveItemTags($items);

						$_value['items'] = $items;

						$update = true;
					}
				}

				if ($update)
				{
					if ($is_subform_non_repeatable)
					{
						$subform_value = reset($subform_value);
					}

					// Update subform field
					$model->setFieldValue($field->id, $item->id, json_encode($subform_value));
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
	
				// $value is a string when batching an item
				if (!is_array($value))
				{
					$value = [];
				}

				// We should run our cleanup routine at the end
				$should_clean = true;

				// Move to final folder
				$items = GalleryManager::moveTempItemsToDestination($value, $field, $this->getDestinationFolder($field, $item));

				// Save item tags
				$items = GalleryManager::saveItemTags($items);

				$value['items'] = $items;

				// Setting the value for the field and the item
				$model->setFieldValue($field->id, $item->id, json_encode($value));
			}
		}

		if ($should_clean)
		{
			// Clean old files from temp folder
			GalleryManager::clean();
		}
	}

	/**
	 * Returns the destination folder.
	 * 
	 * @param   object  $field
	 * @param   array   $item
	 * 
	 * @return  string
	 */
	private function getDestinationFolder($field, $item)
	{
		$ds = DIRECTORY_SEPARATOR;
		$destination_folder = null;

		$field_id = $field->id;
		$item_id = $item->id;
		
		// Make field params use Registry
		if (!$field->fieldparams instanceof Registry)
		{
			$field->fieldparams = new Registry($field->fieldparams);
		}

		switch ($field->fieldparams->get('upload_folder_type', 'auto'))
		{
			case 'auto':
			default:
				// Get context and remove `com_` part
				$context = preg_replace('/^com_/', '', Factory::getApplication()->input->get('option'));
				$destination_folder = ['media', 'acfgallery', $context, $item_id, $field_id];
				break;
			case 'custom':
				$upload_folder = trim(ltrim($field->fieldparams->get('upload_folder'), $ds), $ds);

				// Smart Tags Instance
				$st = new \NRFramework\SmartTags();

				// Add custom Smart Tags
				$catAlias = isset($item->catid) ? $this->getCategoryAlias($item->catid) : '';

				$custom_tags = [
					'field_id' => $field_id,
					'cat_id' => isset($item->catid) ? $item->catid : '',
					'cat_alias' => $catAlias,
					'item_catid' => isset($item->catid) ? $item->catid : '',
					'item_catalias' => $catAlias,
					'item_id' => $item_id,
					'item_alias' => isset($item->alias) ? $item->alias : '',
					'item_author_id' => isset($item->created_by) ? $item->created_by : ''
				];
				$st->add($custom_tags, 'field.');

				// Replace Smart Tags
				$upload_folder = $st->replace($upload_folder);

				$destination_folder = [$upload_folder];
				break;
		}

		return implode($ds, array_merge([JPATH_ROOT], $destination_folder)) . $ds;
	}

	/**
	 * Returns a category alias by its ID.
	 * 
	 * @param   int     $cat_id
	 * 
	 * @return  string
	 */
	private function getCategoryAlias($cat_id = null)
	{
		if (!$cat_id)
		{
			return;
		}
		
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('alias'))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('id') . ' = ' . (int) $cat_id);
		$db->setQuery($query);

		return $db->loadResult();
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
        
		$fieldNode->setAttribute('field_id', $field->id);

		return $fieldNode;
	}

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param   JForm     $form  The form
	 * @param   stdClass  $data  The data
	 *
	 * @return  void
	 */
	public function onContentPrepareForm(Joomla\CMS\Form\Form $form, $data)
	{
		// Make sure we are manipulating the right field.
		if (isset($data->type) && $data->type != $this->_name)
		{
			return;
		}

		$result = parent::onContentPrepareForm($form, $data);

		// Display the server's maximum upload size in the field's description
		$max_upload_size_str = HTMLHelper::_('number.bytes', Utility::getMaxUploadSize());
		$field_desc = $form->getFieldAttribute('max_file_size', 'description', null, 'fieldparams');
		$form->setFieldAttribute('max_file_size', 'description', Text::sprintf($field_desc, $max_upload_size_str), 'fieldparams');
		
		// Set the Field ID in Upload Folder Type description (if field is saved), otherwise, show FIELD_ID placeholder.
		// ITEM_ID is not replaceable in the field settings.
		$field_id = isset($data->id) ? $data->id : 'FIELD_ID';
		$upload_folder_type_desc = $form->getFieldAttribute('upload_folder_type', 'description', null, 'fieldparams');
		$form->setFieldAttribute('upload_folder_type', 'description', Text::sprintf($upload_folder_type_desc, $field_id), 'fieldparams');

		return $result;
	}
}