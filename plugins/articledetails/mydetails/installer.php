<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Script file for the Article Details MyDetails plugin
 */
class plgArticleDetailsMyDetailsInstallerScript extends InstallerScript
{
	/**
	 * Minimum ADP version required to install the extension
	 */
	protected $minimumADP = '6.0.0';

	/**
	 * ADP extension link for download
	 */
	protected $ADPDownloadLink = 'https://simplifyyourweb.com/downloads/article-details-profiles';

	/**
	 * Extension script constructor
	 */
	public function __construct($installer)
	{
	    $this->extension = 'plg_articledetails_mydetails';
	    $this->minimumJoomla = '4.0.0';
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

		// check if Article Details Profiles is installed and compatible

		if (!Folder::exists(JPATH_ROOT.'/modules/mod_articledetailsprofile')) {

			$message = Text::_('PLG_ARTICLEDETAILS_MYDETAILS_MISSING_EXTENSION').'.<br /><a href="'.$this->ADPDownloadLink.'" target="_blank">'.Text::_('PLG_ARTICLEDETAILS_MYDETAILS_DOWNLOAD_EXTENSION').'</a>.';
			Factory::getApplication()->enqueueMessage($message, 'error');
			return false;

		} else {
			$module_version = strval(simplexml_load_file(JPATH_SITE . '/modules/mod_articledetailsprofile/mod_articledetailsprofile.xml')->version);

			if (version_compare($module_version, $this->minimumADP, 'ge')) {
				Factory::getApplication()->enqueueMessage(Text::_('PLG_ARTICLEDETAILS_MYDETAILS_COMPATIBLE_EXTENSION'), 'message');
				return true;
			} else {
				$message = Text::_('PLG_ARTICLEDETAILS_MYDETAILS_NONCOMPATIBLE_EXTENSION').'.<br /><a href="'.$this->ADPDownloadLink.'" target="_blank">'.Text::_('PLG_ARTICLEDETAILS_MYDETAILS_UPDATE_EXTENSION').'</a>.';
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

		echo '<p style="margin: 10px 0 20px 0">';
		echo '<span class="badge bg-dark">' . Text::sprintf('PLG_ARTICLEDETAILS_MYDETAILS_VERSION', $this->release) . '</span>';
		echo '<br /><br />Olivier Buisard @ <a href="https://simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';

		if ($action === 'update') {

			// update warning

			echo '<div class="alert alert-warning">' . Text::_('PLG_ARTICLEDETAILS_MYDETAILS_WARNING_RELEASENOTES') . '</div>';
		}

		// move layouts to ADP extension

		$layoutfiles = array();
		$layoutfiles[] = 'adp_detail_mydetails_url.php';

		foreach ($layoutfiles as $layoutfile) {
		    $src = JPATH_ROOT.'/plugins/articledetails/mydetails/layouts/'.$layoutfile;
		    $dest = JPATH_ROOT.'/components/com_articledetailsprofiles/layouts/details/'.$layoutfile;

		    if (!File::copy($src, $dest)) {
		        Factory::getApplication()->enqueueMessage(Text::sprintf('PLG_ARTICLEDETAILS_MYDETAILS_WARNING_COULDNOTCOPYFILE', $layoutfile), 'warning');
		    }
		}

		// enable the plugin

		$this->enableExtension('plugin', 'mydetails', 'articledetails');

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
		} catch (ExecutionFailureException $e) {
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return false;
		}

		return true;
	}
}
?>