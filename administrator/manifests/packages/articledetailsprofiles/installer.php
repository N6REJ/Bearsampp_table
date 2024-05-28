<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;

/**
 * Script file of the Article Details Profiles component
 */
class Pkg_ArticleDetailsProfilesInstallerScript extends InstallerScript
{
	/*
	 * Minimum extensions library version required
	 */
	protected $minimumLibrary = '2.4.0';

	/**
	 * Available languages
	 */
	protected $availableLanguages = array('de-DE', 'en-GB', 'es-ES', 'fa-IR', 'fi-FI', 'fr-FR', 'it-IT', 'nl-NL', 'pt-BR', 'ru-RU', 'sl-SI', 'tr-TR');

	/**
	 * Extensions library link for download
	 */
	protected $libraryDownloadLink = 'https://simplifyyourweb.com/downloads/syw-extension-library';

	/**
	 * Link to the change logs
	 */
	protected $changelogLink = 'https://simplifyyourweb.com/documentation/article-details/installation/updating-older-versions';

	/**
	 * Link to the translation page
	 */
	protected $translationLink = 'https://simplifyyourweb.com/translators';

	/**
	 * Link to the quick start page
	 */
	protected $quickstartLink = 'https://simplifyyourweb.com/documentation/tutorials/831-setting-up-article-details-profiles';

	/**
	 * Extension script constructor
	 */
	public function __construct($installer)
	{
	    $this->extension = 'pkg_articledetailsprofiles';
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

	    // make sure the library is installed and that it is compatible with the extension
	    return $this->installOrUpdateLibrary($installer);
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
	    echo HTMLHelper::image('com_articledetailsprofiles/logo.png', 'Article Details Profiles', null, true);
	    echo '<br /><br /><span class="badge bg-dark">'.Text::sprintf('PKG_ARTICLEDETAILSPROFILES_VERSION', $this->release).'</span>';
	    echo '<br /><br />Olivier Buisard @ <a href="https://simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
	    echo '</p>';

        // language test

	    $current_language = Factory::getLanguage()->getTag();
	    if (!in_array($current_language, $this->availableLanguages)) {
	        Factory::getApplication()->enqueueMessage('The ' . Factory::getLanguage()->getName() . ' language is missing for this component.<br /><a href="' . $this->translationLink . '" target="_blank">Please consider contributing to its translation</a> and get a license upgrade for your help!', 'info');
	    }

	    // enable the installer plugin

	    $this->enableExtension('plugin', 'adpinstaller', 'installer');

	    if ($action === 'install') {

	        // link to Quickstart

	        echo '<p><a class="btn btn-primary" href="' . $this->quickstartLink . '" target="_blank"><i class="fa fa-stopwatch"></i> ' . Text::_('PKG_ARTICLEDETAILSPROFILES_BUTTON_QUICKSTART') . '</a></p>';

	        // enable the quickicon plugin

	        $this->enableExtension('plugin', 'articledetailsprofiles', 'quickicon');

	        // on install (only) retrieve values from Article Details, if enabled

	        if (PluginHelper::isEnabled('content', 'articledetails')) {
	            $this->migrateFreeExtension();
	        }
	    }

     	if ($action === 'update') {

			// update warning

     	    echo '<p><a class="btn btn-primary" href="' . $this->changelogLink . '" target="_blank">' . Text::_('PKG_ARTICLEDETAILSPROFILES_BUTTON_UPDATENOTES') . '</a></p>';

		    // overrides warning

// 		    $defaultemplate = $this->getDefaultTemplate();

// 		    if ($defaultemplate) {
// 		        $overrides_path = JPATH_ROOT.'/templates/'.$defaultemplate.'/html/';
// 		        if (Folder::exists($overrides_path.'layouts/com_articledetailsprofiles') && Folder::exists($overrides_path.'layouts/com_articledetailsprofiles/details') && count(Folder::files($overrides_path.'layouts/com_articledetailsprofiles/details', 'adp[a-z_]*.php')) > 0) {
// 		            Factory::getApplication()->enqueueMessage(Text::_('PKG_ARTICLEDETAILSPROFILES_WARNING_LAYOUTOVERRIDES'), 'warning');
// 		        }
// 		    }

		    // remove old cached headers which may interfere with fixes, updates or new additions

		    if (function_exists('glob')) {

		        $filenames = glob(JPATH_SITE.'/media/cache/plg_content_articledetailsprofiles/style_*.css');
		        if ($filenames != false) {
		            $this->deleteFiles = array_merge($this->deleteFiles, $filenames);
		        }

		        $filenames = glob(JPATH_SITE.'/media/cache/plg_content_articledetailsprofiles/print_*.css');
		        if ($filenames != false) {
		            $this->deleteFiles = array_merge($this->deleteFiles, $filenames);
		        }
		    }

		    // move position parameter to config

		    $this->movePositionParameter();

		    // +++ Migration Joomla 3 to Joomla 4

		    // remove data from /cache

		    $this->deleteFolders[] = '/cache/plg_content_articledetailsprofiles';

		    // +++ End Migration
        }

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
	            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_ARTICLEDETAILSPROFILES_ERROR_CANNOTMOVEFILE', $file), 'warning');
	        }
	    }
	}

	private function removePackage($element)
	{
	    $db = Factory::getDBO();

	    $query = $db->getQuery(true);

	    $query->delete('#__extensions');
	    $query->where($db->quoteName('type') . '=' . $db->quote('package'));
	    $query->where($db->quoteName('element') . '=' . $db->quote($element));

	    $db->setQuery($query);

	    try {
	        $db->execute();
	    } catch (ExecutionFailureException $e) {
	        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	        return false;
	    }

	    return true;
	}

	private function enableExtension($type, $element, $folder = '', $enable = true)
	{
	    $db = Factory::getDBO();

	    $query = $db->getQuery(true);

	    $query->update($db->quoteName('#__extensions'));
	    if ($enable) {
	        $query->set($db->quoteName('enabled').' = 1');
	    } else {
	        $query->set($db->quoteName('enabled').' = 0');
	    }
	    $query->where($db->quoteName('type').' = '.$db->quote($type));
	    $query->where($db->quoteName('element').' = '.$db->quote($element));
	    if ($folder) {
	        $query->where($db->quoteName('folder').' = '.$db->quote($folder));
	    }

	    $db->setQuery($query);

	    try {
	        $db->execute();
	    } catch (ExecutionFailureException $e) {
	        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	        return false;
	    }

	    return true;
	}

	private function getDefaultTemplate()
	{
	    $db = Factory::getDBO();

	    $query = $db->getQuery(true);

	    $query->select('template');
	    $query->from('#__template_styles');
	    $query->where($db->quoteName('client_id').'= 0');
	    $query->where($db->quoteName('home').'= 1');

	    $db->setQuery($query);

	    $defaultemplate = '';

	    try {
	        $defaultemplate = $db->loadResult();
	    } catch (ExecutionFailureException $e) {
	        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	    }

	    return $defaultemplate;
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
		} catch (ExecutionFailureException $e) {
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
			} catch (ExecutionFailureException $e) {
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
				} catch (ExecutionFailureException $e) {
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
			        } catch (ExecutionFailureException $e) {
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

	private function installOrUpdatePackage($installer, $package_name, $installation_type = 'install')
	{
	    // Get the path to the package

	    $sourcePath = $installer->getParent()->getPath('source');
	    $sourcePackage = $sourcePath . '/packages/'.$package_name.'.zip';

	    // Extract and install the package

	    $package = InstallerHelper::unpack($sourcePackage);
	    if ($package === false || (is_array($package) && $package['type'] === false)) {
	        return false;
	    }

	    $tmpInstaller = new Installer();

	    if ($installation_type === 'install') {
	        return $tmpInstaller->install($package['dir']);
	    } else {
	        return $tmpInstaller->update($package['dir']);
	    }
	}

	/**
	 * Install the library and its plugin if missing or outdated
	 */
	private function installOrUpdateLibrary($installer)
	{
	    if (!Folder::exists(JPATH_ROOT . '/libraries/syw') || !Folder::exists(JPATH_ROOT . '/plugins/system/syw')) {

	        if (!$this->installOrUpdatePackage($installer, 'pkg_sywlibrary')) {
	            Factory::getApplication()->enqueueMessage(Text::_('SYWLIBRARY_INSTALLFAILED').'<br /><a href="'.$this->libraryDownloadLink.'" target="_blank">'.Text::_('SYWLIBRARY_DOWNLOAD').'</a>', 'error');
	            return false;
	        }

	        Factory::getApplication()->enqueueMessage(Text::sprintf('SYWLIBRARY_INSTALLED', $this->minimumLibrary), 'message');
	    } else {

	        $library_version = strval(simplexml_load_file(JPATH_ADMINISTRATOR . '/manifests/libraries/syw.xml')->version);
	        if (!version_compare($library_version, $this->minimumLibrary, 'ge')) {

	            if (!$this->installOrUpdatePackage($installer, 'pkg_sywlibrary', 'update')) {
	                Factory::getApplication()->enqueueMessage(Text::_('SYWLIBRARY_UPDATEFAILED').'<br />'.Text::_('SYWLIBRARY_UPDATE'), 'error');
	                return false;
	            }

	            Factory::getApplication()->enqueueMessage(Text::sprintf('SYWLIBRARY_UPDATED', $this->minimumLibrary), 'message');
	        }
	    }

	    return true;
	}

	private function migrateFreeExtension()
	{
	    $ad_plugin = PluginHelper::getPlugin('content', 'articledetails');
	    $ad_plugin_params = new Registry($ad_plugin->params);

	    // enable the pro plugin

	    $this->enableExtension('plugin', 'articledetailsprofiles', 'content');

	    // set plugin parameters

	    $adp_plugin_params = new Registry();

	    $this->transferParamValues($adp_plugin_params, $ad_plugin_params);

	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);

	    $query->update('#__extensions');
	    $query->set($db->quoteName('params').'='.$db->quote($adp_plugin_params->toString()));
	    $query->where($db->quoteName('type').'='.$db->quote('plugin'));
	    $query->where($db->quoteName('folder').'='.$db->quote('content'));
	    $query->where($db->quoteName('element').'='.$db->quote('articledetailsprofiles'));

	    $db->setQuery($query);

	    try {
	        $db->execute();
	        Factory::getApplication()->enqueueMessage(Text::_('PKG_ARTICLEDETAILSPROFILES_MESSAGE_LOADINGPARAMSINTOPLUGINSUCCESSFUL'), 'message');
	    } catch (ExecutionFailureException $e) {
	        Factory::getApplication()->enqueueMessage('1 '.Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	    }

	    // set module parameters

	    $create_module_instance = true;

	    $categories_array = $ad_plugin_params->get('catid', array('none'));

	    if (!is_array($categories_array)) { // before the plugin is saved, the value is the string 'all'
	        $categories_array = explode(' ', $categories_array);
	    }

	    $array_of_category_values = array_count_values($categories_array);
	    if (isset($array_of_category_values['none']) && $array_of_category_values['none'] > 0) { // 'none' was selected
	        $create_module_instance = false;
	    }

	    $adp_module = ModuleHelper::getModule('mod_articledetailsprofile'); // there is only one instance on first install

	    if ($create_module_instance && !is_null($adp_module)) {

	        $adp_module_params = new Registry($adp_module->params);

	        $this->transferParamValues($adp_module_params, $ad_plugin_params);

	        // update the database

	        if ($adp_module->id == 0) {

	            $columns = array('title', 'note', 'position', 'published', 'module', 'params', 'access', 'language');
	            $values = array($db->quote('Article Details Profile'), $db->quote(Text::_('PKG_ARTICLEDETAILSPROFILES_MESSAGE_IMPORTFROMFREEVERSION')), $db->quote('adprofile'), 1, $db->quote('mod_articledetailsprofile'), $db->quote($adp_module_params->toString()), 1, $db->quote('*'));

	            $query->clear();

	            $query->insert($db->quoteName('#__modules'));
	            $query->columns($db->quoteName($columns));
	            $query->values(implode(',', $values));

	            $db->setQuery($query);

	            try {
	                $db->execute();
	                Factory::getApplication()->enqueueMessage(Text::_('PKG_ARTICLEDETAILSPROFILES_MESSAGE_LOADINGPARAMSINTOMODULESUCCESSFUL'), 'message');
	            } catch (ExecutionFailureException $e) {
	                Factory::getApplication()->enqueueMessage('2 '.Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	            }

	        } else {
	            $query->clear();

	            $query->update($db->quoteName('#__modules'));
	            $query->set($db->quoteName('title').'='.$db->quote('Article Details Profile'));
	            $query->set($db->quoteName('params').'='.$db->quote($adp_module_params->toString()));
	            $query->set($db->quoteName('published').'=1');
	            $query->set($db->quoteName('note').'='.$db->quote(Text::_('PKG_ARTICLEDETAILSPROFILES_MESSAGE_IMPORTFROMFREEVERSION')));
	            $query->set($db->quoteName('position').'='.$db->quote('adprofile'));
	            $query->where($db->quoteName('id').'='.$adp_module->id);

	            $db->setQuery($query);

	            try {
	                $db->execute();
	                Factory::getApplication()->enqueueMessage(Text::_('PKG_ARTICLEDETAILSPROFILES_MESSAGE_LOADINGPARAMSINTOMODULESUCCESSFUL'), 'message');
	            } catch (ExecutionFailureException $e) {
	                Factory::getApplication()->enqueueMessage('3 '.Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	            }
	        }
	    }
	}

	private function movePositionParameter()
	{
		$db = Factory::getDBO();

		$query = $db->getQuery(true);

		$query->select('params');
		$query->from('#__extensions');
		$query->where($db->quoteName('type').'='.$db->quote('plugin'));
		$query->where($db->quoteName('folder').'='.$db->quote('content'));
		$query->where($db->quoteName('element').'='.$db->quote('articledetailsprofiles'));

		$db->setQuery($query);

		$plugin_params = array();
		try {
			$plugin_params = json_decode($db->loadResult(), true);
		} catch (ExecutionFailureException $e) {
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
		}

		if (isset($plugin_params['position'])) {

			// get config params

			$query->clear();

			$query->select($db->quoteName('params'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('type').'='.$db->quote('component'));
			$query->where($db->quoteName('element').'='.$db->quote('com_articledetailsprofiles'));

			$db->setQuery($query);

			$config = '';
			try {
				$config = $db->loadResult();
			} catch (ExecutionFailureException $e) {
				Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			}

			if ($config) {

				$config_params = json_decode($config, true);

				$config_params['position'] = $plugin_params['position'];
				unset($plugin_params['position']);

				$query->clear();

				$query->update('#__extensions');
				$query->set($db->quoteName('params').'='.$db->quote(json_encode($config_params)));
				$query->where($db->quoteName('type').'='.$db->quote('component'));
				$query->where($db->quoteName('element').'='.$db->quote('com_articledetailsprofiles'));

				$db->setQuery($query);

				try {
					$db->execute();
				} catch (ExecutionFailureException $e) {
					Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				}

				$query->clear();

				$query->update('#__extensions');
				$query->set($db->quoteName('params').'='.$db->quote(json_encode($plugin_params)));
				$query->where($db->quoteName('type').'='.$db->quote('plugin'));
				$query->where($db->quoteName('folder').'='.$db->quote('content'));
				$query->where($db->quoteName('element').'='.$db->quote('articledetailsprofiles'));

				$db->setQuery($query);

				try {
					$db->execute();
				} catch (ExecutionFailureException $e) {
					Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				}
			}
		}
	}

	private function transferParamValues(&$out_params, $in_params)
	{
	    // tab: layout

	    $out_params->set('disable_in_list_views', $in_params->get('disable_in_list_views', '0'));
	    $out_params->set('lists_head_type', $in_params->get('lists_head_type', 'none'));
	    $out_params->set('lists_head_w', $in_params->get('lists_head_w', '64'));
	    $out_params->set('lists_align_details', $in_params->get('lists_align_details', 'left'));

	    $out_params->set('head_type', $in_params->get('head_type', 'none'));
	    $out_params->set('head_w', $in_params->get('head_w', '64'));
	    $out_params->set('align_details', $in_params->get('align_details', 'left'));

	    $out_params->set('footer_head_type', $in_params->get('footer_head_type', 'none'));
	    $out_params->set('footer_align_details', $in_params->get('footer_align_details', 'left'));

	    // tab: calendar header

	    $out_params->set('post_d', $in_params->get('post_d', 'published'));
	    $out_params->set('pos_1', $in_params->get('pos_1', 'w'));
	    $out_params->set('pos_2', $in_params->get('pos_2', 'd'));
	    $out_params->set('pos_3', $in_params->get('pos_3', 'm'));
	    $out_params->set('pos_4', $in_params->get('pos_4', 'y'));
	    $out_params->set('pos_5', $in_params->get('pos_5', 't'));
	    $out_params->set('fmt_w', $in_params->get('fmt_w', 'D'));
	    $out_params->set('fmt_m', $in_params->get('fmt_m', 'M'));
	    $out_params->set('fmt_d', $in_params->get('fmt_d', 'd'));
	    $out_params->set('cal_style', $in_params->get('cal_style', 'original'));
	    $out_params->set('border_w', $in_params->get('border_w', '0'));
	    $out_params->set('border_r', $in_params->get('border_r', '0'));
	    $out_params->set('border_c', $in_params->get('border_c', '#000000'));
	    $out_params->set('sh_w', $in_params->get('sh_w', '0'));
	    $out_params->set('fontcalendar', $in_params->get('fontcalendar', ''));
	    $out_params->set('f_r', $in_params->get('f_r', '14'));
	    $out_params->set('c2', $in_params->get('c2', '#494949'));
	    $out_params->set('bgc21', $in_params->get('bgc21', ''));
	    $out_params->set('bgc22', $in_params->get('bgc22', ''));
	    $out_params->set('c1', $in_params->get('c1', '#3D3D3D'));
	    $out_params->set('bgc11', $in_params->get('bgc11', ''));
	    $out_params->set('bgc12', $in_params->get('bgc12', ''));
	    $out_params->set('c3', $in_params->get('c3', '#494949'));
	    $out_params->set('bgc31', $in_params->get('bgc31', ''));
	    $out_params->set('bgc32', $in_params->get('bgc32', ''));

	    // tab: header

	    $out_params->set('show_pub_status', $in_params->get('show_pub_status', '1'));
	    $out_params->set('t_tag', $in_params->get('t_tag', '2'));

	    if ($in_params->get('before_title_information_blocks') !== null) {
	        $out_params->set('before_title_information_blocks', $in_params->get('before_title_information_blocks', ''));
	        $out_params->set('after_title_information_blocks', $in_params->get('after_title_information_blocks', ''));
	        $out_params->set('footer_information_blocks', $in_params->get('footer_information_blocks', ''));
	    } else {

	        $j = 0;
	        $j_sub = 0;

	        $info_blocs = array();

	        while ($j < 3) {
	            if ($in_params->get('info_b'.($j + 1)) !== null && $in_params->get('info_b'.($j + 1)) != 'none') {
	                $info_bloc = array();
	                $info_bloc['show_icons'] = $in_params->get('show_icons_b'.($j + 1)) !== null ? $in_params->get('show_icons_b'.($j + 1)) : 0;
	                $info_bloc['icon'] = '';
	                $info_bloc['prepend'] = $in_params->get('prepend_b'.($j + 1)) !== null ? $in_params->get('prepend_b'.($j + 1)) : '';
	                $info_bloc['info'] = $in_params->get('info_b'.($j + 1));
	                $info_bloc['extra_classes'] = $in_params->get('extra_classes_b'.($j + 1)) !== null ? $in_params->get('extra_classes_b'.($j + 1)) : '';
	                $info_bloc['new_line'] = $in_params->get('new_line_b'.($j + 1)) !== null ? $in_params->get('new_line_b'.($j + 1)) : 0;
	                $info_bloc['showing_in'] = $in_params->get('showing_in_b'.($j + 1)) !== null ? $in_params->get('showing_in_b'.($j + 1)) : '';
	                $info_bloc['access'] = 1;

	                $info_blocs['information_blocks'.$j_sub] = $info_bloc;
	                $j_sub++;
	            }
	            $j++;
	        }

	        if (!empty($info_blocs)) {
	            $out_params->set('before_title_information_blocks', $info_blocs);
	        }

	        $j = 0;
	        $j_sub = 0;

	        $info_blocs = array();

	        while ($j < 9) {
	            if ($in_params->get('info_'.($j + 1)) !== null && $in_params->get('info_'.($j + 1)) != 'none') {
	                $info_bloc = array();
	                $info_bloc['show_icons'] = $in_params->get('show_icons_'.($j + 1)) !== null ? $in_params->get('show_icons_'.($j + 1)) : 0;
	                $info_bloc['icon'] = '';
	                $info_bloc['prepend'] = $in_params->get('prepend_'.($j + 1)) !== null ? $in_params->get('prepend_'.($j + 1)) : '';
	                $info_bloc['info'] = $in_params->get('info_'.($j + 1));
	                $info_bloc['extra_classes'] = $in_params->get('extra_classes_'.($j + 1)) !== null ? $in_params->get('extra_classes_'.($j + 1)) : '';
	                $info_bloc['new_line'] = $in_params->get('new_line_'.($j + 1)) !== null ? $in_params->get('new_line_'.($j + 1)) : 0;
	                $info_bloc['showing_in'] = $in_params->get('showing_in_'.($j + 1)) !== null ? $in_params->get('showing_in_'.($j + 1)) : '';
	                $info_bloc['access'] = 1;

	                $info_blocs['information_blocks'.$j_sub] = $info_bloc;
	                $j_sub++;
	            }
	            $j++;
	        }

	        if (!empty($info_blocs)) {
	            $out_params->set('after_title_information_blocks', $info_blocs);
	        }

	        // tab: footer

	        $j = 0;
	        $j_sub = 0;

	        $info_blocs = array();

	        while ($j < 3) {
	            if ($in_params->get('info_foot'.($j + 1)) !== null && $in_params->get('info_foot'.($j + 1)) != 'none') {
	                $info_bloc = array();
	                $info_bloc['show_icons'] = $in_params->get('show_icons_foot'.($j + 1)) !== null ? $in_params->get('show_icons_foot'.($j + 1)) : 0;
	                $info_bloc['icon'] = '';
	                $info_bloc['prepend'] = $in_params->get('prepend_foot'.($j + 1)) !== null ? $in_params->get('prepend_foot'.($j + 1)) : '';
	                $info_bloc['info'] = $in_params->get('info_foot'.($j + 1));
	                $info_bloc['extra_classes'] = $in_params->get('extra_classes_foot'.($j + 1)) !== null ? $in_params->get('extra_classes_foot'.($j + 1)) : '';
	                $info_bloc['new_line'] = $in_params->get('new_line_foot'.($j + 1)) !== null ? $in_params->get('new_line_foot'.($j + 1)) : 0;
	                $info_bloc['showing_in'] = 2;
	                $info_bloc['access'] = 1;

	                $info_blocs['information_blocks'.$j_sub] = $info_bloc;
	                $j_sub++;
	            }
	            $j++;
	        }

	        if (!empty($info_blocs)) {
	            $out_params->set('footer_information_blocks', $info_blocs);
	        }
	    }

	    // tab: details

	    $out_params->set('d_fs', $in_params->get('d_fs', '80'));
	    $out_params->set('fontdetails', $in_params->get('fontdetails', ''));
	    $out_params->set('separator', $in_params->get('separator', ''));
	    $out_params->set('details_line_spacing', $in_params->get('details_line_spacing', array('', 'px')));
	    $out_params->set('details_color', $in_params->get('details_color', '#000000'));
	    $out_params->set('iconscolor', $in_params->get('iconscolor', '#000000'));
	    $out_params->set('show_d', $in_params->get('show_d', 'date'));
	    $out_params->set('d_format', $in_params->get('d_format', 'd F Y'));
	    $out_params->set('t_format', $in_params->get('t_format', 'H:i'));
	    $out_params->set('show_rating', $in_params->get('show_rating', 'text'));
	    $out_params->set('star_color', $in_params->get('star_color', '#000000'));
	    $out_params->set('distinct_keywords', $in_params->get('distinct_keywords', '0'));
	    $out_params->set('show_icon_keywords', $in_params->get('show_icon_keywords', '0'));
	    $out_params->set('prepend_keywords', $in_params->get('prepend_keywords', ''));
	    $out_params->set('order_tags', $in_params->get('order_tags', 'none'));
	    $out_params->set('hide_tags', $in_params->get('hide_tags', ''));
	    $out_params->set('distinct_tags', $in_params->get('distinct_tags', '0'));
	    $out_params->set('show_icon_tags', $in_params->get('show_icon_tags', '0'));
	    $out_params->set('prepend_tags', $in_params->get('prepend_tags', ''));
	    $out_params->set('bootstrap_tags', $in_params->get('bootstrap_tags', '0'));
	    $out_params->set('show_icon_links', $in_params->get('show_icon_links', '0'));
	    $out_params->set('prepend_links', $in_params->get('prepend_links', ''));
	    $out_params->set('protocol', $in_params->get('protocol', '1'));
	    $out_params->set('share_color', $in_params->get('share_color', 'none'));
	    $out_params->set('share_r', $in_params->get('share_r', '0'));
	    $out_params->set('share_classes', $in_params->get('share_classes', ''));
	    $out_params->set('share_pos_1', $in_params->get('share_pos_1', 'none'));
	    $out_params->set('share_pos_2', $in_params->get('share_pos_2', 'none'));
	    $out_params->set('share_pos_3', $in_params->get('share_pos_3', 'none'));
	    $out_params->set('share_pos_4', $in_params->get('share_pos_4', 'none'));
	    $out_params->set('share_pos_5', $in_params->get('share_pos_5', 'none'));
	    $out_params->set('share_pos_6', $in_params->get('share_pos_6', 'none'));

	    // tab: advanced

	    $out_params->set('site_mode', $in_params->get('site_mode', 'dev'));
	    $out_params->set('show_errors', $in_params->get('show_errors', '0'));
	    $out_params->set('load_icon_font', $in_params->get('load_icon_font', '1'));
	    $out_params->set('style_overrides', $in_params->get('style_overrides', ''));
	    $out_params->set('print_overrides', $in_params->get('print_overrides', ''));
	    $out_params->set('autohide_title', $in_params->get('autohide_title', '0'));
	    $out_params->set('title_element', $in_params->get('title_element', ''));
	    $out_params->set('info_element', $in_params->get('info_element', '.article-info'));
	    $out_params->set('links_element', $in_params->get('links_element', ''));
	    $out_params->set('images_element', $in_params->get('images_element', ''));
	    $out_params->set('autohide_tags', $in_params->get('autohide_tags', '0'));
	    $out_params->set('tags_element', $in_params->get('tags_element', ''));
	    $out_params->set('icons_element', $in_params->get('icons_element', ''));
	    $out_params->set('fields_element', $in_params->get('fields_element', ''));
	    $out_params->set('autohide_vote', $in_params->get('autohide_vote', '0'));
	    $out_params->set('clear_header_files_cache', $in_params->get('clear_header_files_cache', '1'));
	}

}
