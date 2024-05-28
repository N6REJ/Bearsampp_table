<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Plugin\Content\RelatedNewsInArticle\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * No support for K2
 *
 */
final class RelatedNewsInArticle extends CMSPlugin
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
     * Load the language file on instantiation.
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;
    
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

		// If we are on the save command, no data is passed to $data variable, we need to get it directly from request
		$jformData = $this->app->input->get('jform', [], 'array');
		
		if ($jformData && !$data) {
		    $data = $jformData;
		}
		
		if (is_array($data)) {
		    $data = (object) $data;
		}

		if ($data->module == 'mod_latestnewsenhanced' && $data->position == 'inside-article') {
		    Form::addFormPath(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/forms');
			$form->loadFile('module', false);
		}

		return true;
	}

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
	    if (!$this->app->isClient('site')) {
	        return true;
	    }

		$canProceed = ($context == 'com_content.article' && $this->app->input->get('view') == 'article');
		if (!$canProceed) {
		    return true;
		}

		if (!isset($row->text)) {
		    return true;
		}

		// get the modules and their locations

		$modules_to_use = array();
		$locations = array();
		$fallbacks = array(); // use it to avoid checking params again later

		$modules = ModuleHelper::getModules('inside-article');
		foreach ($modules as $module) {
			if ($module->module == 'mod_latestnewsenhanced') {

				// get module parameters
				$module_params = new Registry();
				$module_params->loadString($module->params);

				if ($module_params->get('datasource') == 'articles') {
					$modules_to_use[] = $module;

					// get module location inside the article
					$location = $module_params->get('location_inside', 'paragraph');
					if ($location == 'paragraph') {
						$location = $module_params->get('after_paragraph', 1);
						if ($location < 1) {
							$location = 1;
						}
					}
					$locations[$module->id] = $location;
					$fallbacks[$module->id] = $module_params->get('after_paragraph_fallback', 0);
				}
			}
		}

		if (empty($modules_to_use)) {
			return;
		}

		// iterate through each paragraph

		$delimiter = '</p>';

		$renderer = Factory::getDocument()->loadRenderer('module');

		$tmp_output_before = '';

		// location 'before'
		foreach ($modules_to_use as $key => $module) {

			if ($locations[$module->id] == 'before') {

				$module_output = trim($renderer->render($module, array('style' => $module->style)));
				if ($module_output) {
					$tmp_output_before .= $module_output;
				}

				unset($modules_to_use[$key]);
			}
		}

		// Articles: look through $row->text

		// location 'after paragraph'

		$paragraphs = array_map('trim', explode($delimiter, $row->text)); // array_map trims the strings
		$paragraphs = array_filter($paragraphs, 'strlen'); // array_filter eliminates empty strings

		if (!empty($paragraphs)) {

			foreach ($paragraphs as $index => $paragraph) {

				if ($paragraph) {
					$paragraphs[$index] .= $delimiter;
				}

				foreach ($modules_to_use as $key => $module) {

					if ($locations[$module->id] == $index + 1) {

						$module_output = trim($renderer->render($module, array('style' => $module->style)));
						if ($module_output) {
							$paragraphs[$index] .= $module_output;
						}

						unset($modules_to_use[$key]);
					}
				}
			}

			$row->text = $tmp_output_before.implode('', $paragraphs);
		} else {
			$row->text = $tmp_output_before.$row->text;
		}

		// location 'after paragraph' for missed location and fallback
		foreach ($modules_to_use as $key => $module) {

			// TODO would be nice to order the modules by the location (all but 'after')

			if ($locations[$module->id] != 'after' && $fallbacks[$module->id]) {

				$module_output = trim($renderer->render($module, array('style' => $module->style)));
				if ($module_output) {
					$row->text .= $module_output;
				}

				unset($modules_to_use[$key]);
			}
		}

		// location 'after'
		foreach ($modules_to_use as $module) {

			if ($locations[$module->id] == 'after') {

				$module_output = trim($renderer->render($module, array('style' => $module->style)));
				if ($module_output) {
					$row->text .= $module_output;
				}
			}
		}
	}
}
