<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use SYW\Plugin\Quickicon\LatestNewsEnhancedPro\Extension\LatestNewsEnhancedPro;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                
                $plugin = new LatestNewsEnhancedPro(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('quickicon', 'latestnewsenhancedpro')
                );
                
                //$plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
