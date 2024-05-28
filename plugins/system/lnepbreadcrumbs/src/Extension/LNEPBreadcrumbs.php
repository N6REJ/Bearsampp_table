<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Plugin\System\LNEPBreadcrumbs\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

final class LNEPBreadcrumbs extends CMSPlugin implements SubscriberInterface
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
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     */
    public static function getSubscribedEvents(): array
    {
        try {
            $app = Factory::getApplication();
        } catch (\Exception $e) {
            return [];
        }
        
        if (!$app->isClient('site')) {
            return [];
        }
        
        return [
            'onBeforeRender' => 'onBeforeRender',
        ];
    }
    
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        if (!$this->app) {
            $this->app = Factory::getApplication();
        }
    }

	public function onBeforeRender()
	{
		if (!$this->app->isClient('site')) {
			return;
		}

		if ($this->app->input->getString('tmpl', '') == 'component') { // no breadcrumb in popup
			return;
		}

		if ($this->app->input->get('option', '') == 'com_content') {

			$pathway = $this->app->getPathway();

			$pathway_items = $pathway->getPathway();

			$found_index = -1;
			foreach ($pathway_items as $i => $pathway_item) {
			    if (isset($pathway_item->link) && !empty($pathway_item->link) && strpos($pathway_item->link, 'option=com_latestnewsenhancedpro&view=articles') !== false) {
					$found_index = $i;
					break;
				}
			}

			if ($found_index >= 0) {

				$pathway->setPathway(array());

				foreach ($pathway_items as $i => $pathway_item) {

					if ($i > $found_index) {

						if (strpos($pathway_item->link, 'option=com_content&view=categories') !== false) {
							continue;
						}

						if (strpos($pathway_item->link, 'option=com_content&view=category') !== false && !$this->params->get('keep_category', 0)) {
							continue;
						}
					}

					$pathway->addItem($pathway_item->name, $pathway_item->link);
				}
			}

		} else if ($this->app->input->get('option', '') == 'com_k2') {

			$pathway = $this->app->getPathway();

			$pathway_items = $pathway->getPathway();

			$found_index = -1;
			foreach ($pathway_items as $i => $pathway_item) {
				if (strpos($pathway_item->link, 'option=com_latestnewsenhancedpro&view=k2items') !== false) {
					$found_index = $i;
					break;
				}
			}

			if ($found_index >= 0) {

				$pathway->setPathway(array());

				$config = Factory::getConfig();

				$category_string = 'option=com_k2&amp;view=itemlist&amp;task=category'; // sef : 0
				if ($config->get('sef') == '1') {
					//if ($config->get('sef_rewrite') == '0') {
						$category_string = 'itemlist/category';	// sef : 1, sef_rewrite : 0
					//} else {
						// what is the value? identical
						// sef : 1, sef_rewrite : 1
					//}
				}

				foreach ($pathway_items as $i => $pathway_item) {

					if ($i > $found_index) {

						if (strpos($pathway_item->link, 'option=com_k2&view=itemlist&layout=category') !== false) {
							continue;
						}

						if (strpos($pathway_item->link, $category_string) !== false && !$this->params->get('keep_category', 0)) {
							continue;
						}
					}

					$pathway->addItem($pathway_item->name, $pathway_item->link);
				}
			}
		}
	}
}
