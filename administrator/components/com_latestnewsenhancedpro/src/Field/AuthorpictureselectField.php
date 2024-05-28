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
use Joomla\CMS\Language\Text;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class AuthorpictureselectField extends ListField
{
	public $type = 'Authorpictureselect';

	static $core_fields = array();

	static function getCoreFields($allowed_types = array(), $origin = 'com_content.article')
	{
		if (!isset(self::$core_fields[$origin])) {

			$fields = FieldsHelper::getFields($origin);

			self::$core_fields[$origin] = array();

			if (!empty($fields)) {
				foreach ($fields as $field) {
					if (!empty($allowed_types) && !in_array($field->type, $allowed_types)) {
						continue;
					}
					self::$core_fields[$origin][] = $field;
				}
			}
		}

		return self::$core_fields[$origin];
	}

	protected function getOptions()
	{
		$customfields = array();

		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields')) {
			// get the custom fields
			if (ComponentHelper::getParams('com_users')->get('custom_fields_enable', '1')) {
				$customfields['com_users'] = self::getCoreFields(array('media'), 'com_users.user');
			}
			if (ComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
				$customfields['com_contact'] = self::getCoreFields(array('media'), 'com_contact.contact');
			}
		}

		$options = array();

		$options[] = HTMLHelper::_('select.option', 'contact', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_LINKEDCONTACT'), 'value', 'text', $disable = false);
		// TODO $options[] = HTMLHelper::_('select.option', 'gravatar', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_GRAVATAR'), 'value', 'text', $disable = false);

		if (isset($customfields['com_users'])) {
			$group_options = self::getFieldGroup('com_users', $customfields['com_users'], 'media');
			$options = array_merge($options, $group_options);
		}

		if (isset($customfields['com_contact'])) {
			$group_options = self::getFieldGroup('com_contact', $customfields['com_contact'], 'media');
			$options = array_merge($options, $group_options);
		}

		// TODO add from community builder profile

		// merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	protected function getFieldGroup($option, $fields, $type)
	{
		$options = array();

		if (empty($fields)) {
			return $options;
		}

		// organize the fields according to their group

		$fieldsPerGroup = array(
			0 => array()
		);

		$groupTitles = array(
			0 => Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_NOGROUPFIELD')
		);

		$fields_exist = false;
		foreach ($fields as $field) {

			if ($field->type != $type) {
				continue;
			}

			if (!array_key_exists($field->group_id, $fieldsPerGroup)) {
				$fieldsPerGroup[$field->group_id] = array();
				$groupTitles[$field->group_id] = $field->group_title;
			}

			$fieldsPerGroup[$field->group_id][] = $field;
			$fields_exist = true;
		}

		// loop trough the groups

		$prefix = 'jfield';
		if ($option != 'com_content') {
			$prefix .= str_replace('com_', '' , $option);
		}

		if ($fields_exist) {
			foreach ($fieldsPerGroup as $group_id => $groupFields) {

				if (!$groupFields) {
					continue;
				}

				foreach ($groupFields as $field) {
					$options[] = HTMLHelper::_('select.option', $prefix.':'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', $disable = false);
				}
			}
		}

		return $options;
	}

}
?>