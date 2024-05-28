<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class SPMapMarkers
{
    protected $params = null;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Get all markers.
     * 
     * @return  array
     */
    public function getAll()
    {
        $markers = [];

        // Manual Selection
        if ($this->params->get('source', 'custom') === 'custom')
        {
            $markers = $this->params->get('value');

            if (is_string($markers))
            {
                $markers = json_decode($markers, true);
            }
        }
        

        return $markers;
    }

    
}