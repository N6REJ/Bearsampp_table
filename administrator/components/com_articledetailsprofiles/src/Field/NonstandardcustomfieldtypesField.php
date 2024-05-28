<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\ArticleDetailsProfiles\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class NonstandardcustomfieldtypesField extends GroupedlistField
{
	public $type = 'Nonstandardcustomfieldtypes';

	protected function getGroups()
	{
		$groups = array();

		// test the fields folder first to avoid message warning that the component is missing
		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

			$field_types = FieldsHelper::getFieldTypes();

			$standard_types = array('calendar', 'checkboxes', 'color', 'editor', 'imagelist', 'integer', 'list', 'media', 'radio', 'sql', 'subform', 'text', 'textarea', 'url', 'user', 'usergrouplist');
			$supported_standard_types = array('calendar', 'checkboxes', 'editor', 'integer', 'list', 'radio', 'sql', 'text', 'textarea', 'url'); // to be supported by detailselect

//  			$additional_types = array();
// 			if (PluginHelper::isEnabled('fields', 'sywicon')) {
// 				$additional_types[] = 'sywicon';
// 			}
 			$supported_additional_types = array(); // to be supported by detailselect

// 			$avoid_types = array_merge($additional_types, $standard_types);

			$avoid_types = $standard_types;

			$group_name = Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_STANDARD');
			$groups[$group_name] = array();

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_STANDARD'));

			foreach ($field_types as $field_type) {
				if (in_array($field_type['type'], $supported_standard_types)) {
					$groups[$group_name][] = HTMLHelper::_('select.option', $field_type['type'], $field_type['label'], 'value', 'text', $disable = true);
				}
			}

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_STANDARD'));

			$group_name = Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_NONSTANDARD');
			$groups[$group_name] = array();

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_NONSTANDARD'));

			foreach ($field_types as $field_type) {
				if (in_array($field_type['type'], $supported_additional_types)) {
					$groups[$group_name][] = HTMLHelper::_('select.option', $field_type['type'], $field_type['label'], 'value', 'text', $disable = true);
				}
			}

			foreach ($field_types as $field_type) {
				if (!in_array($field_type['type'], $avoid_types)) {
					$groups[$group_name][] = HTMLHelper::_('select.option', $field_type['type'], $field_type['label'], 'value', 'text', $disable = false);
				}
			}

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_ARTICLEDETAILSPROFILES_VALUE_NONSTANDARD'));
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
?>