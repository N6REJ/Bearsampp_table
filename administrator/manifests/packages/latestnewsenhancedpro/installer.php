<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Script file of the Latest News Enhanced Pro component
 */
class Pkg_LatestNewsEnhancedProInstallerScript extends InstallerScript
{

    /*
     * Minimum extensions library version required
     */
    protected $minimumLibrary = '2.6.2';

    /**
     * Available languages
     */
    protected $availableLanguages = array('da-DK', 'de-DE', 'en-GB', 'es-ES', 'fi-FI', 'fr-FR', 'hu-HU', 'it-IT', 'ja-JP', 'nl-NL', 'pl-PL', 'pt-BR', 'pt-PT', 'ru-RU', 'sl-SI', 'sv-SE', 'tr-TR');

    /**
     * Extensions library link for download
     */
    protected $libraryDownloadLink = 'https://simplifyyourweb.com/downloads/syw-extension-library';

    /**
     * Link to the change logs
     */
    protected $changelogLink = 'https://simplifyyourweb.com/documentation/latest-news/installation/updating-older-versions';

    /**
     * Link to the translation page
     */
    protected $translationLink = 'https://simplifyyourweb.com/translators';

    /**
     * Link to the quick start page
     */
    protected $quickstartLink = 'https://simplifyyourweb.com/documentation/latest-news/quickstart-guide';

    /*
     * Show a message when Newsfeeds needs an update
     */
    protected $updateNewsfeed = true;

    /**
     * Extension script constructor
     */
    public function __construct($parent)
    {
        $this->extension = 'pkg_latestnewsenhancedpro';
        $this->minimumJoomla = '4.1.0';
        // $this->minimumPhp = JOOMLA_MINIMUM_PHP; // not needed
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
    public function install($installer)
    {
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($installer)
    {
    }

    /**
     * method to update the component
     *
     * @return boolean True on success
     */
    public function update($installer)
    {
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

        echo '<p style="margin: 10px 0 20px 0">';
        echo HTMLHelper::image('com_latestnewsenhancedpro/logo.png', 'Latest News Enhanced Pro', null, true);
        echo '<br /><br /><span class="badge bg-dark">' . Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_VERSION', $this->release) . '</span>';
        echo '<br /><br />Olivier Buisard @ <a href="https://simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
        echo '</p>';

        // language test

        $current_language = Factory::getLanguage()->getTag();
        if (!in_array($current_language, $this->availableLanguages)) {
            Factory::getApplication()->enqueueMessage('The ' . Factory::getLanguage()->getName() . ' language is missing for this component.<br /><a href="' . $this->translationLink .
                '" target="_blank">Please consider contributing to its translation</a> and get a license upgrade for your help!', 'info');
        }

        // enable the installer plugin

        $this->enableExtension('plugin', 'lnepinstaller', 'installer');

        // enable the datasources plugin

        $this->enableExtension('plugin', 'lnedatasources', 'content');

        // remove the package from the free version
        // can only be done through the database manually because automatic uninstall would also remove the module

        $this->removePackage('pkg_latestnewsenhanced');

        // remove the package update site of the free module

        $this->removeUpdateSite('package', 'pkg_latestnewsenhanced', '', 'https://updates.simplifyyourweb.com/free/latestnewsenhanced/latestnewsenhanced-pkg-v4-update.xml');

        if ($action === 'install') {

            // link to Quickstart

            echo '<p><a class="btn btn-primary" href="' . $this->quickstartLink . '" target="_blank"><i class="fa fa-stopwatch"></i> ' . Text::_('PKG_LATESTNEWSENHANCEDPRO_BUTTON_QUICKSTART') .
                '</a></p>';

            // enable the quickicon plugin

            $this->enableExtension('plugin', 'latestnewsenhancedpro', 'quickicon');

            // move calendar images

            $media_params = ComponentHelper::getParams('com_media');
            $images_path = $media_params->get('image_path', 'images');
            $calendars_path = JPATH_ROOT . '/' . $images_path . '/calendars';

            if (!Folder::exists($calendars_path)) {
                if (Folder::create($calendars_path)) {
                    if (!Folder::exists($calendars_path . '/rainbow')) {
                        if (Folder::create($calendars_path . '/rainbow')) {
                            $src = JPATH_ROOT . '/media/mod_latestnewsenhanced/images/calendars/rainbow';
                            $dest = $calendars_path . '/rainbow';
                            if (!Folder::copy($src, $dest, '', true)) {
                                Factory::getApplication()->enqueueMessage(Text::_('PKG_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTCOPYFILES'), 'warning');
                            }
                        } else {
                            Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTCREATEFOLDER', $calendars_path . '/rainbow'), 'warning');
                        }
                    }
                } else {
                    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_WARNING_COULDNOTCREATEFOLDER', $calendars_path), 'warning');
                }
            }
        }

        if ($action === 'update') {

            // update warning

            echo '<p><a class="btn btn-primary text-light" href="' . $this->changelogLink . '" target="_blank">' . Text::_('PKG_LATESTNEWSENHANCEDPRO_BUTTON_UPDATENOTES') . '</a></p>';

            // newsfeed needs to be updated (if not done already) warning

            if ($this->updateNewsfeed && PluginHelper::isEnabled('latestnewsenhanced', 'newsfeeds')) {
                Factory::getApplication()->enqueueMessage(Text::_('PKG_LATESTNEWSENHANCEDPRO_WARNING_UPDATENEWSFEEDS'), 'warning');
            }

            // remove old cached headers which may interfere with fixes, updates or new additions

            if (function_exists('glob')) {

                // module
                $filenames = glob(JPATH_SITE . '/media/cache/mod_latestnewsenhanced/style_*.{css,js}', GLOB_BRACE);
                if ($filenames != false) {
                    $this->deleteFiles = array_merge($this->deleteFiles, $filenames);
                }

                // module
                $filenames = glob(JPATH_SITE . '/media/cache/mod_latestnewsenhanced/animation_*.js');
                if ($filenames != false) {
                    $this->deleteFiles = array_merge($this->deleteFiles, $filenames);
                }

                // views
                $filenames = glob(JPATH_SITE . '/media/cache/com_latestnewsenhanced/style_*.{css,js}', GLOB_BRACE);
                if ($filenames != false) {
                    $this->deleteFiles = array_merge($this->deleteFiles, $filenames);
                }
            }

            // +++ Migration Joomla 3 to Joomla 4

            // move module instances from mod_latestnewsenhancedextended to mod_latestnewsenhanced in the move from Joomla 3 to Joomla 4
            // + move additional data from datasources (RSEvents and Newsfeeds)

            $this->migrateOldProModule();

            // remove latestnewsenhancedextended quickicon

            if (Folder::exists(JPATH_ROOT . '/plugins/quickicon/latestnewsenhancedextended')) {
                if ($this->uninstallExtension('plugin', 'latestnewsenhancedextended', 'quickicon')) {
                    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UNINSTALLED', 'plugins/quickicon/latestnewsenhancedextended'), 'message');
                } else {
                    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_ERRORUNINSTALLING', 'plugins/quickicon/latestnewsenhancedextended'), 'error');
                }
            }

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
                Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_ERROR_CANNOTMOVEFILE', $file), 'warning');
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
            $query->set($db->quoteName('enabled') . ' = 1');
        } else {
            $query->set($db->quoteName('enabled') . ' = 0');
        }
        $query->where($db->quoteName('type') . ' = ' . $db->quote($type));
        $query->where($db->quoteName('element') . ' = ' . $db->quote($element));
        if ($folder) {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
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
        $query->where($db->quoteName('client_id') . '= 0');
        $query->where($db->quoteName('home') . '= 1');

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
        $query->where($db->quoteName('type') . '=' . $db->quote($type));
        $query->where($db->quoteName('element') . '=' . $db->quote($element));
        if ($folder) {
            $query->where($db->quoteName('folder') . '=' . $db->quote($folder));
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
            $query->where($db->quoteName('extension_id') . '=' . $db->quote($extension_id));

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
                $query->where($db->quoteName('update_site_id') . ' = ' . $db->quote($updatesite_id[0]));

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
                    $query->where($db->quoteName('update_site_id') . ' IN (' . implode(',', $updatesite_id) . ')');
                    $query->where($db->quoteName('location') . ' = ' . $db->quote($location));

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
        $sourcePackage = $sourcePath . '/packages/' . $package_name . '.zip';

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
                Factory::getApplication()->enqueueMessage(Text::_('SYWLIBRARY_INSTALLFAILED') . '<br /><a href="' . $this->libraryDownloadLink . '" target="_blank">' . Text::_('SYWLIBRARY_DOWNLOAD') .
                    '</a>', 'error');
                return false;
            }

            Factory::getApplication()->enqueueMessage(Text::sprintf('SYWLIBRARY_INSTALLED', $this->minimumLibrary), 'message');
        } else {

            $library_version = strval(simplexml_load_file(JPATH_ADMINISTRATOR . '/manifests/libraries/syw.xml')->version);
            if (!version_compare($library_version, $this->minimumLibrary, 'ge')) {

                if (!$this->installOrUpdatePackage($installer, 'pkg_sywlibrary', 'update')) {
                    Factory::getApplication()->enqueueMessage(Text::_('SYWLIBRARY_UPDATEFAILED') . '<br />' . Text::_('SYWLIBRARY_UPDATE'), 'error');
                    return false;
                }

                Factory::getApplication()->enqueueMessage(Text::sprintf('SYWLIBRARY_UPDATED', $this->minimumLibrary), 'message');
            }
        }

        return true;
    }

    private function uninstallExtension($type, $element, $folder = '')
    {
        $db = Factory::getDBO();

        $query = $db->getQuery(true);

        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($db->quoteName('type') . '=' . $db->quote($type));
        $query->where($db->quoteName('folder') . '=' . $db->quote($folder));
        $query->where($db->quoteName('element') . '=' . $db->quote($element));

        $db->setQuery($query);

        $extension_id = '';
        try {
            $extension_id = $db->loadResult();
        } catch (ExecutionFailureException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        if ($extension_id) {

            // first remove the package id to remove the locking mechanism

            $query->clear();

            $query->update('#__extensions');
            $query->set($db->quoteName('package_id') . '= 0');
            $query->where($db->quoteName('extension_id') . '=' . $db->quote($extension_id));

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }

            // uninstall the plugin

            $tmpInstaller = new Installer();
            if ($tmpInstaller->uninstall($type, $extension_id)) {
                return true;
            }
        }

        return false;
    }

    private function migrateOldProModule()
    {
        $db = Factory::getDBO();

        $query = $db->getQuery(true);

        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($db->quoteName('type') . '=' . $db->quote('module'));
        $query->where($db->quoteName('element') . '=' . $db->quote('mod_latestnewsenhancedextended'));

        $db->setQuery($query);

        $lnee_module_id = '';
        try {
            $lnee_module_id = $db->loadResult();
        } catch (ExecutionFailureException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        if ($lnee_module_id) { // there is a version of the pro module with the old name

            $query->clear();

            // get all instances of the old pro module

            $query->select('id');
            $query->select('title');
            $query->select('params');
            $query->from('#__modules');
            $query->where($db->quoteName('module') . '=' . $db->quote('mod_latestnewsenhancedextended'));

            $db->setQuery($query);

            $lne_instances = array();
            try {
                $lne_instances = $db->loadObjectList();
            } catch (ExecutionFailureException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }

            foreach ($lne_instances as $lne_instance) {

                // move newsfeeds / rsevents data from old json parameter

                $instance_params = json_decode($lne_instance->params, true);

                $modified_params = false;

                if (isset($instance_params['datasource']) && ($instance_params['datasource'] == 'newsfeeds' || $instance_params['datasource'] == 'rsevents')) {
                    $datasource_params = json_decode($instance_params['datasourcefields'], true);

                    if ($instance_params['datasource'] == 'newsfeeds') {
                        $instance_params['newsfeeds_url'] = $datasource_params['newsfeeds_url'];
                        $instance_params['newsfeeds_order'] = $datasource_params['newsfeeds_order'];
                        $instance_params['newsfeeds_count_for_feed'] = $datasource_params['newsfeeds_count_for_feed'];
                    } else if ($instance_params['datasource'] == 'rsevents') {
                        $instance_params['rsevents_events'] = $datasource_params['events'];
                        $instance_params['rsevents_categories'] = $datasource_params['categories'];
                    }

                    unset($instance_params['datasourcefields']);
                    $modified_params = true;
                }

                // update the instance

                $query->clear();

                $query->update('#__modules');
                if ($lne_instance->title == 'MOD_LATESTNEWSENHANCEDEXTENDED') {
                    $query->set($db->quoteName('title') . '=' . $db->quote('MOD_LATESTNEWSENHANCED'));
                }
                if ($modified_params) {
                    $query->set($db->quoteName('params') . '=' . $db->quote(json_encode($instance_params)));
                }
                $query->set($db->quoteName('module') . '=' . $db->quote('mod_latestnewsenhanced'));
                $query->where($db->quoteName('id') . '=' . $db->quote($lne_instance->id));

                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (ExecutionFailureException $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                    return false;
                }
            }

            // remove the old pro module
            // but first remove the package id to remove the locking mechanism

            $query->clear();

            $query->update('#__extensions');
            $query->set($db->quoteName('package_id') . '= 0');
            $query->where($db->quoteName('extension_id') . '=' . $db->quote($lnee_module_id));

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }

            $tmpInstaller = new Installer();
            if ($tmpInstaller->uninstall('module', $lnee_module_id)) {
                Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UNINSTALLED', 'mod_latestnewsenhancedextended'), 'message');
            } else {
                Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_ERRORUNINSTALLING', 'mod_latestnewsenhancedextended'), 'error');
            }
        }

        return true;
    }
}
