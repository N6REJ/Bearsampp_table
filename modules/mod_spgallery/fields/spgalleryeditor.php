<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\Field\TextField;
use Joomla\Registry\Registry;

class JFormFieldSPGalleryEditor extends TextField
{
    /**
	 * Generates the Gallery Field
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        require_once __DIR__ . '/helper.php';

        
        $limit_files = (int) $this->form->getData()->get('params.limit_files');
        if ($limit_files < 1 || $limit_files > 8)
        {
            $limit_files = 8;
        }
        
        
        
        $data = [
            'value' => $this->prepareValue(),
            'name' => $limit_files == 1 ? $this->name . '[items][0]' : $this->name . '[items][ITEM_ID]',
            'limit_files' => $limit_files,
            
            'max_file_size' => 5,
            'original_image_resize' => false,
            
            
            'disabled' => $this->disabled,
            'context' => 'module',
            'item_id' => $this->getItemID(),
            'id' => $this->id
        ];

        return \NRFramework\Widgets\Helper::render('GalleryManager', $data);
    }

    private function getItemID()
    {
        $item_id = (int) Factory::getApplication()->input->get('id');

        switch (Factory::getApplication()->input->get('option'))
        {
            case 'com_users':
                $item_id = Factory::getUser()->id;
                break;
        }
        
        return $item_id;
    }

    /**
     * The list of uploaded Gallery Items.
     * 
     * @return  mixed
     */
    private function prepareValue()
    {
        if (empty($this->value))
        {
            return;
        }

        $this->value = is_string($this->value) ? json_decode($this->value, true) : (array) $this->value;

        if (!isset($this->value['items']))
        {
            return;
        }

        $value = [];

        foreach ($this->value['items'] as $key => $file)
        {
            $file = new Registry($file);

            $value[] = [
                'source' => $file->get('source'),
                'original' => $file->get('original') ? $file->get('original') : $file->get('image'),
                'exists' => file_exists(Path::clean(implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, $file->get('thumbnail')]))),
                'caption' => $file->get('caption', ''),
                'thumbnail' => $file->get('thumbnail', ''),
                'alt' => $file->get('alt', ''),
                'is_media_uploader_file' => ($file->get('media_upload_source', 'false') == 'true'),
                'tags' => json_encode($file->get('tags', []))
            ];
        }

        return $value;
    }
}
