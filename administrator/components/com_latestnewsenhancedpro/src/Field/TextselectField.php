<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use SYW\Library\K2 as SYWK2;

class TextselectField extends GroupedlistField
{
	public $type = 'Textselect';

	protected $option;
	protected $syw_useglobal;

	static $core_fields = null;
	static $k2_fields = null;

	static function getCoreFields($allowed_types = array())
	{
		if (!isset(self::$core_fields)) {
			$fields = FieldsHelper::getFields('com_content.article');

			self::$core_fields = array();

			if (!empty($fields)) {
				foreach ($fields as $field) {
					if (!empty($allowed_types) && !in_array($field->type, $allowed_types)) {
						continue;
					}
					self::$core_fields[] = $field;
				}
			}
		}

		return self::$core_fields;
	}

	static function getK2Fields($allowed_types = array())
	{
		if (!isset(self::$k2_fields)) {
			self::$k2_fields = SYWK2::getK2Fields($allowed_types);
		}

		return self::$k2_fields;
	}

	protected function getGroups()
	{
		$groups = array();

		$group_name = '-';
		$groups[$group_name] = array();

	    $groups[$group_name][] = HTMLHelper::_('select.option', 'intro', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_INTROTEXT'), 'value', 'text', $disable = false);
	    $groups[$group_name][] = HTMLHelper::_('select.option', 'intrometa', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_INTROMETADESC'), 'value', 'text', $disable = false);
	    $groups[$group_name][] = HTMLHelper::_('select.option', 'meta', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_METADESC'), 'value', 'text', $disable = false);
	    $groups[$group_name][] = HTMLHelper::_('select.option', 'metaintro', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_METADESCINTRO'), 'value', 'text', $disable = false);

		if ($this->option == 'com_k2' && SYWK2::exists()) {

			// get K2 extra fields
			$fields = self::getK2Fields(array('textarea'));

			$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2EXTRAFIELDS');
			$groups[$group_name] = array();

			foreach ($fields as $field) {
				$groups[$group_name][] = HTMLHelper::_('select.option', 'k2field:'.$field->type.':'.$field->id, 'K2: '.$field->group_name.': '.$field->name, 'value', 'text', $disable = false);
			}
		}

		// get Joomla! fields
		// test the fields folder first to avoid message warning that the component is missing
		if ($this->option == 'com_content' && Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

			// get the custom fields
			$fields = self::getCoreFields(array('textarea', 'editor'));

			// organize the fields according to their group

			$fieldsPerGroup = array(
				0 => array()
			);

			$groupTitles = array(
				0 => Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_NOGROUPFIELD')
			);

			$fields_exist = false;
			foreach ($fields as $field) {

				if (!array_key_exists($field->group_id, $fieldsPerGroup)) {
					$fieldsPerGroup[$field->group_id] = array();
					$groupTitles[$field->group_id] = $field->group_title;
				}

				$fieldsPerGroup[$field->group_id][] = $field;
				$fields_exist = true;
			}

			// loop trough the groups

			if ($fields_exist) {

				$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_JOOMLAFIELDS');
				$groups[$group_name] = array();

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_JOOMLAFIELDS'));

				foreach ($fieldsPerGroup as $group_id => $groupFields) {

					if (!$groupFields) {
						continue;
					}

					foreach ($groupFields as $field) {
						$groups[$group_name][] = HTMLHelper::_('select.option', 'jfield:'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', $disable = false);
					}
				}

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_JOOMLAFIELDS'));
			}
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		// impossible to use the code in FormFieldList because of the option groups
		if ($this->syw_useglobal) {
			$global_text = Text::_('JGLOBAL_USE_GLOBAL');
		    $component = Factory::getApplication()->input->getCmd('option');

		    // Get correct component for menu items
		    if ($component == 'com_menus') {
		        $link      = $this->form->getData()->get('link');
		        $uri       = new Uri($link);
		        $component = $uri->getVar('option', 'com_menus');
		    }

		    $params = ComponentHelper::getParams($component);
		    $value  = $params->get($this->fieldname);

		    // Try with global configuration
		    if (is_null($value)) {
		        $value = Factory::getConfig()->get($this->fieldname);
		    }

		    // Try with menu configuration
		    if (is_null($value) && Factory::getApplication()->input->getCmd('option') == 'com_menus') {
		        $value = ComponentHelper::getParams('com_menus')->get($this->fieldname);
		    }

		    if (!is_null($value)) {
		        $value = (string) $value;

		        foreach ($groups as $group) {
		        	foreach ($group as $option) {
		        		if (isset($option->value) && $option->value === $value) {
		        			$value = $option->text;
		        			break;
		        		}
		        	}
		        }

		        $global_text = Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
		    }

		    array_unshift($groups, array(HTMLHelper::_('select.option', '', $global_text, 'value', 'text', $disable = false)));
		}

		return $groups;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
	    $return = parent::setup($element, $value, $group);

	    if ($return) {
	        $this->option = isset($this->element['option']) ? $this->element['option'] : '';
	        $this->syw_useglobal = isset($this->element['useglobal']) ? $this->element['useglobal'] : false;
	    }

	    return $return;
	}
}
?>