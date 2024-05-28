<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\InstallerAdapter;

/**
 * Script file of the Latest News Enhanced Pro component
 */
class PlgSystemLNEPBreadcrumbsInstallerScript
{
    public function __construct($parent)
    {
        Factory::getLanguage()->load('pkg_latestnewsenhancedpro.sys');
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
		return true;
	}

	/**
	 * method to install the component
	 *
	 * @return boolean True on success
	 */
	public function install($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_INSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_PLG_SYSTEM_LNEPBREADCRUMBS')), 'message');
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($installer)
	{
	    //Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UNINSTALLED', Text::_('PKG_LATESTNEWSENHANCEDPRO_PLG_SYSTEM_LNEPBREADCRUMBS')), 'message');
	}
	
	/**
	 * method to update the component
	 *
	 * @return boolean True on success
	 */
	public function update($installer)
	{
	    Factory::getApplication()->enqueueMessage(Text::sprintf('PKG_LATESTNEWSENHANCEDPRO_UPDATED', Text::_('PKG_LATESTNEWSENHANCEDPRO_PLG_SYSTEM_LNEPBREADCRUMBS')), 'message');
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
	    return true;
	}

}
