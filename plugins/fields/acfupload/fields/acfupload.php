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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Filesystem\Path;

class JFormFieldACFUpload extends TextField
{
    /**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        if ($this->element['limit_files'] != 1)
        {
            HTMLHelper::script('plg_fields_acfupload/vendor/sortable.min.js', ['relative' => true, 'version' => 'auto']);
        }

        HTMLHelper::script('plg_fields_acfupload/vendor/dropzone.min.js', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::script('plg_fields_acfupload/acfupload.js', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::stylesheet('plg_fields_acfupload/acfupload.css', ['relative' => true, 'version' => 'auto']);

        // Add language strings used by script
        Text::script('ACF_UPLOAD_FILETOOBIG');
        Text::script('ACF_UPLOAD_INVALID_FILE');
        Text::script('ACF_UPLOAD_FALLBACK_MESSAGE');
        Text::script('ACF_UPLOAD_RESPONSE_ERROR');
        Text::script('ACF_UPLOAD_CANCEL_UPLOAD');
        Text::script('ACF_UPLOAD_CANCEL_UPLOAD_CONFIRMATION');
        Text::script('ACF_UPLOAD_REMOVE_FILE');
        Text::script('ACF_UPLOAD_MAX_FILES_EXCEEDED');
        Text::script('ACF_UPLOAD_FILE_MISSING');
        Text::script('ACF_UPLOAD_REMOVE_FILE_CONFIRM');

        // Render File Upload Field
        $data = [
            'id'                  => $this->id,
            'value'               => $this->prepareValue(),
            'input_name'          => $this->element['limit_files'] == 1 ? $this->name : $this->name . '[INDEX]',
            'required'            => ((string) $this->element['required'] == 'true' ? true : false),
            'field_id'            => $this->element['field_id'],
            'max_file_size'       => $this->element['max_file_size'],
            'limit_files'         => $this->element['limit_files'],
            'multiple'            => $this->element['limit_files'] == 1 ? false : true,
            'upload_types'        => $this->element['upload_types'],
            'disabled'            => $this->disabled,
            'show_download_links' => isset($this->element['show_download_links']) && $this->element['show_download_links'] == 1 ? true : false,
            'base_url'            => Uri::base() // AJAX endpoint works on both site and backend.
        ];

		$layout = new FileLayout('acfuploadlayout', __DIR__);
        return $layout->render($data);
    }

    /**
     * Prepare the value.
     * 
     * @return  void
     */
    private function prepareValue()
    {
        if (empty($this->value))
        {
            return;
        }

        require_once __DIR__ . '/uploadhelper.php';

        $limit_files = (int) $this->element['limit_files'];

        /**
         * This handles backwards compatibility
         */
        $files = is_string($this->value) ? json_decode($this->value, true) ?? [['value' => $this->value]] : $this->value;

        // Ensure its an array. If it was saved via a Subform field, it would return us an array of
        $files = json_decode(json_encode($files), true);

        // Handle single file
        if ($limit_files === 1 && is_array($files))
        {
            if (isset($files['value']))
            {
                $files = [$files];
            }

            $files = [reset($files)];
        }

        if (!$files)
        {
            return;
        }
        
        $return = [];
        
        foreach ($files as $value)
        {
            if (!$value)
            {
                continue;
            }

            $file = isset($value['value']) ? $value['value'] : $value;
            $title = isset($value['title']) ? $value['title'] : '';
            $description = isset($value['description']) ? $value['description'] : '';

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);

            $file_path = Path::clean(implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, $file]));
            $exists    = is_file($file_path);
            $file_size = $exists ? filesize($file_path) : 0;

            $return[] = [
                'title' => $title,
                'description' => $description,
                'name'    => $filePathInfo['basename'],
                'path'    => $file,
                'encoded' => base64_encode($file),
                'url'     => ACFUploadHelper::absURL($file_path),
                'size'    => $file_size,
                'exists'  => $exists
            ];
        }

        return $return;
    }
}