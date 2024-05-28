<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use NRFramework\File;
use NRFramework\Mimes;
use NRFramework\Image;
use NRFramework\Functions;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Utility\Utility;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');
JLoader::register('ACFUploadHelper', __DIR__ . '/fields/uploadhelper.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFUpload extends ACF_Field
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
							$subformfields[$field_id] = $subform_field;
						}

						// We should run our cleanup routine at the end
						$should_clean = true;

						// Move to final folder
						$_value = ACFUploadHelper::moveTempItemsToDestination($_value, $subform_field, $item);

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
	
				// We should run our cleanup routine at the end
				$should_clean = true;

				// Move to final folder
				$value = ACFUploadHelper::moveTempItemsToDestination($value, $field, $item);

				// Setting the value for the field and the item
				$model->setFieldValue($field->id, $item->id, json_encode($value));
			}
		}

		if ($should_clean)
		{
			// Clean old files from temp folder
			ACFUploadHelper::clean();
		}
	}

    /**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   Form       $form    The form.
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

        $this->attachEditModal();

        HTMLHelper::stylesheet('plg_system_acf/acf-backend.css', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::script('plg_fields_acfupload/edit-modal.js', ['relative' => true, 'version' => 'auto']);
        
		$fieldNode->setAttribute('field_id', $field->id);

		return $fieldNode;
	}
	
    /**
     * Attaches the edit modal to the page.
     * 
     * @return  void
     */
    private function attachEditModal()
    {
        $form_source = new SimpleXMLElement('
            <form>
                <fieldset name="acfupload_edit_modal">
                    <field name="" type="text"
                        label="ACF_UPLOAD_TITLE"
                        description="ACF_UPLOAD_TITLE_DESC"
                        hint="ACF_UPLOAD_TITLE_HINT"
                        class="acfupload_custom_title_value w-100"
                    />
                    <field name="" type="textarea"
                        label="ACF_UPLOAD_DESCRIPTION"
                        description="ACF_UPLOAD_DESCRIPTION_DESC"
                        hint="ACF_UPLOAD_DESCRIPTION_HINT"
                        class="acfupload_custom_description_value w-100"
						rows="5"
						filter="safehtml"
                    />
                </fieldset>
            </form>
        ');

        $form = Form::getInstance($this->_name, $form_source->asXML(), ['control' => $this->_name]);

        $content = 
        '<div class="acfupload-edit-modal-content">' .
            '<div class="acfupload-edit-modal-editing-item">' . Text::_('ACF_UPLOAD_CURRENTLY_EDITING_ITEM') . '</div>' .
            $form->renderFieldset('acfupload_edit_modal') .
        '</div>';

        echo HTMLHelper::_('bootstrap.renderModal', 'acfUploadItemEditModal', [
            'title'  => Text::_('ACF_UPLOAD_EDIT_ITEM'),
            'modalWidth' => '40',
            'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button><button type="button" class="btn btn-success acf-upload-save-item" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">' . Text::_('JAPPLY') . '</button>'
        ], $content);
    }

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param   Form      $form  The form
	 * @param   stdClass  $data  The data
	 *
	 * @return  void
	 *
	 * @since   3.7.0
	 */
	public function onContentPrepareForm(Joomla\CMS\Form\Form $form, $data)
	{
		// Make sure we are manipulating the right field.
		if (isset($data->type) && ($data->type != $this->_name))
		{
			return;
		}

		$result = parent::onContentPrepareForm($form, $data);

		// Display the server's maximum upload size in the field's description
		$max_upload_size_str = HTMLHelper::_('number.bytes', Utility::getMaxUploadSize());
		$field_desc = $form->getFieldAttribute('max_file_size', 'description', null, 'fieldparams');
		$form->setFieldAttribute('max_file_size', 'description', Text::sprintf($field_desc, $max_upload_size_str), 'fieldparams');

		// If the Fileinfo PHP extension is not installed, display a warning.
		if (!extension_loaded('fileinfo') || !function_exists('mime_content_type'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('ACF_UPLOAD_MIME_CONTENT_TYPE_MISSING'), 'warning');
		}

		return $result;
	}

    /**
     * Handle AJAX endpoint
     *
     * @return void
     */
    public function onAjaxACFUpload()
    {
		if (!Session::checkToken('request'))
        {
        	$this->uploadDie(Text::_('JINVALID_TOKEN'));
        }
		
		$taskMethod = 'task' . ucfirst(Factory::getApplication()->input->get('task', 'upload'));

		if (!method_exists($this, $taskMethod))
		{
			$this->uploadDie('Invalid endpoint');
		}

		$this->$taskMethod();
	}
	
	/**
	 * The Upload task called by the AJAX hanler
	 *
	 * @return void
	 */
	public function taskUpload()
	{
		$input = Factory::getApplication()->input;

		// Make sure we have a valid form and a field key
		if (!$field_id = $input->getInt('id'))
		{
			$this->uploadDie('ACF_UPLOAD_ERROR');
		}

		// Get Upload Settings
		if (!$upload_field_settings = $this->getCustomFieldData($field_id))
		{
			$this->uploadDie('ACF_UPLOAD_ERROR_INVALID_FIELD');
		}

		$allow_unsafe = $upload_field_settings->get('allow_unsafe', false);

		// Make sure we have a valid file passed
		if (!$file = $input->files->get('file', null, ($allow_unsafe ? 'raw' : 'cmd')))
		{
			$this->uploadDie('ACF_UPLOAD_ERROR_INVALID_FILE');
		}

		// In case we allow multiple uploads the file parameter is a 2 levels array.
		$first_property = array_pop($file);
		if (is_array($first_property))
		{
			$file = $first_property;
		}

		// Upload temporarily to the default upload folder
		$allowed_types = $upload_field_settings->get('upload_types');

		try {
			$randomize_filename = $upload_field_settings->get('randomize_filename', false);

			$upload_folder = implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, ACFUploadHelper::getTempFolder()]);

			$uploaded_filename = File::upload($file, $upload_folder, $allowed_types, $allow_unsafe, $randomize_filename ? '' : null);
			$uploaded_filename = str_replace([JPATH_SITE, JPATH_ROOT], '', $uploaded_filename);

			// Resize images
			if ($upload_field_settings->get('resize_images', false))
			{
				// Get file type
				$file_type = Mimes::detectFileType(JPATH_ROOT . $uploaded_filename);

				// Allowed image file types
				$allowed_image_file_types = [
					'gif',
					'jpg',
					'jpeg',
					'jpe',
					'png',
					'image/webp'
				];

				// Ensure it is a valid image
				if (Mimes::check($allowed_image_file_types, $file_type))
				{
					// We require at least width or height to not be null
					$resize_width = $upload_field_settings->get('width', null);
					$resize_height = $upload_field_settings->get('height', null);
					if ($resize_width || $resize_height)
					{
						Image::resizeByWidthOrHeight(JPATH_ROOT . $uploaded_filename, $resize_width, $resize_height);
					}
				}
			}

			$response = [
				'file' => $uploaded_filename,
				'file_encode' => base64_encode($uploaded_filename),
				'url' => ACFUploadHelper::absURL($uploaded_filename)
			];

			header('Content-Type: application/json');

			echo json_encode($response);

			jexit();

		} catch (\Throwable $th)
		{
			$this->uploadDie($th->getMessage());
		}
	}

	/**
	 * The delete task called by the AJAX hanlder
	 *
	 * @return void
	 */
	private function taskDelete()
	{
		// Make sure we have a valid file passed
		if (!$filename = Factory::getApplication()->input->get('file', '', 'BASE64'))
		{
			$this->uploadDie('ACF_UPLOAD_ERROR_INVALID_FILE');
		}

		// Delete the uploaded file
		echo json_encode([
			'success' => ACFUploadHelper::deleteFile(base64_decode($filename))
		]);
	}

	/**
	 * Pull Custom Field Data
	 *
	 * @param  integer $id The Custom Field primary key
	 *
	 * @return object
	 */
    private function getCustomFieldData($id)
    {
        $db = Factory::getDbo();

        $query = $db->getQuery(true);

        $query
            ->select($db->quoteName(['fieldparams']))
            ->from($db->quoteName('#__fields'))
            ->where($db->quoteName('id') . ' = ' . $id)
            ->where($db->quoteName('type') . ' = ' . $db->quote('acfupload'))
            ->where($db->quoteName('state') . ' = 1');

        $db->setQuery($query);

        if (!$result = $db->loadResult())
        {
            return;
        }

        return new Joomla\Registry\Registry($result);
    }

	/**
	 * DropzoneJS detects errors based on the response error code.
	 *
	 * @param  string $error_message
	 *
	 * @return void
	 */
	private function uploadDie($error_message)
	{
		http_response_code('500');
		die(Text::_($error_message));
    }
}
