<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Field;

defined( '_JEXEC' ) or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class ListwrapperglobalField extends ListField
{
	public $type = 'Listwrapperglobal';

	protected $fieldname_global;

	protected function getOptions()
	{
		$options = array();

		$options = array_merge($options, parent::getOptions());

		$params = ComponentHelper::getParams('com_articledetailsprofiles');
		$value  = $this->fieldname_global ? $params->get($this->fieldname_global) : $params->get($this->fieldname);
		$text = '';

		if (!is_null($value)) {
			$value = (string) $value;

			foreach ($options as $option) {
				if (isset($option->value) && $option->value === $value) {
					$text = Text::_($option->text);
					break;
				}
			}
		}

		$global_option = HTMLHelper::_('select.option', '', $text ? Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $text) : Text::_('JGLOBAL_USE_GLOBAL'), 'value', 'text', $disable = false);

		array_unshift($options, $global_option);

		return $options;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->fieldname_global = isset($this->element['nameglobal']) ? (string) $this->element['nameglobal'] : '';
		}

		return $return;
	}

}
?>