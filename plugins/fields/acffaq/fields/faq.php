<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\SubformField;

class JFormFieldFAQ extends SubformField
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
        
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<form>
    <field
        name="value"
        type="subform"
        hiddenLabel="true"
        multiple="true"
        layout="joomla.form.field.subform.repeatable-table"
        formsource="/plugins/fields/acffaq/fields/value.xml"
		default='{"faq":{}}'
    />
</form>
XML;
        $this->formsource = $xml;

		return true;
	}

    public function getInput()
    {
        return '<div class="tf-subform-hide-label">' . parent::getInput() . '</div>';
    }
}
