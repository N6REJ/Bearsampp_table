<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;

class plgArticleDetailsMyDetails extends CMSPlugin
{
	protected $type = 'mydetails'; // the name that will be recognized by the extension. It should be identical to the plugin name
	protected $autoloadLanguage = true;

	/*
	 * Return the detail options available
	 */
	function onArticleDetailsPrepareDetailSelection()
	{
		$information_types = array(); // the list of information types to add to the module

		// all information detail types must start with the plugin name to avoid naming conflict
		$information_types[] = 'mydetails_url';

		return array('type' => $this->type, 'options' => $information_types);
	}

	/*
	 * Data information detail retrieval
	 *
	 * $detail array of detailed info
	 * ['info'] detail name
	 * ['prepend'] prepend text
	 * ['append'] append text
	 * ['show_icon'] show icon true|false
	 * ['icon'] the icon selected or '' (default)
	 * ['extra_classes'] CSS classes
	 *
	 * $params the extension's parameters
	 * $item object contains all the data for an item
	 * $item_params the items specific parameters
	 *
	 * list of available item properties, in the context of an article
	 * some properties may only be available under certain conditions
	 *
	 * id, title, alias, ...
	 */
	function onArticleDetailsGetDetailData($detail, $params, $item, $item_params)
	{
		// $detail['info'] must correspond to the information detail types that are set in onArticleDetailsPrepareDetailSelection
		// additional tests may be necessary in order to make sure the requested data is set

		$layout_suffix = $params->get('layout_suffix', '');

		if ($detail['info'] == 'mydetails_url')
		{
			$layout = new FileLayout('details.adp_detail_mydetails_url', null, array('component' => 'com_articledetailsprofiles')); // gets override from component

			if ($layout_suffix)
			{
				$layout->setSuffixes(array($layout_suffix));
			}

			$data = array('item' => $item, 'item_params' => $item_params, 'params' => $params, 'label' => $detail['prepend'], 'postinfo' => $detail['append'], 'show_icon' => $detail['show_icon'], 'icon' => $detail['icon'], 'extraclasses' => $detail['extra_classes']);

			return $layout->render($data);
		}

		return null;
	}

}