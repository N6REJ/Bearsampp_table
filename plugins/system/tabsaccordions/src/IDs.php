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

use RegularLabs\Library\Alias as RL_Alias;

class IDs
{
    private static array $ids = [];

    public static function create($item, $prefix = '')
    {
        $alias = RL_Alias::get($item->alias ?? $item->name);
        $alias = $alias ?: 'tab';

        $id = $prefix . $alias;

        $i = 1;

        while (in_array($id, self::$ids))
        {
            $id = $prefix . $alias . '-' . ++$i;
        }

        self::$ids[] = $id;

        return $id;
    }
}
