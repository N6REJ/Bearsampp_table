<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use NRFramework\Extension;
use Joomla\Registry\Registry;
use Joomla\Filesystem\Path;
use Joomla\CMS\Factory;

class ACFMigrator
{
    /**
     * Application object.
     * 
     * @var  object
     */
    private $app;

    /**
     * Database object.
     * 
     * @var  object
     */
    private $db;

    /**
     * Database object.
     * 
     * @var  object
     */
    private $dbDestination;

    /**
     * The currently installed version.
     * 
     * @var  string
     */
    private $installedVersion;
    
    /**
     * Class constructor
     *
     * @param string $installedVersion  The current extension version
     */
    public function __construct($installedVersion, $dbSource = null, $dbDestination = null)
    {
        $this->app = Factory::getApplication();
        $this->db = $dbSource ? $dbSource : Factory::getDbo();
        $this->dbDestination = $dbDestination ? $dbDestination : Factory::getDbo();
        $this->installedVersion = $installedVersion;
    }

    public function do()
    {
        if (version_compare($this->installedVersion, '1.3.0', '<='))
        {
            $this->uploadFieldConvertBasenameToRelativePaths();
        }
        
        
        if (version_compare($this->installedVersion, '2.1', '<='))
        {
            $this->migratePublishingRulesToConditionBuilder();
        }
        

        if (version_compare($this->installedVersion, '2.7.2', '<='))
        {
            $this->updateMapMaximumMarkers();
        }

        if (version_compare($this->installedVersion, '2.7.3', '<='))
        {
            
            $this->updateAddressDefaultMarkerImage();
            
            $this->updateMapValue();
        }
    }

    
    /**
     * ACF Address used the default marker image from the ACF OSM custom field.
     * 
     * Old path: media/plg_fields_acfosm/img/marker.png
     * 
     * However, since the ACF OSM map is now removed from the build, we must update it
     * to point to the same file but from the ACF Address custom field.
     * 
     * New value: media/plg_fields_acfaddress/img/marker.png
     */
    public function updateAddressDefaultMarkerImage()
    {
        // Get all custom fields.
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__fields'))
            ->where($db->quoteName('type') . ' = ' . $db->q('acfaddress'));

        $db->setQuery($query);

        if (!$fields = $db->loadObjectList())
        {
            return;
        }

        foreach ($fields as $key => $field)
        {
            if (!isset($field->fieldparams))
            {
                continue;
            }

            $params = json_decode($field->fieldparams);

            $markerImage = $params->marker_image;

            if ($markerImage !== 'media/plg_fields_acfosm/img/marker.png')
            {
                continue;
            }

            $params->marker_image = 'media/plg_fields_acfaddress/img/marker.png';

            // Update field `param` with new value
            $dbDestination = $this->dbDestination;

            $query = $dbDestination->getQuery(true)
                ->update($dbDestination->quoteName('#__fields'))
                ->set($dbDestination->quoteName('fieldparams') . '=' . $dbDestination->quote(json_encode($params)))
                ->where($dbDestination->quoteName('id') . '=' . $dbDestination->quote($field->id));

            $dbDestination->setQuery($query);
            $dbDestination->execute();
        }
    }
    

    public function updateMapValue()
    {
        // Get all custom fields.
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__fields'))
            ->where($db->quoteName('type') . ' = ' . $db->q('acfmap'));

        $db->setQuery($query);

        if (!$fields = $db->loadObjectList())
        {
            return;
        }

        $count = 0;

        foreach ($fields as $key => $field)
        {
            // Find now all field values
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__fields_values'))
                ->where($db->quoteName('field_id') . ' = ' . $field->id);

            $db->setQuery($query);

            if (!$values = $db->loadObjectList())
            {
                return;
            }

            foreach ($values as $value)
            {
                if (!$decoded_value = json_decode($value->value, true))
                {
                    continue;
                }

                if (!isset($decoded_value['markers']))
                {
                    continue;
                }

                $newValue = $decoded_value['markers'];

                $query = $db->getQuery(true);
                
                $fields = [
                    $db->quoteName('value') . ' = ' . $db->q($newValue)
                ];
                
                $conditions = [
                    $db->quoteName('field_id') . ' = ' . $value->field_id, 
                    $db->quoteName('item_id') . ' = ' . $db->quote($value->item_id),
                    $db->quoteName('value') . ' = ' . $db->quote($value->value)
                ];
                
                $query->update($db->quoteName('#__fields_values'))->set($fields)->where($conditions);
                
                $db->setQuery($query);
                $db->execute();

                $count++;
            }
        }

        return $count;
    }

    public function updateMapMaximumMarkers()
    {
        // Get all custom fields.
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__fields'))
            ->where($db->quoteName('type') . ' = ' . $db->q('acfmap'));

        $db->setQuery($query);

        if (!$fields = $db->loadObjectList())
        {
            return;
        }

        foreach ($fields as $key => $field)
        {
            if (!isset($field->fieldparams))
            {
                continue;
            }

            $params = json_decode($field->fieldparams);

            if (isset($params->maximum_markers))
            {
                continue;
            }

            $params->maximum_markers = 0;

            // Update field `param` with new value
            $dbDestination = $this->dbDestination;

            $query = $dbDestination->getQuery(true)
                ->update($dbDestination->quoteName('#__fields'))
                ->set($dbDestination->quoteName('fieldparams') . '=' . $dbDestination->quote(json_encode($params)))
                ->where($dbDestination->quoteName('id') . '=' . $dbDestination->quote($field->id));

            $dbDestination->setQuery($query);
            $dbDestination->execute();
        }
    }

    
    public function migratePublishingRulesToConditionBuilder()
    {
        // Get all custom fields.
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__fields'));

        $db->setQuery($query);

        if (!$fields = $db->loadObjectList())
        {
            return;
        }

        foreach ($fields as $key => $field)
        {
            if (!isset($field->params))
            {
                continue;
            }

            $params = new Registry($field->params);

            \NRFramework\Conditions\Migrator::run($params);

            if (!isset($params['rules']) || empty($params['rules']))
            {
                continue;
            }

            // Update field `param` with new value
            $dbDestination = $this->dbDestination;

            $query = $dbDestination->getQuery(true)
                ->update($dbDestination->quoteName('#__fields'))
                ->set($dbDestination->quoteName('params') . '=' . $dbDestination->quote(json_encode($params)))
                ->where($dbDestination->quoteName('id') . '=' . $dbDestination->quote($field->id));

            $dbDestination->setQuery($query);
            $dbDestination->execute();
        }
    }
    

    /**
     * Since v1.3.0, the File Upload field is no longer storing just the file's name in the database but the full relative path to Joomla's root.
     * 
     * This migration tasks, loops through all file values in the database and prepends to them the Upload Folder as configured in the Field settings.
     * 
     * Previous value: myAwesomePhoto.jpg
     * New value: media/myimages/myAwesomePhoto.jpg
     *
     * @return mixed    Null when the migrationt ask doesn't run, Integer when the migration run.
     */
    public function uploadFieldConvertBasenameToRelativePaths()
    {
        // Get all upload fields.
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__fields'))
            ->where($db->quoteName('type') . ' = ' . $db->q('acfupload'));

        $db->setQuery($query);

        if (!$fields = $db->loadObjectList())
        {
            return;
        }

        $count = 0;

        foreach ($fields as $key => $field)
        {
            if (!isset($field->fieldparams))
            {
                continue;
            }

            $params = json_decode($field->fieldparams);

            if (!isset($params->upload_folder))
            {
                continue;
            }

            $upload_folder = $params->upload_folder;

            // Find now all field values
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__fields_values'))
                ->where($db->quoteName('field_id') . ' = ' . $field->id);

            $db->setQuery($query);

            if (!$values = $db->loadObjectList())
            {
                return;
            }

            foreach ($values as $value)
            {
                if (!isset($value->value) || empty($value->value))
                {
                    continue;
                }

                // If the path has a slash, consider it as already fixed.
                if (strpos($value->value, DIRECTORY_SEPARATOR) !== false)
                {
                    continue;
                }

                $newValue = trim(Path::clean($upload_folder . '/' . $value->value));

                $query = $db->getQuery(true);
                
                $fields = [
                    $db->quoteName('value') . ' = ' . $db->q($newValue)
                ];
                
                $conditions = [
                    $db->quoteName('field_id') . ' = ' . $value->field_id, 
                    $db->quoteName('item_id') . ' = ' . $db->quote($value->item_id),
                    $db->quoteName('value') . ' = ' . $db->quote($value->value)
                ];
                
                $query->update($db->quoteName('#__fields_values'))->set($fields)->where($conditions);
                
                $db->setQuery($query);
                $db->execute();

                $count++;
            }
        }

        return $count;
    }
}
