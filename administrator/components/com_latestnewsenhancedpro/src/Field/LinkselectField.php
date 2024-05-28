<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use SYW\Library\K2 as SYWK2;

class LinkselectField extends GroupedlistField
{
	public $type = 'Linkselect';

	protected $option;

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

		if ($this->option == 'com_content') {
			$groups[$group_name][] = HTMLHelper::_('select.option', 'item', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ARTICLE'), 'value', 'text', false);
			//$groups[$group_name][] = HTMLHelper::_('select.option', 'modal', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ARTICLEMODAL'), 'value', 'text', false);
		}

		if ($this->option == 'com_k2' && SYWK2::exists()) {
			$groups[$group_name][] = HTMLHelper::_('select.option', 'item', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2ITEM'), 'value', 'text', false);
			//$groups[$group_name][] = HTMLHelper::_('select.option', 'modal', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2ITEMMODAL'), 'value', 'text', false);
		}

		if ($this->option == 'com_content') {
			$groups[$group_name][] = HTMLHelper::_('select.option', 'urla', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_URLA'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'urlb', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_URLB'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'urlc', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_URLC'), 'value', 'text', false);
		}

		if ($this->option == 'com_k2' && SYWK2::exists()) {

			// get K2 extra fields
			$fields = self::getK2Fields(array('link'));

			$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2EXTRAFIELDS');
			$groups[$group_name] = array();

			foreach ($fields as $field) {
				$groups[$group_name][] = HTMLHelper::_('select.option', 'k2field:'.$field->type.':'.$field->id, 'K2: '.$field->group_name.': '.$field->name, 'value', 'text', false);

			}
		}

		// get Joomla! fields
		// test the fields folder first to avoid message warning that the component is missing
		if ($this->option == 'com_content' && Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

		    $supported_types = ['url'];
		    
		    if (PluginHelper::isEnabled('system', 'acf')) {
		        $supported_types[] = 'acfurl';
		    }
		    
		    // get the custom fields
		    $fields = self::getCoreFields($supported_types);

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
						$groups[$group_name][] = HTMLHelper::_('select.option', 'jfield:'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', false);
					}
				}

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_JOOMLAFIELDS'));
			}
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->option = isset($this->element['option']) ? $this->element['option'] : 'com_content';
		}

		return $return;
	}
}
?>