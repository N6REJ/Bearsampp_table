<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         9.0.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2023 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ActionLogPlugin as RL_ActionLogPlugin;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Extension as RL_Extension;

defined('_JEXEC') or die;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
    || ! class_exists('RegularLabs\Library\Parameters')
    || ! class_exists('RegularLabs\Library\DownloadKey')
    || ! class_exists('RegularLabs\Library\ActionLogPlugin')
)
{
    return;
}

if ( ! RL_Document::isJoomlaVersion(4))
{
    RL_Extension::disable('regularlabsmanager', 'plugin', 'actionlog');

    return;
}

if (true)
{
    class PlgActionlogRegularLabsManager extends RL_ActionLogPlugin
    {
        public $name  = 'REGULARLABSEXTENSIONMANAGER';
        public $alias = 'regularlabsmanager';

        public function onExtensionAfterInstall($installer, $eid)
        {
            // Prevent duplicate logs
            if (in_array('install_' . $eid, self::$ids, true))
            {
                return;
            }

            $context = JFactory::getApplication()->input->get('option', '');

            if ( ! str_contains($context, $this->option))
            {
                return;
            }

            if ( ! RL_Array::find(['*', 'install'], $this->events))
            {
                return;
            }

            $extension = RL_Extension::getById($eid);

            if (empty($extension->manifest_cache))
            {
                return;
            }

            $manifest = json_decode($extension->manifest_cache);

            if (empty($manifest->name) || empty($manifest->type))
            {
                return;
            }

            self::$ids[] = 'install_' . $eid;

            $message = [
                'action'         => 'install',
                'type'           => $this->lang_prefix_install . '_TYPE_' . strtoupper($manifest->type),
                'id'             => $eid,
                'extension_name' => JText::_($manifest->name),
            ];

            $languageKey = $this->lang_prefix_install . '_' . strtoupper($manifest->type) . '_INSTALLED';

            if ( ! JFactory::getApplication()->getLanguage()->hasKey($languageKey))
            {
                $languageKey = $this->lang_prefix_install . '_EXTENSION_INSTALLED';
            }

            $this->addLog([$message], $languageKey, 'com_regularlabsmanager');
        }
    }
}
