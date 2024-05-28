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

use Joomla\CMS\Factory;
use NRFramework\Cache;

class Modules
{
    /**
     * Get total modules by name.
     * 
     * @param   string  $name  Module name
     * 
     * @return  int
     */
    public static function getTotalModulesByName($name = '')
    {
        if (!$name)
        {
            return 0;
        }

        $cache_key = 'getTotalModulesByName_' . $name;

        if (Cache::has($cache_key))
        {
            return Cache::get($cache_key);
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote($name));

        $db->setQuery($query);
        $totalModules = (int) $db->loadResult();

        Cache::set($cache_key, $totalModules);

        return $totalModules;
    }

    /**
     * Get module status.
     * 
     * @param   string  $name  Module name
     * 
     * @return  string
     */
    public static function getModuleStatus($name = '')
    {
        if (!$name)
        {
            return '';
        }

        $cache_key = 'getModuleStatus_' . $name;

        if (Cache::has($cache_key))
        {
            return Cache::get($cache_key);
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('enabled')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($name));

        $db->setQuery($query);

        $result = $db->loadResult();

        $status = null;

        if (is_scalar($result))
        {
            switch ($result)
            {
                case 1:
                    $status = 'published';
                    break;
                
                case 0:
                    $status = 'unpublished';
                    break;
            }
        }

        Cache::set($cache_key, $status);

        return $status;
    }

    /**
     * Update module status.
     * 
     * @param   string  $module  Module name
     * @param   int     $status  Module status
     * 
     * @return  bool
     */
    public static function updateModuleStatus($module = '', $status = 0)
    {
        if (!$module)
        {
            return false;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = ' . (int) $status)
            ->where($db->quoteName('element') . ' = ' . $db->quote($module));

        $db->setQuery($query);

        return $db->execute();
    }
}