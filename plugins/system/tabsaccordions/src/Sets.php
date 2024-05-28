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
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\HtmlTag as RL_HtmlTag;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\ObjectHelper as RL_Object;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper;
use RegularLabs\Library\Title as RL_Title;

/**
 * Class Output
 *
 * @package RegularLabs\Plugin\System\TabsAccordions
 */
class Sets
{
    private array $colors        = ['red', 'orange', 'yellow', 'green', 'teal', 'blue', 'indigo', 'purple', 'pink'];
    private array $matches       = [];
    private array $random_colors = [];
    private int   $set_count     = 0;
    private array $sets          = [];
    private       $user_groups   = null;

    public function __construct()
    {
        $this->user_groups = $this->getUserGroups();
    }

    public function get(&$string)
    {
        $matches = $this->getMatches($string);

        if (empty($matches))
        {
            return [];
        }

        $this->sets = [];
        $set_ids    = [];


        foreach ($matches as $match)
        {
            if (str_starts_with($match['tag'], '/'))
            {
                if (empty($set_ids))
                {
                    continue;
                }

                $set_id = key($set_ids);

                array_pop($set_ids);

                if (empty($set_id))
                {
                    continue;
                }

                $this->sets[$set_id]->items[0]->ending = $match[0];

                continue;
            }

            end($set_ids);

            $item = $this->getItem($match, $set_ids);


            if ( ! isset($this->sets[$item->set]))
            {
                $this->sets[$item->set] = $this->getSet($item);
            }

            $this->sets[$item->set]->items[] = $item;
        }


        $this->setData();

        return $this->sets;
    }

    private function addChildToParent($item)
    {
        if (empty($item->parent))
        {
            return;
        }

        [$parent_set, $parent_item] = $item->parent;

        if (empty($this->sets[$parent_set]) || empty($this->sets[$parent_set]->items[$parent_item]))
        {
            return;
        }

        $this->sets[$parent_set]->items[$parent_item]->children[] = $item->set;
    }

    private function getAccessLevels()
    {
    }

    private function getItem($match, &$set_ids)
    {
        $params = Params::get();

        $item = (object) [];

        // Set the values from the tag
        $tag = RL_Title::clean($match['data'], false, false);
        $this->setTagAttributes($item, $tag);
        $item->original_match = $match[0];

        $item->type = $match['type'] == $params->tag_accordions_open ? 'accordions' : 'tabs';

        $item->set_id = trim(str_replace('-', '_', $match['set_id']));
        $item->data   = (object) [];

        // New set
        if (empty($set_ids) || current($set_ids) != $item->set_id)
        {
            $this->set_count++;
            $set_id = $this->set_count . $item->set_id;

            $set_ids[$set_id] = $item->set_id;
        }

        $item->set = array_search($item->set_id, array_reverse($set_ids, true));

        $item->level = $this->getSetLevel($item->set, $set_ids);


        [$item->pre, $item->post] = RL_Html::cleanSurroundingTags(
            [$match['pre'], $match['post']],
            ['div', 'p', 'span', 'h[0-6]']
        );

        return $item;
    }

    private function getMatches($string)
    {
        $regex_end = Params::getRegex('end');

        if ( ! RL_RegEx::match($regex_end, $string))
        {
            return [];
        }

        $regex_tabs = Params::getRegex('tabs');
        RL_RegEx::matchAll($regex_tabs, $string, $matches_tabs);

        $regex_accordions = Params::getRegex('accordions');
        RL_RegEx::matchAll($regex_accordions, $string, $matches_accordions);

        return [...$matches_tabs, ...$matches_accordions];
    }

    private function getParent($set_id, $level)
    {
        if (empty($this->sets))
        {
            return false;
        }

        if (isset($this->sets[$set_id]) && ! empty($this->sets[$set_id]->items))
        {
            return $this->sets[$set_id]->items[0]->parent;
        }

        reset($this->sets);

        $previous_set = current($this->sets);
        $prev_level   = $previous_set->items[0]->level;

        while ($prev_level >= $level)
        {
            $previous_set = prev($this->sets);

            if (empty($previous_set))
            {
                end($this->sets);

                return false;
            }

            $prev_level = $previous_set->items[0]->level;
        }

        end($this->sets);
        end($previous_set->items);

        $parent_item = key($previous_set->items);

        return [$previous_set->items[$parent_item]->set, $parent_item];
    }

    private function getRandomColorName($item)
    {
        if ( ! isset($this->random_colors[$item->set_id]))
        {
            // store colors in random order for this specific set
            $this->random_colors[$item->set_id] = array_values($this->colors);
            shuffle($this->random_colors[$item->set_id]);
        }

        // get the color matching the current item count (will wrap if number is higher than number of colors)
        return $this->random_colors[$item->set_id][$item->count % count($this->random_colors[$item->set_id])];
    }

    private function getSet($item)
    {
        $params = Params::get();

        $type = $item->type ?? 'tabs';

        $theme       = $item->{'theme'} ?? $params->theme;
        $theme       = in_array($theme, ['custom', 'neutral']) ? $theme : 'neutral';
        $positioning = 'top';
        $alignment   = $item->{'align'} ?? $params->alignment;

        $orientation = 'vertical';

        if ($type == 'tabs' && ($positioning == 'top' || $positioning == 'bottom'))
        {
            $orientation = 'horizontal';
        }

        return (object) [
            'items' => [],
            'data'  => (object) [
                'id'                     => $item->set,

                // Settings (needed for js)
                'type'                   => $type,
                'title-tag'              => $item->{'title-tag'} ?? $params->title_tag,
                'orientation'            => $orientation,
                'wrap-buttons'           => (bool) ($item->{'wrap-buttons'} ?? false),
                'remember-active'        => (bool) ($item->{'remember-active'} ?? false),
                'button-scroll-duration' => $item->{'button-scroll-duration'} ?? null,
                'switch-to-accordions'   => $item->{'switch'} ?? null,
                'switch-break-point'     => $item->{'break-point'} ?? null,

                // Styling (only used by css)
                'theme'                  => $this->getThemeName($item->{'theme'} ?? $params->theme),
                'color-panels'           => (bool) ($item->{'color-panels'} ?? $params->color_panels),
                'positioning'            => $positioning,
                'alignment'              => $alignment,
                'has-button-scroller'    => false,
            ],
        ];
    }

    private function getSetLevel($set_id, $set_ids)
    {
        // Sets are still empty, so this is the first set
        if (empty($this->sets))
        {
            return 1;
        }

        // Grab the level from the previous entry of this set
        if (isset($this->sets[$set_id]) && ! empty($this->sets[$set_id]->items))
        {
            return $this->sets[$set_id]->items[0]->level;
        }

        // Look up the level of the previous set
        $previous_set_id = array_search(prev($set_ids), array_reverse($set_ids));

        // Grab the level from the previous entry of this set
        if (isset($this->sets[$previous_set_id]) && ! empty($this->sets[$previous_set_id]->items))
        {
            return $this->sets[$previous_set_id]->items[0]->level + 1;
        }

        return 1;
    }

    private function getTagAttributes($string)
    {
        RL_PluginTag::protectSpecialChars($string);

        RL_PluginTag::unprotectSpecialChars($string, true);

        $known_boolean_keys = [
            'open', 'active', 'opened', 'default',
            'scroll', 'noscroll',
            'nooutline', 'outline_handles', 'outline_content', 'color_inactive_handles',
        ];

        // Get the values from the tag
        $attributes = RL_PluginTag::getAttributesFromString($string, 'title', $known_boolean_keys, '', []);

        $key_aliases = [
            'title'              => ['name'],
            'title-active'       => ['title-open', 'title-opened'],
            'title-inactive'     => ['title-close', 'title-closed'],
            'icons'              => ['icon'],
            'open'               => ['active', 'opened', 'default'],
            'access'             => ['accesslevels', 'accesslevel'],
            'usergroup'          => ['usergroups', 'group', 'groups'],
            'position'           => ['positioning'],
            'align'              => ['alignment'],
            'animations'         => ['effect', 'effects', 'animate', 'animation'],
            'scroll'             => ['scrolling'],
            'heading_attributes' => ['li_attributes'],
            'link_attributes'    => ['a_attributes'],
            'body_attributes'    => ['content_attributes'],
        ];

        return RL_Object::replaceKeys($attributes, $key_aliases);
    }

    private function getThemeName($string)
    {
        return StringHelper::toDashCase($string, true);
    }

    private function getUserGroups()
    {
    }

    private function hasAccess($item)
    {
    }

    private function hasAccessByList($levels, $list)
    {
    }

    private function itemIsOpen($item, $urlitem, $is_first = false)
    {
        if ($item->haslink)
        {
            return false;
        }

        if ( ! empty($item->close))
        {
            return false;
        }

        if (isset($item->open))
        {
            return $item->open;
        }

        if ($urlitem && in_array($urlitem, $item->matches))
        {
            return true;
        }

        if ($is_first)
        {
            return true;
        }

        return false;
    }

    private function removeByAccess(&$string)
    {
    }

    private function setData()
    {
        $params = Params::get();

        $urlitem   = RL_Input::get('tab', '');
        $itemcount = 0;

        foreach ($this->sets as $set_id => $set)
        {
            $opened_by_default = 0;


            foreach ($set->items as $i => $item)
            {
                $title = trim($item->title ?? ($item->type == 'accordions' ? 'Accordion' : 'Tab'));

                if (isset($item->{'title-active'}) || isset($item->{'title-inactive'}))
                {
                    $item->data->{'title-active'}   = $item->{'title-active'} ?? $title;
                    $item->data->{'title-inactive'} = $item->{'title-inactive'} ?? $title;

                    // Set main title (if not set) to the title-inactive, otherwise to title-active
                    $item->title = ($item->title ?: $item->{'title-inactive'}) ?: $item->{'title-active'};
                }

                $item->title = $item->title ?: $title;

                $item->haslink = RL_RegEx::match('<a [^>]*>.*?</a>', $item->title);

                $item->name = RL_Title::clean($item->title, true);
                $item->name = $item->name ?: RL_HtmlTag::getAttributeValue('title', $item->title);
                $item->name = $item->name ?: RL_HtmlTag::getAttributeValue('alt', $item->title);

                $item->id    = IDs::create($item);
                $item->set   = (int) $set_id;
                $item->count = $i + 1;

                $set_keys = [
                    'class', 'title_tag', 'onclick',
                ];

                foreach ($set_keys as $key)
                {
                    $item->{$key} ??= $params->{$key} ?? '';
                }

                $item->matches   = RL_Title::getUrlMatches([$item->id, $item->name]);
                $item->matches[] = ++$itemcount . '';
                $item->matches[] = $item->set . ($i + 1);

                $item->matches = array_unique($item->matches);
                $item->matches = array_diff($item->matches, $this->matches);
                $this->matches = [...$this->matches, ...$item->matches];

                if ($this->itemIsOpen($item, $urlitem, $i == 0))
                {
                    $opened_by_default = $i;
                }

                // Can be set to true after all items are checked based on the $opened_by_default id
                $item->open ??= null;


                if ( ! empty($item->color))
                {
                    $item->data->color = $item->color == 'random'
                        // get the color matching the current item id (will wrap if number is higher than number of colors)
                        ? $this->getRandomColorName($item)
                        : $item->color;
                }

                $this->sets[$set_id]->items[$i] = $item;
            }

            $this->setOpenItem($this->sets[$set_id]->items, $opened_by_default);
        }
    }

    private function setOpenItem(&$items, $opened_by_default = 0)
    {
        $opened_by_default = (int) $opened_by_default;

        while (isset($items[$opened_by_default]) && $items[$opened_by_default]->haslink)
        {
            $opened_by_default++;
        }

        if ( ! isset($items[$opened_by_default]))
        {
            return;
        }

        if ($items[$opened_by_default]->open === false)
        {
            return;
        }

        $items[$opened_by_default]->open = true;
    }

    private function setTagAttributes(&$item, $string)
    {
        $values = $this->getTagAttributes($string);
        $item   = (object) [...(array) $item, ...(array) $values];
    }
}
