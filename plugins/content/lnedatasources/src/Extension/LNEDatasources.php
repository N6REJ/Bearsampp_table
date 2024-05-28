<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Plugin\Content\LNEDatasources\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Plugin that switches form fields for new datasources
 */
final class LNEDatasources extends CMSPlugin
{
    /**
     * Application object.
     * Needed for compatibility with Joomla 4 < 4.2
     * Ultimately, we should use $this->getApplication() in Joomla 6
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     */
    protected $app;
    
    /**
    * The supported form contexts
    *
    * @var    array
    */
    protected $supportedContext = [
        'com_modules.module',
        'com_advancedmodules.module',
    ];
    
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        if (!$this->app) {
            $this->app = Factory::getApplication();
        }
    }

	public function onContentPrepareForm($form, $data)
	{
		if (!$this->app->isClient('administrator')) {
		    return true;
		}

		if (!($form instanceof Form)) {
		    return true;
		}
		
		if (!in_array($form->getName(), $this->supportedContext)) {
		    return true;
		}
			
		if (is_array($data)) {
			$data = (object) $data;
		}

		if (isset($data->module) && $data->module === 'mod_latestnewsenhanced') {
			PluginHelper::importPlugin('latestnewsenhanced');
			Factory::getApplication()->triggerEvent('onLatestNewsEnhancedPrepareForm', array($form, $data));
		}

		return true;
	}

	public function onExtensionBeforeSave($context, $item, $isNew)
	{
		if (!$this->app->isClient('administrator')) {
			return true; // returning false will break saving of modules in the frontend
		}

		if (!in_array($context, $this->supportedContext) || $item->module !== 'mod_latestnewsenhanced') {
			return true;
		}

		PluginHelper::importPlugin('latestnewsenhanced');
		Factory::getApplication()->triggerEvent('onLatestNewsEnhancedBeforeSave', array(&$item, $isNew));

		return true;
	}

}
