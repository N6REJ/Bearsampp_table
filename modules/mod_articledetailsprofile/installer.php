<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;

/**
 * Script file for the Article Details Profiles component
 */
class Mod_ArticleDetailsProfileInstallerScript
{
    /**
     * A list of files to be deleted
     */
    protected $deleteFiles = array();
    
    /**
     * A list of folders to be deleted
     */
    protected $deleteFolders = array();
    
    public function __construct($installer)
    {
        Factory::getLanguage()->load('pkg_articledetailsprofiles.sys');
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
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_INSTALLED', Text::_('PKG_ARTICLEDETAILSPROFILES_MODULE_ARTICLEDETAILSPROFILE')), 'message');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($installer)
	{
	    //Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_UNINSTALLED', Text::_('PKG_ARTICLEDETAILSPROFILES_MODULE_ARTICLEDETAILSPROFILE')), 'message');
	}

	/**
	 * method to update the component
	 *
	 * @return boolean True on success
	 */
	public function update($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_UPDATED', Text::_('PKG_ARTICLEDETAILSPROFILES_MODULE_ARTICLEDETAILSPROFILE')), 'message');
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
	    if (Folder::exists(JPATH_SITE . '/modules/mod_articledetailsprofile/images')) {	        
	        $this->deleteFolders[] = '/modules/mod_articledetailsprofile/images';
	    }
	    
	    // +++ End Migration
	    
	    $this->deleteFiles[] = '/modules/mod_articledetailsprofile/mod_articledetailsprofile.php'; // when moving to service providers
	    
	    $this->removeFiles();
	    
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
	            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_ERROR_CANNOTMOVEFILE', $file), 'warning');
	        }
	    }
	    
	    if ($minified_version) {
	        $file_name = File::stripExt($file);
	        $file_extension = File::getExt($file);
	        $file = $file_name . $minified_version . '.' . $file_extension;
	        
	        if (File::exists(JPATH_SITE . $source . '/' . $file)) {
	            if (!$this->isFolderReady($destination) || !File::move(JPATH_SITE . $source . '/' . $file, JPATH_SITE . $destination . '/' . $file)) {
	                Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_ERROR_CANNOTMOVEFILE', $file), 'warning');
	            }
	        }
	    }
	}
	
	private function copyFile($file, $source, $destination)
	{
	    if (File::exists(JPATH_SITE . $source . '/' . $file)) {
	        if (!$this->isFolderReady($destination) || !File::copy(JPATH_SITE . $source . '/' . $file, JPATH_SITE . $destination . '/' . $file)) {
	            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_WARNING_COULDNOTCOPYFILE', $file), 'warning');
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
