<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Language\Text;

class JFormFieldACFPayPal extends TextField
{
    /**
	 * Renders the PayPal settings when viewing a specific item
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$required = (bool) $this->required;
		$requiredAtt = ($required) ? ' required="required"' : '';

		$value = is_string($this->value) ? json_decode($this->value) : $this->value;
		
		// get overriden values or fall back to default
		$itemName = isset($value->item_name) ? $value->item_name : '';
		$price = isset($value->price) ? $value->price : '';
		
		return '
			<div class="acf-paypal-item-settings">
				<div class="control-group">
					<div class="control-label">
						<label for="' . $this->id . '_item_name" class="hasPopover" data-content="' . Text::_('ACF_PAYPAL_ITEM_NAME_DESC') . '" data-original-title="' . Text::_('ACF_PAYPAL_ITEM_NAME') . '">' . Text::_('ACF_PAYPAL_ITEM_NAME') . '</label>
					</div>
					<div class="controls">
						<input id="' . $this->id . '_item_name" type="text" name="' . $this->name . '[item_name]" value="' . $itemName . '" placeholder="' . Text::_('ACF_PAYPAL_ITEM_NAME_HINT') . '" class="form-control input-xlarge w-100"' . $requiredAtt . ($this->disabled ? ' disabled' : '') . ' />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="' . $this->id . '_price" class="hasPopover" data-content="' . Text::_('ACF_PAYPAL_PRICE_DESC') . '" data-original-title="' . Text::_('ACF_PAYPAL_PRICE') . '">' . Text::_('ACF_PAYPAL_PRICE') . '</label>
					</div>
					<div class="controls">
						<input id="' . $this->id . '_price" type="text" name="' . $this->name . '[price]" value="' . $price . '" placeholder="' . Text::_('ACF_PAYPAL_PRICE_HINT') . '" class="form-control input-xlarge w-100"' . $requiredAtt . ($this->disabled ? ' disabled' : '') . ' />
					</div>
				</div>
			</div>
		';
	}
}
