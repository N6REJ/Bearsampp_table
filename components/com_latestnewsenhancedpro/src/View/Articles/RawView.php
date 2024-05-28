<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Site\View\Articles;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

/**
 * Raw View class for the Latest News Enhanced Pro component (used in load more)
 */
class RawView extends HtmlView
{
	protected $items;
	protected $params;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$this->input = $app->input;

		$this->params = $app->getParams('com_latestnewsenhancedpro');

		$total_items = $app->getUserState('com_latestnewsenhancedpro.ajaxtotal', 0);

		if ($app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0) == 0) {
			$app->setUserState('com_latestnewsenhancedpro.ajaxstart', $this->params->get('initial_count', 10));
		} else {
			$app->setUserState('com_latestnewsenhancedpro.ajaxstart', $app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0) + $this->params->get('more_count', 3));
		}

		if ($total_items < $app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0) + $this->params->get('more_count', 3)) {
			//$app->setUserState('com_latestnewsenhancedpro.ajaxstart', max(0, (int) (ceil($total_items / $this->params->get('more_count', 3)) - 1) * $this->params->get('more_count', 3)));
			$this->input->set('limit', $total_items - $app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0));
		}

		if ($app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0) >= $total_items) {
			return;
		}

		$this->input->set('limitstart', $app->getUserState('com_latestnewsenhancedpro.ajaxstart', 0));

		$this->items = $this->get('Items');

		$this->load_more = true;

		// leading items are not allowed for the following items
		//$this->possibleLeading = false;

		$this->categories_list = $this->get('CategoriesList');

		$this->bootstrap_version = $this->params->get('bootstrap_version', 'joomla');
		$this->load_bootstrap = false;
		if ($this->bootstrap_version === 'joomla') {
			$this->bootstrap_version = 5;
			$this->load_bootstrap = true;
		} else {
			$this->bootstrap_version = intval($this->bootstrap_version);
		}
		
		$this->site_mode = Helper::getSiteMode($this->params);

		$this->show_errors = Helper::isShowErrors($this->params);

		$this->popup_width = $this->params->get('popup_x', 600);
		$this->popup_height = $this->params->get('popup_y', 500);

		// display the items
		//parent::display('items'); // will call onAfterDisplay events!!!
		echo $this->loadTemplate('items');
	}

}
