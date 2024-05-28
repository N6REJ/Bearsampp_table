<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\ArticleDetailsProfiles\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;

/**
 * Article Details Profiles helper
 *
 */
class ArticleDetailsProfilesHelper extends ContentHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName
	 * @return void
	 */
// 	public static function addSubmenu($vName = 'info')
// 	{
// 	    HtmlSidebar::addEntry(
// 	        Text::_('COM_ARTICLEDETAILSPROFILES_SUBMENU_INFO'),
// 	        'index.php?option=com_articledetailsprofiles&amp;view=info',
// 	        $vName == 'info'
// 		);

// 	    $lang = Factory::getLanguage();
// 	    $lang->load('com_content', JPATH_ADMINISTRATOR);

// 	    JHtmlSidebar::addEntry('<hr>', '');

// 	    JHtmlSidebar::addEntry(
// 	        Text::_('JGLOBAL_ARTICLES'),
// 	        'index.php?option=com_content&view=articles',
// 	        $vName == 'articles'
// 	        );

// 	    JHtmlSidebar::addEntry(
// 	        Text::_('COM_CONTENT_SUBMENU_CATEGORIES'),
// 	        'index.php?option=com_categories&extension=com_content',
// 	        $vName == 'categories'
// 	        );

// 	    if (\JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

// 	        JHtmlSidebar::addEntry(
// 	            Text::_('JGLOBAL_FIELDS'),
// 	            'index.php?option=com_fields&context=com_content.article',
// 	            $vName == 'fields.fields'
// 	            );

// 	        JHtmlSidebar::addEntry(
// 	            Text::_('JGLOBAL_FIELD_GROUPS'),
// 	            'index.php?option=com_fields&view=groups&context=com_content.article',
// 	            $vName == 'fields.groups'
// 	            );
// 	    }

// 	    JHtmlSidebar::addEntry(
// 	        Text::_('COM_CONTENT_SUBMENU_FEATURED'),
// 	        'index.php?option=com_content&view=featured',
// 	        $vName == 'featured'
// 	        );
// 	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    CMSObject
	 */
// 	public static function getActions()
// 	{
// 		$user = Factory::getUser();
// 		$result = new CMSObject();

// 		$assetName = 'com_articledetailsprofiles';

// 		$actions = array(
// 			'core.admin', 'core.manage'
// 		);

// 		foreach ($actions as $action) {
// 			$result->set($action, $user->authorise($action, $assetName));
// 		}

// 		return $result;
// 	}

}
