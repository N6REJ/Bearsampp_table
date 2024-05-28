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

namespace RegularLabs\Plugin\System\TabsAccordions;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;

class Document
{
    static $all_themes;
    static $used_themes = [];

    public static function loadStylesAndScripts(&$buffer)
    {
        self::loadScripts();
        self::loadStyles();
    }

    public static function removeHeadStuff(&$html)
    {
        // Remove all scripts and styles if data-tabsaccordions attribute is not found
        if ( ! RL_RegEx::match('data-rlta', $html))
        {
            self::removeAllScriptsAndStyles($html);

            return;
        }

        // Otherwise only remove the unused styles
        self::removeUnusedStyles($html);
    }

    private static function getThemes()
    {
        if ( ! is_null(self::$all_themes))
        {
            return self::$all_themes;
        }

        $folder = JPATH_SITE . '/media/tabsaccordions/css';
        $files  = JFolder::files($folder, '^theme-[a-z0-9-_]+\.css$');

        $template = JFactory::getApplication()->getTemplate();
        $folder   = JPATH_SITE . '/media/templates/site/' . $template . '/css/tabsaccordions';

        if (is_dir($folder))
        {
            $files_template = JFolder::files($folder, '^theme-[a-z0-9-_]+\.css$');
            $files_template = empty($files_template) ? [] : $files_template;
            $files          = [...$files, ...$files_template];
        }

        $files = array_unique($files);

        $themes = [];

        foreach ($files as $file)
        {
            $file_name = JFile::stripExt($file);
            $file_name = substr($file_name, 6);

            $themes[] = $file_name;
        }

        self::$all_themes = array_unique($themes);

        return self::$all_themes;
    }

    private static function getUsedThemes($html)
    {
        $params = Params::get();

        $themes = [$params->theme];

        if ( ! RL_RegEx::matchAll('data-rlta-theme="([^"]*)"', $html, $matches, null, PREG_PATTERN_ORDER))
        {
            return $themes;
        }

        return array_unique([...$themes, ...$matches[1]]);
    }

    private static function loadScripts()
    {
        $params = Params::get();

        $settings = (object) [
            'switchToAccordions'         => (bool) ($params->switch_to_accordions ?? true),
            'switchBreakPoint'           => (int) ($params->switch_break_point ?? 576),
            'buttonScrollSpeed'          => max(1, min(10, (int) ($params->button_scroll_speed ?? 5))),
            'addHashToUrls'              => (bool) ($params->use_url_hash ?? false),
            'rememberActive'             => (bool) ($params->remember_active ?? false),
            'wrapButtons'                => isset($params->fit_buttons) ? $params->fit_buttons == 'wrap' : false,
        ];

        RL_Document::script('tabsaccordions.script', ['type' => 'module']);
        RL_Document::scriptDeclaration(
            'rltaSettings = ' . json_encode($settings),
            'tabsaccordions'
        );

        JText::script('RLTA_BUTTON_SCROLL_LEFT', true);
        JText::script('RLTA_BUTTON_SCROLL_RIGHT', true);
    }

    private static function loadStyles()
    {
        $params = Params::get();

        if ( ! $params->load_stylesheet)
        {
            RL_Document::style('tabsaccordions.theme-custom');

            return;
        }

        RL_Document::style('tabsaccordions.style');

        $themes = self::getThemes();

        foreach ($themes as $theme)
        {
            RL_Document::style('tabsaccordions.theme-' . $theme);
        }
    }

    private static function removeAllScriptsAndStyles(&$html)
    {
        // Prevent the tabsaccordions.button and tabsaccordions.popup script from being removed
        RL_Protect::protectByRegex($html, '<script [^>]*src="[^"]*/(tabsaccordions/js|js/tabsaccordions)/(button|popup)\..*?>');

        // remove style and script if no items are found
        RL_Document::removeScriptsStyles($html, 'tabsaccordions');
        RL_Document::removeScriptsOptions($html, 'tabsaccordions');

        RL_Protect::unprotect($html);
    }

    private static function removeUnusedStyles(&$html)
    {
        $all_themes    = self::getThemes();
        $used_themes   = self::getUsedThemes($html);
        $unused_themes = array_diff($all_themes, $used_themes);

        foreach ($unused_themes as $theme)
        {
            RL_Document::removeStyleTag($html, 'tabsaccordions', 'theme-' . $theme);
        }
    }
}
