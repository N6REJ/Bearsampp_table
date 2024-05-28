<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Quickicon\LatestNewsEnhancedPro\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;
use Joomla\Module\Quickicon\Administrator\Event\QuickIconsEvent;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Plugin to check if there is a new version available
 */
final class LatestNewsEnhancedPro extends CMSPlugin implements SubscriberInterface
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
     * Load plugin language files automatically.
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
        return [
            'onGetIcons' => 'onGetIcons',
        ];
    }
    
    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe.
     * @param   array   $config    An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        if (!$this->app) {
            $this->app = Factory::getApplication();
        }
    }

    /**
     * Check extensions' available version.
     *
     * @param   QuickIconsEvent  $event  The event object
     *
     * @return  void
     */
    public function onGetIcons(QuickIconsEvent $event)
    {
        $context = $event->getContext();

        if (
            $context !== $this->params->get('context', 'update_quickicon')
            || !$this->app->getIdentity()->authorise('core.admin', 'com_latestnewsenhancedpro')
            || !ComponentHelper::isEnabled('com_latestnewsenhancedpro')
        ) {
            return;
        }

        $token = Session::getFormToken() . '=' . 1;
        $extension_path  = 'index.php?option=com_latestnewsenhancedpro';
        
        $currentVersion = strval(simplexml_load_file(JPATH_BASE . '/components/com_latestnewsenhancedpro/latestnewsenhancedpro.xml')->version);
        
        Text::script('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_UNKNOWN_MESSAGE');
        Text::script('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_VERSION_UPTODATE');
        Text::script('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_VERSION_OUTDATED');

        $options  = [
            'updateUrl' => Uri::base() . 'index.php?option=com_installer&view=update',
            'ajaxUrl' => Uri::base() . $extension_path . '&task=info.ajaxversion&format=json&' . $token,
            'version' => $currentVersion,
        ];
        
        $wam = $this->app->getDocument()->getWebAssetManager();

        $this->app->getDocument()->addScriptOptions('js-latest-news-enhanced-check', $options);

        $wam->registerAndUseScript('plg_quickicon_latestnewsenhancedpro', 'plg_quickicon_latestnewsenhancedpro/latestnewsenhancedpro.js', [], ['defer' => true], ['core']);

        $inlineCSS = <<< CSS
            .icon-lnep {
                -webkit-mask-image: url("../media/com_latestnewsenhancedpro/images/lnep.svg");
                mask-image: url("../media/com_latestnewsenhancedpro/images/lnep.svg");
                -webkit-mask-size: 100%;
                mask-size: 100%;
                width: 32px;
                height: 32px;
                background-color: var(--icon-color);
            }
CSS;
        $wam->addInlineStyle($inlineCSS);
        
        // Add the icon to the result array
        $result = $event->getArgument('result', []);

        $result[] = [
            [
                'link'  => $extension_path,
                'image' => 'icon-lnep',
                'icon'  => '',
                'text'  => $this->app->getLanguage()->_('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_CHECKING'),
                'id'    => 'plg_quickicon_latestnewsenhancedpro',
                'group' => 'MOD_QUICKICON_MAINTENANCE',
            ],
        ];

        $event->setArgument('result', $result);
    }
}
