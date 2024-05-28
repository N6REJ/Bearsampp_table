<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\ArticleDetailsProfiles\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Http\Http;

/**
 * Article Deatils Profiles Info Controller
 */
class InfoController extends BaseController
{
	/**
	 * Retrieve status info
	 */
	public function getUpdates()
	{
		$HTTPClient = new Http();

		// Model instance
		$model = $this->getModel('info');
		$model->setState('option', 'articledetailsprofiles');
		$jsonObject = $model->getUpdates($HTTPClient);

		header('Content-type: application/json');
		echo json_encode($jsonObject);
	}
	
	/**
	 * Fetch and report update information in \JSON format, for AJAX requests
	 *
	 * @return  void
	 */
	public function ajaxversion()
	{
	    if (!Session::checkToken('get')) {
	        $this->app->setHeader('status', 403, true);
	        $this->app->sendHeaders();
	        echo Text::_('JINVALID_TOKEN_NOTICE');
	        $this->app->close();
	    }
	    
	    $model = $this->getModel('Info');
	    $updateInfo = $model->getUpdateInformation();
	    
	    $update = [];
	    if (isset($updateInfo['latest'])) {
	        $update[] = ['latest' => $updateInfo['latest'], 'relevance' => $updateInfo['relevance']];
	    }
	    
	    echo json_encode($update);
	    
	    $this->app->close();
	}

}
