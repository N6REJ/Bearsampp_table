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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Uri\Uri as JUri;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

/**
 * Class Output
 *
 * @package RegularLabs\Plugin\System\TabsAccordions
 */
class Replace
{
    static $layout_path = JPATH_PLUGINS . '/system/tabsaccordions/layouts';

    /**
     * @param string $string
     *
     * @return string
     */
    public static function render(string &$string): string
    {
        Protect::_($string);

        // Tag syntax: {tab ...}

        self::replaceSyntax($string);

        // Closing tag: {/tab}

        self::replaceClosingTag($string);

        // Links with #tab-name or &tab=tab-name
        self::replaceLinks($string);

        RL_Protect::unprotect($string);

        return $string;
    }

    private static function replaceClosingTag(&$string)
    {
        $regex = Params::getRegex('end');

        RL_RegEx::matchAll($regex, $string, $matches);

        if (empty($matches))
        {
            return;
        }

        foreach ($matches as $match)
        {
            $html = [];

            $html[] = LayoutHelper::render('panel_end', [], self::$layout_path);
            $html[] = LayoutHelper::render('container_end', [], self::$layout_path);

            if (Params::get()->place_comments)
            {
                $html[] = Protect::getCommentEndTag();
            }

            [$pre, $post] = RL_Html::cleanSurroundingTags([$match['pre'], $match['post']]);

            $html = $pre . implode('', $html) . $post;

            $string = RL_String::replaceOnce($match[0], $html, $string);
        }
    }

    private static function replaceItem(&$string, $set, $item, $is_first)
    {
        $html = [];

        if ($is_first && Params::get()->place_comments)
        {
            $html[] = Protect::getCommentStartTag();
        }

        $html[] = $is_first
            ? LayoutHelper::render(
                'container_start',
                $set,
                self::$layout_path
            )
            : LayoutHelper::render('panel_end', [], self::$layout_path);

        $html[] = LayoutHelper::render('button', compact('item', 'set'), self::$layout_path);
        $html[] = LayoutHelper::render('panel_start', compact('item', 'set'), self::$layout_path);

        $html = $item->pre . implode('', $html) . $item->post;

        $string = RL_String::replaceOnce(
            $item->original_match,
            $html,
            $string
        );
    }

    private static function replaceLinks(&$string)
    {
        // Links with #tab-name
        self::replaceLinksWithHashes($string);
        // Links with &tab=tab-name
        self::replaceLinksWithUrlParameters($string);
    }

    private static function replaceLinksByRegEx(&$string, $regex)
    {
        RL_RegEx::matchAll(
            $regex,
            $string,
            $matches
        );

        if (empty($matches))
        {
            return;
        }

        self::replaceLinksMatches($string, $matches);
    }

    private static function replaceLinksMatches(&$string, $matches)
    {
        $uri            = JUri::getInstance();
        $current_urls   = [];
        $current_urls[] = $uri->toString(['path']);
        $current_urls[] = ltrim($uri->toString(['path']), '/');
        $current_urls[] = $uri->toString(['scheme', 'host', 'path']);
        $current_urls[] = $uri->toString(['scheme', 'host', 'path']) . '/';
        $current_urls[] = $uri->toString(['scheme', 'host', 'port', 'path']);
        $current_urls[] = $uri->toString(['scheme', 'host', 'port', 'path']) . '/';

        foreach ($matches as $match)
        {
            $attributes = $match['attributes'];

            if (
                str_contains($attributes, 'data-toggle=')
                || str_contains($attributes, 'onclick=')
            )
            {
                continue;
            }

            $url = $match['url'];

            if (str_starts_with($url, 'index.php/'))
            {
                $url = '/' . $url;
            }

            if (str_starts_with($url, 'index.php'))
            {
                $url = JRoute::link('site', $url);
            }

            if ($url != '' && ! in_array($url, $current_urls))
            {
                continue;
            }

            $id = $match['id'];

            if ( ! self::stringHasItem($string, $id))
            {
                // This is a link to a normal anchor or other element on the page
                continue;
            }

            $attributes .= ' onclick="RegularLabs.TabsAccordions.open(\'' . $id . '\');return false;"';

            $string = str_replace($match[0], '<a ' . $attributes . '>', $string);
        }
    }

    private static function replaceLinksWithHashes(&$string)
    {
        self::replaceLinksByRegEx(
            $string,
            '<a\s(?<attributes>[^>]*href="(?<url>[^"]*)\#(?<id>[^"]*)"[^>]*)>',
        );
    }

    private static function replaceLinksWithUrlParameters(&$string)
    {
        self::replaceLinksByRegEx(
            $string,
            '<a\s(?<attributes>[^>]*href="(?<url>[^"]*)(?:\?|&(?:amp;)?)(?:tab|accordion)=(?<id>[^"\#&]*)(?:\#[^"]*)?"[^>]*)>',
        );
    }

    private static function replaceSet(&$string, $set)
    {
        foreach ($set->items as $i => $item)
        {
            self::replaceItem($string, $set, $item, $i === 0);
        }
    }

    private static function replaceSyntax(&$string)
    {
        $sets = (new Sets)->get($string);

        foreach ($sets as $set)
        {
            self::replaceSet($string, $set);
        }
    }

    private static function stringHasItem(&$string, $id)
    {
        return (str_contains($string, 'data-rlta-alias="' . $id . '"'));
    }
}
