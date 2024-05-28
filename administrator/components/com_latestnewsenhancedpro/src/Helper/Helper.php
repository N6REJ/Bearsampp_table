<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;

/**
 * Latest News Enhanced Pro helper.
 */
class Helper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName
	 * @return void
	 */
// 	public static function addSubmenu($vName = 'info')
// 	{
// 		JHtmlSidebar::addEntry(
// 			Text::_('COM_LATESTNEWSENHANCEDPRO_SUBMENU_INFO'),
// 			'index.php?option=com_latestnewsenhancedpro&amp;view=info',
// 			$vName == 'info'
// 		);

// 		$lang = Factory::getLanguage();
// 		$lang->load('com_content', JPATH_ADMINISTRATOR);

// 		JHtmlSidebar::addEntry('<hr>', '');

// 		JHtmlSidebar::addEntry(Text::_('JGLOBAL_ARTICLES'), 'index.php?option=com_content&view=articles');
// 		JHtmlSidebar::addEntry(Text::_('COM_CONTENT_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&extension=com_content');

// 		if (\JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

// 			JHtmlSidebar::addEntry(Text::_('JGLOBAL_FIELDS'), 'index.php?option=com_fields&context=com_content.article');
// 			JHtmlSidebar::addEntry(Text::_('JGLOBAL_FIELD_GROUPS'), 'index.php?option=com_fields&view=groups&context=com_content.article');
// 		}

// 		JHtmlSidebar::addEntry(Text::_('COM_CONTENT_SUBMENU_FEATURED'), 'index.php?option=com_content&view=featured' );

// 		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_k2') && ComponentHelper::isEnabled('com_k2')) {

// 		    $lang->load('com_k2', JPATH_ADMINISTRATOR);

// 		    JHtmlSidebar::addEntry('<hr>', '');
// 		    JHtmlSidebar::addEntry('<legend>K2</legend>', '');

// 		    JHtmlSidebar::addEntry(Text::_('K2_ITEMS'), 'index.php?option=com_k2&view=items');
// 		    JHtmlSidebar::addEntry(Text::_('K2_CATEGORIES'), 'index.php?option=com_k2&view=categories');
// 		    JHtmlSidebar::addEntry(Text::_('K2_EXTRA_FIELDS'), 'index.php?option=com_k2&view=extrafields');
// 		    JHtmlSidebar::addEntry(Text::_('K2_EXTRA_FIELD_GROUPS'), 'index.php?option=com_k2&view=extrafieldsgroups');
// 		}
// 	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    CMSObject
	 */
	public static function getActions()
	{
		$user = Factory::getUser();
		$result = new CMSObject();

		$assetName = 'com_latestnewsenhancedpro';

		$actions = array(
			'core.admin', 'core.manage'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

}
