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

class JFormFieldACFAddress extends TextField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		// Setup properties
		$zoom     	  = $this->element['zoom'] ? (int) $this->element['zoom'] : 4;
		$marker_image = !empty($this->element['marker_image']) ? (string) $this->element['marker_image'] : null;
		$value 		  = is_string($this->value) ? (json_decode($this->value) ? json_decode($this->value, true) : null) : json_decode(json_encode($this->value), true);
		$address	  = isset($value['address']) ? $value['address'] : [];
		$show_map	  = (string) $this->element['show_map'];
		$autocomplete = (string) $this->element['autocomplete'];
		$coordinates  = '';

		if (isset($address['latitude']) && !empty($address['latitude']) && isset($address['longitude']) && !empty($address['longitude']))
		{
			$coordinates = $address['latitude'] . ',' . $address['longitude'];
		}

		$payload = [
			'width' => 600,
			'height' => 400,
			'required' => $this->required,
			'name' => $this->name,
			'id' => $this->id,
			'zoom' => $zoom,
			'markerImage' => $marker_image,
			'show_map' => $show_map === '0' ? false : $show_map,
			'autocomplete' => $autocomplete === '1',
			'value' => $coordinates,
			'address' => $address
		];

		$showAddressDetails = isset($this->element['address_details_info']) ? (string) $this->element['address_details_info'] : false;
		if ($showAddressDetails)
		{
			// Make it an array
			$showAddressDetails = array_map('trim', explode(',', $showAddressDetails));
			// Set all values of each array key to true
			$showAddressDetails = array_fill_keys($showAddressDetails, true);
			$payload['_showAddressDetails'] = $showAddressDetails;
		}

		return \NRFramework\Widgets\Helper::render('MapAddressEditor', $payload);
	}
}