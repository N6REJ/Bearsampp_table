<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Http\Http;
use Joomla\Uri\Uri;

/**
 * LNEPInstaller Plugin
 */
class PlgInstallerLNEPInstaller extends CMSPlugin
{
    protected $server_remote_uri = 'https://simplifyyourweb.com/autoupdates/';
    protected $extension = 'com_latestnewsenhancedpro';
    protected $autoloadLanguage = true;

    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
		// check that we update a SimplifyYourWeb pro extension
		// 2 steps because of free add-ons
		if (preg_match('/simplifyyourweb.com\//', $url) == false) {
			return false;
		}

        if (preg_match('/latestnewsenhancedpro\//', $url) == false) {
            return false;
        }

        // dlid used by third-party services - the url already contains the dlid - do not go through the pre-checks
        if (strpos($url, 'dlid=') !== false) {
        	return true;
        }

        $downloadId = '';
        $uri = new Uri($url);

        // sywdlid is in the url if filled in the Joomla update site admin page
        if (strpos($url, 'dlidus=') !== false) {
        	$downloadId = $uri->getVar('dlidus');
        	$uri->delVar('dlidus');
        }

        // fetch download id from extension parameters
        if (empty($downloadId)) {
        	$downloadId = $this->params->get('download_id', '');
        }

        if ($downloadId) {

            // check if the download id is valid and the user license enabled here to provide better feedback to the user (other than 403 if download fails)

            $HTTPClient = new Http();

            $check_url = $this->server_remote_uri.'check.php?dlid='.$downloadId;

            try {
                $response = $HTTPClient->get($check_url)->body;
                if ($response) {
                    $status = json_decode($response, true);
                    if (empty($status)) {
                        Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_REMOTEDATABASEERROR'), 'warning');
                    } else {
                        if ($status['enabled'] == 0) {
                            Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_DISABLEDLICENSE'), 'warning');
                        } else {
                            if ($status['expiration_date']) {
                                $expiration_date = date(Text::_('DATE_FORMAT_LC3'), strtotime($status['expiration_date']));
                                if (strtotime('now') >= strtotime($status['expiration_date'])) {
                                    Factory::getApplication()->enqueueMessage(Text::sprintf('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_LICENSEEXPIRED', $expiration_date), 'warning');
                                } else {
                                    Factory::getApplication()->enqueueMessage(Text::sprintf('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_ACTIVELICENSE', $expiration_date), 'info');
                                    if ((strtotime($status['expiration_date']) - strtotime('now')) < (60 * 60 * 24 * 120) ) {
                                        Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_RENEWEARLY'), 'info');
                                    }
                                }
                            } else {
                                Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_FOREVERACTIVELICENSE'), 'info');
                            }
                        }
                    }
                } else {
                    Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_REMOTESTATUSREQUESTFAILED'), 'error');
                }
            } catch(\Exception $e) {
                Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_REMOTESTATUSREQUESTFAILED'), 'error');
            }
        } else {
            Factory::getApplication()->enqueueMessage(Text::_('PLG_INSTALLER_LNEPINSTALLER_MESSAGE_MISSINGDOWNLOADID'), 'warning');
        }

        $uri->setVar('dlid', $downloadId);

        $url = $uri->render();

        return true;
    }
}
