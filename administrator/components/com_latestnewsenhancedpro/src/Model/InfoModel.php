<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Http\Http;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Exception;

class InfoModel extends BaseDatabaseModel
{
    protected $extensionVersion;
    protected $server_remote_uri = 'https://updates.simplifyyourweb.com/pro/latestnewsenhancedpro/';
    protected $server_remote_uri_license = 'https://simplifyyourweb.com/autoupdates/';

	/**
	 * Get by remote server informations for new updates of this extension
	 *
	 * @access public
	 * @return mixed An object json decoded from server if update information retrieved correctly otherwise false
	 */
	public function getUpdates(Http $httpClient)
	{
	    $option = $this->getState('option');
	    if (!$option) {
	        return false;
	    }

	    try {
	        $response = $httpClient->get($this->server_remote_uri . $option . '-j4.json', [], 5);
	        if ($response->getStatusCode() == 200) {
	            $decodedUpdateInfos = json_decode($response->getBody(), true);
	            return $decodedUpdateInfos;
	        }
	    } catch (Exception $e) {
	        return false;
	    }

	    return false;
	}
	
	/**
	 * Get by remote server information for new updates of this extension
	 *
	 * @return array An array with 'latest' and 'relevance' information
	 */
	public function getUpdateInformation()
	{
	    $return = [];
	    
	    $http = new Http();
	    
	    try {
	        $response = $http->get($this->server_remote_uri . 'com_latestnewsenhancedpro-j4.json');
	    } catch (\RuntimeException $e) {
	        $response = null;
	    }
	    
	    if ($response === null || $response->code !== 200) {
	        return $return;
	    }
	    
	    return json_decode($response->getBody(), true);
	}

	/**
	 * Get by remote server informations for the license state for the extension
	 *
	 * @access public
	 * @return mixed An object json decoded from server if update information retrieved correctly otherwise false
	 */
	public function getLicenseState(Http $httpClient)
	{
	    // get the download id, if any

	    if (!PluginHelper::isEnabled('installer', 'lnepinstaller')) { // the plugin does not exist
	        return array('download_id' => '');
	    }

	    $installer_plugin = PluginHelper::getPlugin('installer', 'lnepinstaller');

	    $params = new Registry();
	    $params->loadString($installer_plugin->params);

	    $downloadId = $params->get('download_id', '');

	    if (empty($downloadId)) {
	        return array('download_id' => '');
	    }

	    // get the license info

	    $url = $this->server_remote_uri_license . 'check.php?dlid=' . $downloadId;

	    $license_status = array('download_id' => $downloadId);

	    try {
	        $response = $httpClient->get($url, [], 5)->body;
	        if ($response) {
	            $status = json_decode($response, true);
	            if (!empty($status)) {
	                $license_status = array_merge($license_status, $status);
	            }
	        }
	    } catch(\Exception $e) {
	        return $license_status;
	    }

	    return $license_status;
	}

	/**
	 * Get the plugins that extend the extension
	 *
	 * @access public
	 * @return
	 */
	public function getExtendedPlugins()
	{
	    $db = Factory::getDbo();

	    $query = $db->getQuery(true)
	    	->select($db->quoteName(array('element', 'extension_id'), array('name', 'id')))
	        ->from('#__extensions')
	        ->where('type = ' . $db->quote('plugin'))
	        ->where('folder = ' . $db->quote('latestnewsenhanced'))
	        ->where('state IN (0,1)')
	        ->order('ordering');

        $db->setQuery($query);

        try {
            return $db->loadObjectList();
        } catch (ExecutionFailureException $e) {
            return array();
        }
	}

}
