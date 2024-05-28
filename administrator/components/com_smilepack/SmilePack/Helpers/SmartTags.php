<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace SmilePack\Helpers;

defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

class SmartTags
{
    /**
     * Replace Smart Tags in the buffer.
     * 
     * @param   string  $buffer  The buffer to replace Smart Tags in.
     * 
     * @return  void
     */
    public static function doSmartTagReplacements(&$buffer)
    {
        // Check whether the plugin should process or not
        if (StringHelper::strpos($buffer, '{sp') === false)
        {
            return true;
        }

        $smartTags = \SmilePack\SmartTags::getInstance();

        $buffer = $smartTags->replace($buffer);
    }
}