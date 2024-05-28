<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nrframework/fields/nrtoggle.php';

use NRFramework\Extension;
use Joomla\CMS\Language\Text;

class JFormFieldFAQSchemaToggle extends JFormFieldNRToggle
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
    public function getInput()
    {
		// If GSD Pro is not installed and activated abort
		if (!Extension::isInstalled('gsd', 'plugin') || !Extension::isPro('plg_system_gsd'))
		{
            return '<div class="alert alert-warning">' . Text::_('ACF_FAQ_SCHEMA_GSD_MISSING') . '</div>';
		}
        
        return parent::getInput();
    }
}