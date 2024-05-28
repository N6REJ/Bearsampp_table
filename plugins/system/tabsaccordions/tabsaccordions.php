<?php
/**
 * @package         Tabs & Accordions
 * @version         2.1.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Extension as RL_Extension;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\SystemPlugin as RL_SystemPlugin;
use RegularLabs\Plugin\System\TabsAccordions\Document;
use RegularLabs\Plugin\System\TabsAccordions\Params;
use RegularLabs\Plugin\System\TabsAccordions\Replace;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
    || ! class_exists('RegularLabs\Library\Parameters')
    || ! class_exists('RegularLabs\Library\DownloadKey')
    || ! class_exists('RegularLabs\Library\SystemPlugin')
)
{
    JFactory::getLanguage()->load('plg_system_tabsaccordions', __DIR__);
    JFactory::getApplication()->enqueueMessage(
        JText::sprintf('RLTA_EXTENSION_CAN_NOT_FUNCTION', JText::_('TABSACCORDIONS'))
        . ' ' . JText::_('RLTA_REGULAR_LABS_LIBRARY_NOT_INSTALLED'),
        'error'
    );

    return;
}

if ( ! RL_Document::isJoomlaVersion(4, 'TABSACCORDIONS'))
{
    RL_Extension::disable('tabsaccordions', 'plugin');

    RL_Document::adminError(
        JText::sprintf('RL_PLUGIN_HAS_BEEN_DISABLED', JText::_('TABSACCORDIONS'))
    );

    return;
}

if (true)
{
    class PlgSystemTabsAccordions extends RL_SystemPlugin
    {
        public    $_lang_prefix     = 'RLTA';
        protected $_jversion        = 4;
        public    $_enable_in_admin = true;

        public function __construct(&$subject, $config = [])
        {
            parent::__construct($subject, $config);

            $params = Params::get();

            $this->_enable_in_admin = $params->enable_admin;
        }

        protected function loadStylesAndScripts(&$buffer)
        {
            Document::loadStylesAndScripts($buffer);
        }

        public function processArticle(
            &$string,
            $area = 'article',
            $context = '',
            $article = null,
            $page = 0
        )
        {
            Replace::render($string);
        }

        /**
         * @param object $module
         * @param array  $params
         */
        protected function handleOnAfterRenderModule(&$module, &$params): void
        {
            if ( ! isset($module->content))
            {
                return;
            }

            Replace::render($module->content);
        }

        protected function changeDocumentBuffer(&$buffer)
        {
            return Replace::render($buffer);
        }

        protected function changeFinalHtmlOutput(&$html)
        {
            [$pre, $body, $post] = RL_Html::getBody($html);

            $body = Replace::render($body);

            $html = $pre . $body . $post;

            return true;
        }

        protected function cleanFinalHtmlOutput(&$html)
        {
            Document::removeHeadStuff($html);

            return true;
        }
    }
}
