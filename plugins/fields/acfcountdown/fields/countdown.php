<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\Form;

class JFormFieldCountdown extends FormField
{
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.6
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		if (!parent::setup($element, $value, $group))
		{
			return false;
        }
        
        // Value must be an array
        if (is_string($this->value) && $value = json_decode($this->value, true))
        {
            $this->value = json_decode($this->value, true);
        }
        
		return true;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
    protected function getInput()
    {
        $countdown_type = (string) $this->element['countdown_type'];

        $static_fields = '
            <field name="value" type="calendar"
                hiddenLabel="true"
                format="%Y-%m-%d %H:%M:%S"
                filter="none"
                translateformat="true"
                showtime="true"
                required="' . $this->required . '"
            />';

        $evergreen_fields = '
			<field name="acf_countdown_inline_start" hiddenLabel="true" type="nr_inline" />
            <field name="dynamic_days" type="nrnumber"
                class="input-small"
                hiddenLabel="true"
                addon="ACF_COUNTDOWN_DAYS"
                default="0"
                min="0"
                max="365"
                required="' . $this->required . '"
            />
            <field name="dynamic_hours" type="nrnumber"
                class="input-small"
                hiddenLabel="true"
                addon="ACF_COUNTDOWN_HOURS"
                default="0"
                min="0"
                max="999"
                required="' . $this->required . '"
            />
            <field name="dynamic_minutes" type="nrnumber"
                class="input-small"
                hiddenLabel="true"
                addon="ACF_COUNTDOWN_MINUTES"
                default="0"
                min="0"
                max="999"
                required="' . $this->required . '"
            />
            <field name="dynamic_seconds" type="nrnumber"
                class="input-small"
                hiddenLabel="true"
                addon="ACF_COUNTDOWN_SECONDS"
                default="0"
                min="0"
                max="999"
                required="' . $this->required . '"
            />
			<field name="acf_countdown_inline_end" end="1" type="nr_inline" />
        ';
        
        $form_source = new SimpleXMLElement('
            <form>
                <fieldset name="acfcountdown">
                    ' . ($countdown_type === 'static' ? $static_fields : $evergreen_fields) . '
                </fieldset>
            </form>
        ');

        $control  = $this->name;
        $formname = 'acfcountdown.' . str_replace(['jform[', '[', ']'], ['', '.', ''], $control);

        $form = Form::getInstance($formname, $form_source->asXML(), ['control' => $control]);
        $form->bind($this->value);

        return '<div class="acf-countdown-item-edit-wrapper">' . $form->renderFieldset('acfcountdown') . '</div>';
    }
}