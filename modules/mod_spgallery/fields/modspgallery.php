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

use NRFramework\Helpers\Widgets\GalleryManager;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

class ModSPGalleryInstance
{
	public function onContentPrepareForm(Form &$form, $data)
    {
        if ($data->module !== 'mod_spgallery')
        {
            return $form;
        }
        
		// Display the server's maximum upload size in the field's description
		$max_upload_size_str = HTMLHelper::_('number.bytes', Utility::getMaxUploadSize());
        
        
		$field_desc = $form->getFieldAttribute('@max_file_size', 'description', null, 'params');
		$form->setFieldAttribute('@max_file_size', 'description', Text::sprintf($field_desc, $max_upload_size_str), 'params');
        
    }

    
    /**
     * Runs when the module is saved.
     * 
     * @param   object  $context
     * @param   object  $table
     * @param   bool    $isNew
     * 
     * @return  bool
     */
	public function onExtensionBeforeSave($context, $table, $isNew)
	{
        $params = $table->params;

        if (!$params = json_decode($params, true))
        {
            return true;
        }

        

        $pass = true;

        $limit_files = isset($params['limit_files']) ? (int) $params['limit_files'] : 8;
        if ($limit_files < 1 || $limit_files > 8)
        {
            Factory::getApplication()->enqueueMessage(Text::_('MOD_SPGALLERY_LIMIT_FILES_INVALID'), 'error');
            $pass = false;
        }

        return $pass;
    }
    

    /**
     * Runs when the module is saved.
     * 
     * @param   object  $context
     * @param   object  $table
     * @param   bool    $isNew
     * 
     * @return  void
     */
    public function onExtensionAfterSave($context, $table, $isNew)
	{
        $params = $table->params;

        if (!$params = json_decode($params, true))
        {
            return;
        }

        if (!isset($params['value']['items']))
        {
            return;
        }
        
        if (!is_array($params['value']['items']))
        {
            return;
        }
        
        if (!count($params['value']['items']))
        {
            return;
        }

        $fieldParams = (object) [
            'fieldparams' => $params
        ];

        
        $limit_files = 8;

        
        
        // Limit items to limit files value
        if ($limit_files > 0)
        {
            $params['value']['items'] = array_slice($params['value']['items'], 0, $limit_files);
        }
        

        $items = GalleryManager::moveTempItemsToDestination($params['value']['items'], $fieldParams, $this->getDestinationFolder($table, $params));

		// Save item tags
		$items = GalleryManager::saveItemTags($items);

        $params['value']['items'] = $items;
        
        $table->bind(['params' => json_encode($params)]);
        $table->check();
        $table->store();
    }

    
    
	/**
	 * Returns the destination folder.
	 * 
	 * @param   array  $module
	 * @param   array  $params
	 * 
	 * @return  string
	 */
	private function getDestinationFolder($module, $params)
	{
		$ds = DIRECTORY_SEPARATOR;
        $destination_folder = ['media', 'spgallery', $module->id];

        

		return implode($ds, array_merge([JPATH_ROOT], $destination_folder)) . $ds;
	}
}