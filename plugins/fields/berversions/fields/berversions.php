<?php
defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('subform');

class JFormFieldBerversions extends JFormFieldSubform
{
  protected $type = 'Berversions';

  protected function getInput()
  {
    // Load the subform XML file
    $this->form->loadFile(__DIR__ . '/../params/berversions.xml', false);

    // Render the subform
    return parent::getInput();
  }
}
