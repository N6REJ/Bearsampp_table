<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Script file for the Latest News Enhanced Pro Newsfeeds plugin
 */
class plglatestnewsenhancednewsfeedsInstallerScript extends InstallerScript
{
	/**
	 * Minimum LNEP version required to install the extension
	 */
	protected $minimumLNEP = '7.0.0';

	/**
	 * LNEP extension link for download
	 */
	protected $LNEPDownloadLink = 'https://simplifyyourweb.com/downloads/latest-news-enhanced-pro';
	
	/**
	 * Link to the change logs
	 */
	protected $changelogLink = 'https://simplifyyourweb.com/downloads/latest-news-enhanced-pro/data-sources/file/437-newsfeeds'; // to change for every new compatible version

	/**
	 * Extension script constructor
	 */
	public function __construct($installer)
	{
	    $this->extension = 'plg_latestnewsenhanced_newsfeeds';
	    $this->minimumJoomla = '4.1.0';
	    //$this->minimumPhp = JOOMLA_MINIMUM_PHP; // not needed
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
	    if ($action === 'uninstall') {
	        return true;
	    }
	    
	    // checks minimum PHP and Joomla versions and that an upgrade is performed	    
	    if (!parent::preflight($action, $installer)) {
            return false;
	    }

		// check if Latest News Enhanced Extended Pro is installed and compatible

		if (!Folder::exists(JPATH_ROOT.'/modules/mod_latestnewsenhanced')) {

			$message = Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_MISSING_MODULE').'.<br /><a href="'.$this->LNEPDownloadLink.'" target="_blank">'.Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_DOWNLOAD_MODULE').'</a>.';
			Factory::getApplication()->enqueueMessage($message, 'error');
			return false;

		} else {
			$module_version = strval(simplexml_load_file(JPATH_SITE . '/modules/mod_latestnewsenhanced/mod_latestnewsenhanced.xml')->version);

			if (version_compare($module_version, $this->minimumLNEP, 'ge')) {
				Factory::getApplication()->enqueueMessage(Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_COMPATIBLE_MODULE'), 'message');
			} else {
				$message = Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_NONCOMPATIBLE_MODULE').'.<br /><a href="'.$this->LNEPDownloadLink.'" target="_blank">'.Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_UPDATE_MODULE').'</a>.';
				Factory::getApplication()->enqueueMessage($message, 'error');
				return false;
			}
		}

		return true;
	}

	/**
	 * method to install the component
	 *
	 * @return boolean True on success
	 */
	public function install($installer) {}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($installer) {}

	/**
	 * method to update the component
	 *
	 * @return boolean True on success
	 */
	public function update($installer) {}
	
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

		echo '<p style="margin: 20px 0">';
		echo '<span class="badge bg-dark">'.Text::sprintf('PLG_LATESTNEWSENHANCED_NEWSFEEDS_VERSION', $this->release).'</span>';
		echo '<br /><br />Olivier Buisard @ <a href="https://simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';

		if ($action === 'update') {

			// update warning

		    echo '<div class="alert alert-warning">' . Text::sprintf('PLG_LATESTNEWSENHANCED_NEWSFEEDS_WARNING_RELEASENOTES', $this->changelogLink) . '</div>';
		}

		// move layouts to LNEP extension

		$layoutfiles = array();
		$layoutfiles[] = 'lnep_detail_newsfeeds_feedtitle.php';
		$layoutfiles[] = 'lnep_detail_newsfeeds_entrycategories.php';
		$layoutfiles[] = 'lnep_detail_newsfeeds_entrysource.php';
		$layoutfiles[] = 'lnep_detail_newsfeeds_fields.php';

		foreach ($layoutfiles as $layoutfile) {
		    $src = JPATH_ROOT.'/plugins/latestnewsenhanced/newsfeeds/layouts/'.$layoutfile;
		    $dest = JPATH_ROOT.'/components/com_latestnewsenhancedpro/layouts/details/'.$layoutfile;

		    if (!File::copy($src, $dest)) {
		        Factory::getApplication()->enqueueMessage(Text::sprintf('PLG_LATESTNEWSENHANCED_NEWSFEEDS_WARNING_COULDNOTCOPYFILE', $layoutfile), 'warning');
		    }
		}

		// enable the plugin

		$this->enableExtension('plugin', 'newsfeeds', 'latestnewsenhanced');

		$this->removeUpdateSite('plugin', 'newsfeeds', 'latestnewsenhanced', 'http://www.barejoomlatemplates.com/autoupdates/latestnewsenhancedpro/newsfeeds/newsfeeds-update.xml');

		return true;
	}

	private function enableExtension($type, $element, $folder = '')
	{
		$db = Factory::getDBO();

		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('enabled').' = 1');
		$query->where($db->quoteName('type').' = '.$db->quote($type));
		$query->where($db->quoteName('element').' = '.$db->quote($element));
		$query->where($db->quoteName('folder').' = '.$db->quote($folder));

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return false;
		}

		return true;
	}
	
	private function removeUpdateSite($type, $element, $folder = '', $location = '')
	{
	    $db = Factory::getDBO();
	    
	    $query = $db->getQuery(true);
	    
	    $query->select('extension_id');
	    $query->from('#__extensions');
	    $query->where($db->quoteName('type').'='.$db->quote($type));
	    $query->where($db->quoteName('element').'='.$db->quote($element));
	    if ($folder) {
	        $query->where($db->quoteName('folder').'='.$db->quote($folder));
	    }
	    
	    $db->setQuery($query);
	    
	    $extension_id = '';
	    try {
	        $extension_id = $db->loadResult();
	    } catch (\RuntimeException $e) {
	        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	        return false;
	    }
	    
	    if ($extension_id) {
	        
	        $query->clear();
	        
	        $query->select('update_site_id');
	        $query->from('#__update_sites_extensions');
	        $query->where($db->quoteName('extension_id').'='.$db->quote($extension_id));
	        
	        $db->setQuery($query);
	        
	        $updatesite_id = array(); // can have several results
	        try {
	            $updatesite_id = $db->loadColumn();
	        } catch (\RuntimeException $e) {
	            Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	            return false;
	        }
	        
	        if (empty($updatesite_id)) {
	            return false;
	        } else if (count($updatesite_id) == 1) {
	            
	            $query->clear();
	            
	            $query->delete($db->quoteName('#__update_sites'));
	            $query->where($db->quoteName('update_site_id').' = '.$db->quote($updatesite_id[0]));
	            
	            $db->setQuery($query);
	            
	            try {
	                $db->execute();
	            } catch (\RuntimeException $e) {
	                Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	                return false;
	            }
	        } else { // several update sites exist for the same extension therefore we need to specify which to delete
	            
	            if ($location) {
	                $query->clear();
	                
	                $query->delete($db->quoteName('#__update_sites'));
	                $query->where($db->quoteName('update_site_id').' IN ('.implode(',', $updatesite_id).')');
	                $query->where($db->quoteName('location').' = '.$db->quote($location));
	                
	                $db->setQuery($query);
	                
	                try {
	                    $db->execute();
	                } catch (\RuntimeException $e) {
	                    Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	                    return false;
	                }
	            } else {
	                return false;
	            }
	        }
	    } else {
	        return false;
	    }
	    
	    return true;
	}

}
