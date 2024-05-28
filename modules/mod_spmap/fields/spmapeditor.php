<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Language\Text;

class JFormFieldSPMapEditor extends TextField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		$moduleParams = $this->form->getValue('params');
		
		$value = is_string($this->value) && !empty($this->value) ? (json_decode($this->value) ? json_decode($this->value, true) : []) : [];

		$show_sidebar = isset($moduleParams->show_sidebar) ? (string) $moduleParams->show_sidebar === '1' : true;

		$payload = [
			'readonly' => $this->readonly,
			'disabled' => $this->disabled,
			'name' => $this->name,
			'width' => 700,
			'required' => $this->required,
			'id' => $this->id,
			'showSidebar' => $show_sidebar,
			
			'extension' => 'mod_spmap',
			'scale' => false,
			'maxMarkers' => 1,
			
			
		];

		

		
		\NRFramework\HTML::renderProOnlyModal();
		

		$default_coords = isset($moduleParams->default_coords) ? (string) $moduleParams->default_coords : [];
		if ($default_coords)
		{
			$default_coords = array_map('trim', explode(',', $default_coords));
			if (count($default_coords) === 2)
			{
				$payload['lat'] = $default_coords[0];
				$payload['long'] = $default_coords[1];
			}
		}

		if ($value)
		{
			$payload['value'] = $value;
		}

		return \NRFramework\Widgets\Helper::render('MapEditor', $payload);
	}
}