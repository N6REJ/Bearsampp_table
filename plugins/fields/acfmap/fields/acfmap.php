<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;

class JFormFieldACFMap extends TextField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		$value = is_string($this->value) && !empty($this->value) ? (json_decode($this->value) ? json_decode($this->value, true) : []) : [];

		$show_sidebar = isset($this->element['show_sidebar']) ? (string) $this->element['show_sidebar'] === '1' : true;

		$payload = [
			'readonly' => $this->readonly,
			'disabled' => $this->disabled,
			'name' => $this->name,
			'width' => 700,
			'required' => $this->required,
			'id' => $this->id,
			'showSidebar' => $show_sidebar,
			
			
			'scale' => (string) $this->element['scale'] !== '0' ? (string) $this->element['scale'] : false,
			'pro' => true,
			'maxMarkers' => isset($this->element['maximum_markers']) ? (int) $this->element['maximum_markers'] : 0,
			'markerImage' => (string) $this->element['markerImage'],
			
		];

		

		$default_coords = (string) $this->element['default_coords'];
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