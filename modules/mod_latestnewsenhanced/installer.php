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
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Script file of the Latest News Enhanced Pro component
 */
class Mod_LatestNewsEnhancedInstallerScript
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
		return true;
	}

	/**
	 * method to install the component
	 *
	 * @return boolean True on success
	 */
	public function install($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_INSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_MODULE_LATESTNEWSENHANCEDPRO')), 'message');
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($installer)
	{
	    //Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UNINSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_MODULE_LATESTNEWSENHANCEDPRO')), 'message');
	}
	
	/**
	 * method to update the component
	 *
	 * @return boolean True on success
	 */
	public function update($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UPDATED', Text::_('PKG_LATESTNEWSENHANCEDPRO_MODULE_LATESTNEWSENHANCEDPRO')), 'message');
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
	    if ($action === 'uninstall') {
	        return true;
	    }
	    
	    // +++ Migration Joomla 3 to Joomla 4
	    
	    // the old folders have not been removed on update so safe to do it here	    
	    if (Folder::exists(JPATH_SITE . '/modules/mod_latestnewsenhancedextended/images')) {
	        
	        // move user files (substitutes) before the module name change
	        
	        $this->moveFile('common_user_styles.css', '/modules/mod_latestnewsenhancedextended/styles', '/media/mod_latestnewsenhanced/css', '-min');
	        $this->moveFile('substitute_styles.css', '/modules/mod_latestnewsenhancedextended/styles', '/media/mod_latestnewsenhanced/css', '-min');
	        
	        // move known additional files (themes, colors, animations, calendars)
	        // best to re-install (so it can be removed properly, if needed) but help the user here
	        
	        if (Folder::exists(JPATH_SITE . '/modules/mod_latestnewsenhancedextended/styles/overall/csseffect1')) {
	            
	            $this->moveFile('csseffect1.png', '/modules/mod_latestnewsenhancedextended/styles/overall/csseffect1', '/media/mod_latestnewsenhanced/images/themes');
	            $this->moveFile('csseffect1_hover.png', '/modules/mod_latestnewsenhancedextended/styles/overall/csseffect1', '/media/mod_latestnewsenhanced/images/themes');
	            
	            $this->moveFile('style.css.php', '/modules/mod_latestnewsenhancedextended/styles/overall/csseffect1', '/media/mod_latestnewsenhanced/styles/themes/csseffect1');
	        }
	        
	        if (Folder::exists(JPATH_SITE . '/media/syw_latestnewsenhanced/colors/eggplant')) {
	            
	            $this->moveFile('eggplant.png', '/media/syw_latestnewsenhanced/colors/eggplant', '/media/mod_latestnewsenhanced/images/colors');
	            $this->moveFile('style.css.php', '/media/syw_latestnewsenhanced/colors/eggplant', '/media/mod_latestnewsenhanced/styles/colors/eggplant');
	        }
	        
	        // OWL carousel will need to be re-installed
	        
	        // delete old media folder
	        // DO NOT REMOVE to allow users to move their own calendars
	        
	        //$this->deleteFolders[] = '/media/syw_latestnewsenhanced';
	        
	        // remove data from /cache if coming from Joomla 3.10
	        
	        $this->deleteFolders[] = '/cache/mod_latestnewsenhancedpro';
	        $this->deleteFolders[] = '/cache/mod_latestnewsenhanced';
	        
	        // no need to delete module files since the old modules are uninstalled and replaced with new one
	        
	        // delete files and folders for module, if free extension was installed previously and not properly removed
	        
	        $this->deleteFiles[] = '/modules/mod_latestnewsenhanced/headerfilesmaster.php';
	        
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/animations';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/fields';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/helpers';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/images';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/js';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/layouts';
	        $this->deleteFolders[] = '/modules/mod_latestnewsenhanced/styles';
	    }
	    
	    // +++ End Migration
	    
	    if ($action == 'install' || $action == 'update') {
	        
	        // fix manual configuration errors made before v6.8.0
	        // v6.8.0 does not return any more results when authors are excluded and authors are set to 'all'
	        
	        $this->fixConfigErrors();	        
	    }
	    
	    if ($action === 'update') {
	        
	        // remove files
	        
	        $this->deleteFolders[] = '/media/cache/mod_latestnewsenhancedpro';
	        $this->deleteFiles[] = '/media/mod_latestnewsenhanced/css/common_styles-min.css';
	    }
	    
	    $this->removeFiles();
	    
	    return true;
	}
	
	private function fixConfigErrors()
	{
	    $db = Factory::getDBO();
	    
	    $query = $db->getQuery(true);
	    
	    $query->select('id');
	    $query->select('params');
	    $query->from('#__modules');
	    $query->where($db->quoteName('module') . '=' . $db->quote('mod_latestnewsenhanced'));
	    
	    $db->setQuery($query);
	    
	    $lne_instances = array();
	    try {
	        $lne_instances = $db->loadObjectList();
	    } catch (ExecutionFailureException $e) {
	        return false;
	    }
	    
	    foreach ($lne_instances as $lne_instance) {
	        
	        $instance_params = json_decode($lne_instance->params, true);
	        
	        $changes_made = false;
	        
	        if (!isset($instance_params['author_match'])) { // before 6.8.0, 'author_match' does not exist
	            if (isset($instance_params['author_inex']) && (int)$instance_params['author_inex'] === 0) { // 'excluded' is selected
	                if (isset($instance_params['datasource'])) {
	                    
	                    $created_by = '';
	                    if ($instance_params['datasource'] === 'articles') {
	                        $created_by = 'created_by';
	                    } else if ($instance_params['datasource'] === 'k2') {
	                        $created_by = 'k2_created_by';
	                    }
	                    
	                    if ($created_by && isset($instance_params[$created_by])) {
	                        $authors_array = (array)$instance_params[$created_by];
    	                    $array_of_authors_values = array_count_values($authors_array);
    	                    if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected
    	                        $changes_made = true;
    	                        $instance_params['author_inex'] = 1;
    	                    }
    	                }
	                }
	            }
	        }
	        
	        if ($changes_made) {
	            
	            $query->clear();
	            
	            $query->update('#__modules');
	            $query->set($db->quoteName('params') . '=' . $db->quote(json_encode($instance_params)));
	            $query->where($db->quoteName('id') . '=' . $db->quote($lne_instance->id));
	            
	            $db->setQuery($query);
	            
	            try {
	                $db->execute();
	            } catch (ExecutionFailureException $e) {
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
