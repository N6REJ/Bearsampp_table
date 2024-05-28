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

namespace SmilePack;

defined('_JEXEC') or die('Restricted access');

use NRFramework\Functions;
use Joomla\CMS\HTML\HTMLHelper;

class Configuration
{
    protected $form;

    protected $data;
    
    public function __construct($form, $data)
    {
        $this->form = $form;
        $this->data = $data;
    }

    public function injectSettings()
    {
        $modules = scandir(JPATH_ADMINISTRATOR . '/components/com_smilepack/forms/modules');
        $modules = array_diff($modules, ['.', '..']);

        if (!$modules)
        {
            return;
        }
        
        foreach ($modules as $module)
        {
            $moduleXML = JPATH_ADMINISTRATOR . '/components/com_smilepack/forms/modules/' . $module;
            $this->form->load(file_get_contents($moduleXML), false);
        }
    }
}