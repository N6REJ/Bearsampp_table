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

use Joomla\CMS\Uri\Uri as JUri;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\EditorButtonPlugin as RL_EditorButtonPlugin;
use RegularLabs\Library\Extension as RL_Extension;

defined('_JEXEC') or die;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
    || ! class_exists('RegularLabs\Library\Parameters')
    || ! class_exists('RegularLabs\Library\DownloadKey')
    || ! class_exists('RegularLabs\Library\EditorButtonPlugin')
)
{
    return;
}

if ( ! RL_Document::isJoomlaVersion(4))
{
    RL_Extension::disable('tabsaccordions', 'plugin', 'editors-xtd');

    return;
}

if (true)
{
    class PlgButtonTabsAccordions extends RL_EditorButtonPlugin
    {
        protected $button_icon = '<svg viewBox="0 0 24 24" style="fill:none;" width="24" height="24" fill="none" stroke="currentColor">'
        . '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M 15 7 L 15 9 L 21 9 L 21 7 C 21 5.9 20.1 5 19 5 L 17 5 C 15.9 5 15 5.9 15 7 ZM 9 7 L 9 9 L 15 9 L 15 7 C 15 5.9 14.1 5 13 5 L 11 5 C 9.9 5 9 5.9 9 7 ZM 3 7 L 3 17 C 3 18.105 3.895 19 5 19 L 19 19 C 20.105 19 21 18.105 21 17 L 21 9 L 9 9 L 9 7 C 9 5.9 8.1 5 7 5 L 5 5 C 3.895 5 3 5.895 3 7 Z" />'
        . '</svg>';

        protected function loadScripts()
        {
            $params = $this->getParams();

            RL_Document::scriptOptions([
                'tag_tabs_open'        => $params->tag_tabs_open,
                'tag_tabs_close'       => $params->tag_tabs_close,
                'tag_accordions_open'  => $params->tag_accordions_open,
                'tag_accordions_close' => $params->tag_accordions_close,
                'tag_characters'       => explode('.', $params->tag_characters),
                'root'                 => JUri::root(true),
            ], 'tabsaccordions_button');

            RL_Document::script('tabsaccordions.button');
        }
    }
}
