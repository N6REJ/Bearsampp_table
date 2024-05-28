<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class VideocustomfieldtypesField extends ListField
{
	public $type = 'Videocustomfieldtypes';

	protected function getOptions()
	{
		$options = array();

		// test the fields folder first to avoid message warning that the component is missing
		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

			$field_types = FieldsHelper::getFieldTypes();

			$standard_types = array('calendar', 'checkboxes', 'color', 'editor', 'imagelist', 'integer', 'list', 'media', 'radio', 'subform', 'sql', 'text', 'textarea', 'user', 'usergrouplist'); 

 			$additional_types = array();
			if (PluginHelper::isEnabled('fields', 'sywicon')) {
				$additional_types[] = 'sywicon';
			}
			$supported_additional_types = array(); // to be supported by detailselect

			$avoid_types = array_merge($additional_types, $standard_types);

			foreach ($field_types as $field_type) {
				if (!in_array($field_type['type'], $avoid_types)) {
					$options[] = HTMLHelper::_('select.option', $field_type['type'], $field_type['label'], 'value', 'text', $disable = false);
				}
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

}
?>