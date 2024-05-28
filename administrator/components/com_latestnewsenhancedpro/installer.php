<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;

/**
 * Script file of the Latest News Enhanced Pro component
 */
class Com_LatestNewsEnhancedProInstallerScript
{
    /**
     * A list of files to be deleted
     */
    protected $deleteFiles = array();
    
    /**
     * A list of folders to be deleted
     */
    protected $deleteFolders = array();
    
    public function __construct($parent)
    {
        Factory::getLanguage()->load('pkg_latestnewsenhancedpro.sys');
    }

	/**
	 * Called before any type of action
	 *
	 * @param string $action Which action is happening (install|uninstall|discover_install|update)
	 * @param InstallerAdapter $installer The class calling this method
	 *
	 * @return boolean True on success
	 */
	public function preflight($action, $installer)
	{
	    // +++ Migration Joomla 3 to Joomla 4
	    
	    // the old folders only exist at this point
	    if (Folder::exists(JPATH_SITE . '/administrator/components/com_latestnewsenhancedpro/assets')) {
	        
	        // move known additional files (themes, colors, animations, calendars)
	        // best to re-install (so it can be removed properly, if needed) but help the user here
	        
	        if (Folder::exists(JPATH_SITE . '/components/com_latestnewsenhancedpro/assets/styles/themes/csseffect1')) {
	            
	            $this->moveFile('csseffect1.png', '/components/com_latestnewsenhancedpro/assets/styles/themes/csseffect1', '/media/com_latestnewsenhancedpro/images/themes');
	            $this->moveFile('csseffect1_hover.png', '/components/com_latestnewsenhancedpro/assets/styles/themes/csseffect1', '/media/com_latestnewsenhancedpro/images/themes');
	            
	            $this->moveFile('style.css.php', '/components/com_latestnewsenhancedpro/assets/styles/themes/csseffect1', '/media/com_latestnewsenhancedpro/styles/themes/csseffect1');
	        }
	        
	        // delete old media folder
	        // DO NOT REMOVE to allow users to move their own calendars
	        
	        //$this->deleteFolders[] = '/media/syw_latestnewsenhanced';
	        
	        // remove data from /cache if coming from Joomla 3.10
	        
	        $this->deleteFolders[] = '/cache/com_latestnewsenhancedpro';
	        
	        // delete files and folders for component - NOT NECESSARY
	        
// 	        $this->deleteFolders[] = '/administrator/components/com_latestnewsenhancedpro/assets';
// 	        $this->deleteFolders[] = '/administrator/components/com_latestnewsenhancedpro/controllers';
// 	        $this->deleteFolders[] = '/administrator/components/com_latestnewsenhancedpro/helpers';
// 	        $this->deleteFolders[] = '/administrator/components/com_latestnewsenhancedpro/models';
// 	        $this->deleteFolders[] = '/administrator/components/com_latestnewsenhancedpro/views';
	        
// 	        $this->deleteFiles[] = '/administrator/components/com_latestnewsenhancedpro/controller.php';
// 	        $this->deleteFiles[] = '/administrator/components/com_latestnewsenhancedpro/latestnewsenhancedpro.php';
	        
// 	        $this->deleteFolders[] = '/components/com_latestnewsenhancedpro/assets';
// 	        $this->deleteFolders[] = '/components/com_latestnewsenhancedpro/controllers';
// 	        $this->deleteFolders[] = '/components/com_latestnewsenhancedpro/helpers';
// 	        $this->deleteFolders[] = '/components/com_latestnewsenhancedpro/models';
// 	        $this->deleteFolders[] = '/components/com_latestnewsenhancedpro/views';
	        
// 	        $this->deleteFiles[] = '/components/com_latestnewsenhancedpro/controller.php';
// 	        $this->deleteFiles[] = '/components/com_latestnewsenhancedpro/latestnewsenhancedpro.php';
// 	        $this->deleteFiles[] = '/components/com_latestnewsenhancedpro/router.php';
	        
	        $this->removeFiles();
	    }
	    
	    // +++ End Migration   
	    
		return true;
	}

	/**
	 * method to install the component
	 *
	 * @return boolean True on success
	 */
	public function install($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_INSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_COMPONENT_LATESTNEWSENHANCEDPRO')), 'message');
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($installer)
	{
	    //Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UNINSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_COMPONENT_LATESTNEWSENHANCEDPRO')), 'message');
	}
	
	/**
	 * method to update the component
	 *
	 * @return boolean True on success
	 */
	public function update($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UPDATED', Text::_('PKG_LATESTNEWSENHANCEDPRO_COMPONENT_LATESTNEWSENHANCEDPRO')), 'message');
	}
	
	/**
	 * Called after any type of action
	 *
	 * @param string $action Which action is happening (install|uninstall|discover_install|update)
	 * @param InstallerAdapter $installer The object responsible for running this script
	 *
	 * @return boolean True on success
	 */
	public function postflight($action, $installer)
	{
	    if ($action == 'install' || $action == 'update') {
	        
	        // fix manual configuration errors made before v6.8.0
	        // v6.8.0 does not return any more results when authors are excluded and authors are set to 'all'
	        
	        $this->fixConfigErrors();
	    }

	    return true;
	}
	
	private function fixConfigErrors()
	{
	    $db = Factory::getDBO();
	    
	    $query = $db->getQuery(true);
	    
	    $query->select($db->quoteName('id'));
	    $query->select($db->quoteName('params'));
	    $query->from($db->quoteName('#__menu'));
	    $query->where(array('link LIKE '.$db->quote('%option=com_latestnewsenhancedpro&view=articles%'), 'link LIKE '.$db->quote('%option=com_latestnewsenhancedpro&view=k2items%')), 'OR');
	    
	    $db->setQuery($query);
	    
	    $views = array();
	    try {
	        $views = $db->loadObjectList();
	    } catch (\RuntimeException $e) {
	        return false;
	    }
	    
	    foreach ($views as $view) {
	        
	        $view_params = json_decode($view->params, true);
	        
	        $changes_made = false;
	        
	        if (!isset($view_params['author_match'])) { // before 6.8.0, 'author_match' does not exist
	            if (isset($view_params['author_inex']) && (int)$view_params['author_inex'] === 0) { // 'excluded' is selected
	                if (isset($view_params['created_by'])) {
	                    $authors_array = (array)$view_params['created_by'];
	                    $array_of_authors_values = array_count_values($authors_array);
	                    if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected
	                        $changes_made = true;
	                        $view_params['author_inex'] = 1;
	                    }
	                }
	            }
	        }
	        
	        if ($changes_made) {
	            
	            $query->clear();
	            
	            $query->update('#__menu');
	            $query->set($db->quoteName('params') . '=' . $db->quote(json_encode($view_params)));
	            $query->where($db->quoteName('id') . '=' . $db->quote($view->id));
	            
	            $db->setQuery($query);
	            
	            try {
	                $db->execute();
	            } catch (\RuntimeException $e) {
	                return false;
	            }
	        }
	    }
	    
	    return true;
	}
	
	private function isFolderReady($extra_path)
	{
	    $path = JPATH_SITE;
	    $folders = explode('/', trim($extra_path, '/'));
	    
	    foreach ($folders as $folder) {
	        $path .= '/' . $folder;
	        if (!Folder::exists($path)) {
	            if (Folder::create($path)) {
	            } else {
	                return false;
	            }
	        }
	    }
	    
	    return true;
	}
	
	private function moveFile($file, $source, $destination, $minified_version = '')
	{
	    if (File::exists(JPATH_SITE . $source . '/' . $file)) {
	        if (!$this->isFolderReady($destination) || !File::move(JPATH_SITE . $source . '/' . $file, JPATH_SITE . $destination . '/' . $file)) {
	            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_ERROR_CANNOTMOVEFILE', $file), 'warning');
	        }
	    }
	    
	    if ($minified_version) {
	        $file_name = File::stripExt($file);
	        $file_extension = File::getExt($file);
	        $file = $file_name . $minified_version . '.' . $file_extension;
	        
	        if (File::exists(JPATH_SITE . $source . '/' . $file)) {
	            if (!$this->isFolderReady($destination) || !File::move(JPATH_SITE . $source . '/' . $file, JPATH_SITE . $destination . '/' . $file)) {
	                Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_ERROR_CANNOTMOVEFILE', $file), 'warning');
	            }
	        }
	    }
	}
	
	private function copyFile($file, $source, $destination)
	{
	    if (File::exists(JPATH_SITE . $source . '/' . $file)) {
	        if (!$this->isFolderReady($destination) || !File::copy(JPATH_SITE . $source . '/' . $file, JPATH_SITE . $destination . '/' . $file)) {
	            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTCOPYFILE', $file), 'warning');
	        }
	    }
	}
	
	/**
	 * Remove the files and folders in the given array from
	 */
	private function removeFiles()
	{
	    if (!empty($this->deleteFiles))
	    {
	        foreach ($this->deleteFiles as $file)
	        {
	            if (file_exists(JPATH_ROOT . $file) && !File::delete(JPATH_ROOT . $file))
	            {
	                echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $file) . '<br>';
	            }
	        }
	    }
	    
	    if (!empty($this->deleteFolders))
	    {
	        foreach ($this->deleteFolders as $folder)
	        {
	            if (Folder::exists(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder))
	            {
	                echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $folder) . '<br>';
	            }
	        }
	    }
	}

}
