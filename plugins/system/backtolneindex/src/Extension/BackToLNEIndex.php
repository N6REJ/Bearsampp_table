<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\System\BackToLNEIndex\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

final class BackToLNEIndex extends CMSPlugin implements SubscriberInterface
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
            'onAfterRender'  => 'onAfterRender',
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

		// for the case the contact is shown in popup or print
		if ($this->app->input->getString('tmpl', '') == 'component') {
			return;
		}

		$view = $this->app->input->get('view', '');
		$option = $this->app->input->get('option', '');

		if (($option == 'com_content' && $view == 'article') || ($option == 'com_k2' && $view == 'item')) {
			$wam = Factory::getApplication()->getDocument()->getWebAssetManager();
			$wam->useStyle('fontawesome');
		}
	}

    public function onAfterRender()
    {
        if (!$this->app->isClient('site')) {
            return;
        }

        // for the case the article is shown in popup or print
        if ($this->app->input->getString('tmpl', '') == 'component') {
            return;
        }

        $session = Factory::getSession();

        $view = $this->app->input->get('view', '');
        $option = $this->app->input->get('option', '');

        if ($option == 'com_latestnewsenhancedpro' && ($view == 'articles' || $view == 'k2items')) {

            // unique variables names to avoid issues with other extensions like TCP, which use the same mechanism
            $session->set('BACKTOINDEX_LNEP_OPTION', 'com_latestnewsenhancedpro');
            //$session->set('BACKTOINDEX_LNEP_URL', Factory::getUri()->getPath()); // in multilangual environments, the value changes!

            return true;
        }

        // if we need to login first before accessing the article page, avoid clearing the session variables
        if ($option == 'com_users' && $view == 'login' && $this->app->input->get('return', '')) {
            return true;
        }

        if (($option == 'com_content' && $view == 'article') || ($option == 'com_k2' && $view == 'item')) {

            $referer_option = $session->get('BACKTOINDEX_LNEP_OPTION');
            if (!isset($referer_option)) {
                return;
            }

            if ($referer_option != 'com_latestnewsenhancedpro') {
                $session->clear('BACKTOINDEX_LNEP_OPTION');
                return;
            }

            $referer_url = $this->app->input->server->getString('HTTP_REFERER', '');
            if (empty($referer_url)) {
                $session->clear('BACKTOINDEX_LNEP_OPTION');
                return;
            }

            $output = $this->app->getBody();

            $raw_pattern = trim($this->params->get('pattern', ''));

            if ($raw_pattern) {
                $pattern = '#' . $raw_pattern . '#';
            } else {
                $pattern = '#<div class="com-content-article item-page#';
                if ($this->app->input->get('option', '') == 'com_k2') {
                    $pattern = '#<div class="itemHeader#';
                }
            }

            ob_start();
            include PluginHelper::getLayoutPath('system', 'backtolneindex');
            $replacement = ob_get_clean();

            if ($raw_pattern) {
                $replacement .= $raw_pattern;
            } else {
                if ($this->app->input->get('option', '') == 'com_k2') {
                    $replacement .= '<div class="itemHeader';
                } else {
                    $replacement .= '<div class="com-content-article item-page';
                }
            }

            $output = preg_replace($pattern, $replacement, $output, 1);

            $this->app->setBody($output);
        }

        $session->clear('BACKTOINDEX_LNEP_OPTION');

        return true;
    }
}
