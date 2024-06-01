<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

namespace Joomla\Plugin\Fields\Subform\Extension;

use JFormHelper;
use Joomla\CMS\Form\Form;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;
use Joomla\CMS\Form\Field\SubformField;

// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('subform');
use SimpleXMLElement;

class JFormFieldBerversions extends SubformField
{
  public $type = 'Berversions';

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
        formsource="/plugins/fields/berversions/fields/row.xml"
        default=""
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
