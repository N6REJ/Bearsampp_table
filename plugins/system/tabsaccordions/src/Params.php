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
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\Uri as RL_Uri;

class Params
{
    protected static $params  = null;
    protected static $regexes = null;

    public static function get()
    {
        if ( ! is_null(self::$params))
        {
            return self::$params;
        }

        $params = RL_Parameters::getPlugin('tabsaccordions');

        $params->tag_tabs_open        = RL_PluginTag::clean($params->tag_tabs_open);
        $params->tag_tabs_close       = RL_PluginTag::clean($params->tag_tabs_close);
        $params->tag_accordions_open  = RL_PluginTag::clean($params->tag_accordions_open);
        $params->tag_accordions_close = RL_PluginTag::clean($params->tag_accordions_close);

        $params->use_responsive_view = false;

        self::$params = $params;

        return self::$params;
    }

    public static function getAlignment()
    {
        $params = self::get();


        if ( ! $params->alignment)
        {
            $params->alignment = JFactory::getLanguage()->isRTL() ? 'right' : 'left';
        }

        return 'align_' . $params->alignment;
    }

    public static function getRegex($type = 'tag')
    {
        $regexes = self::getRegexes();

        return $regexes->{$type} ?? $regexes->tag;
    }

    public static function getTagCharacters()
    {
        $params = self::get();

        if ( ! isset($params->tag_character_start))
        {
            self::setTagCharacters();
        }

        return [$params->tag_character_start, $params->tag_character_end];
    }

    public static function getTags($only_start_tags = false)
    {
        $params = self::get();

        [$tag_start, $tag_end] = self::getTagCharacters();

        $tags = [
            [
                $tag_start . $params->tag_tabs_open,
                $tag_start . $params->tag_accordions_open,
            ],
            [
                $tag_start . '/' . $params->tag_tabs_close . $tag_end,
                $tag_start . '/' . $params->tag_accordions_close . $tag_end,
            ],
        ];

        return $only_start_tags ? $tags[0] : $tags;
    }

    public static function setTagCharacters()
    {
        $params = self::get();

        [self::$params->tag_character_start, self::$params->tag_character_end] = explode('.', $params->tag_characters);
    }

    private static function getRegexEnd()
    {
        $params = self::get();

        [$tag_start, $tag_end] = self::getTagCharacters();

        $pre       = RL_PluginTag::getRegexSurroundingTagsPre();
        $post      = RL_PluginTag::getRegexSurroundingTagsPost();
        $tag_start = RL_RegEx::quote($tag_start);
        $tag_end   = RL_RegEx::quote($tag_end);

        $set_id = '(?:-[a-zA-Z0-9-_]+)?';

        return '(?<pre>' . $pre . ')'
            . $tag_start . '/(?<type>' . $params->tag_tabs_close . '|' . $params->tag_accordions_close . ')' . $set_id . $tag_end
            . '(?<post>' . $post . ')';
    }

    private static function getRegexOpenByType($type = 'tabs')
    {
        $params = self::get();

        [$tag_start, $tag_end] = self::getTagCharacters();

        $pre        = RL_PluginTag::getRegexSurroundingTagsPre();
        $post       = RL_PluginTag::getRegexSurroundingTagsPost();
        $inside_tag = RL_PluginTag::getRegexInsideTag($tag_start, $tag_end);
        $tag_start  = RL_RegEx::quote($tag_start);
        $tag_end    = RL_RegEx::quote($tag_end);
        $space      = RL_PluginTag::getRegexSpaces();
        $set_id     = '(?:-[a-zA-Z0-9-_]+)?';

        $open_tag  = $type == 'accordions' ? $params->tag_accordions_open : $params->tag_tabs_open;
        $close_tag = $type == 'accordions' ? $params->tag_accordions_close : $params->tag_tabs_close;

        return '(?<pre>' . $pre . ')'
            . $tag_start . '(?<tag>'
            . '(?<type>' . $open_tag . ')s?' . '(?<set_id>' . $set_id . ')' . $space . '(?<data>' . $inside_tag . ')'
            . '|/(?<type_close>' . $close_tag . ')' . $set_id
            . ')' . $tag_end
            . '(?<post>' . $post . ')';
    }

    private static function getRegexes()
    {
        if ( ! is_null(self::$regexes))
        {
            return self::$regexes;
        }

        self::$regexes = (object) [
            'tabs'       => self::getRegexOpenByType('tabs'),
            'accordions' => self::getRegexOpenByType('accordions'),
            'end'        => self::getRegexEnd(),
        ];

        return self::$regexes;
    }
}
