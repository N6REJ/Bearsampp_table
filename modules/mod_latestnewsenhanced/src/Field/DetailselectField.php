<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Module\LatestNewsEnhanced\Site\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use SYW\Library\K2 as SYWK2;

class DetailselectField extends GroupedlistField
{
	public $type = 'Detailselect';

	static $config_params = null;
	static $core_fields = null;
	static $k2_fields = null;

	static function getConfigParams()
	{
		if (!isset(self::$config_params)) {
			self::$config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');
		}

		return self::$config_params;
	}

	static function getCoreFields()
	{
		if (!isset(self::$core_fields)) {
			self::$core_fields = FieldsHelper::getFields('com_content.article');
		}

		return self::$core_fields;
	}

	static function getK2Fields()
	{
		if (!isset(self::$k2_fields)) {
			self::$k2_fields = SYWK2::getK2Fields();
		}

		return self::$k2_fields;
	}

	protected function getGroups()
	{
		$groups = array();

		// $this->form->getValue('datasource', 'params'); // does not work from repeatable field
		//open in github https://github.com/joomla/joomla-cms/pull/30143/files/b7900a62051210d025bebd5184e3c2dca8db32a5
		// right now, can't get value of another field when in repeatable

		$group_name = '-';
		$groups[$group_name] = array();

		$groups[$group_name][] = HTMLHelper::_('select.option', 'hits', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_HITS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'rating', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_RATING'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'author', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AUTHOR'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'date', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_DATE'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'ago', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AGO'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'agomhd', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AGOMHD'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'agohm', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AGOHM'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'time', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_TIME'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'category', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CATEGORY'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linkedcategory', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDCATEGORY'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'tags', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_TAGS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'selectedtags', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_SELECTEDTAGS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linkedtags', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDTAGS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linkedselectedtags', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDSELECTEDTAGS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'keywords', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_KEYWORDS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'readmore', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_READMORE'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'share', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_SHAREICONS'), 'value', 'text', $disable = false);

		$groups[$group_name][] = HTMLHelper::_('select.option', 'linka', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKA'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linkb', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKB'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linkc', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKC'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'links', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKS'), 'value', 'text', $disable = false);
		$groups[$group_name][] = HTMLHelper::_('select.option', 'linksnl', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKSNEWLINE'), 'value', 'text', $disable = false);

// 		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_jcomments') && ComponentHelper::isEnabled('com_jcomments')) {
// 			$groups[$group_name][] = HTMLHelper::_('select.option', 'jcommentscount', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JCOMMENTSCOUNT'), 'value', 'text', $disable = false);
// 			$groups[$group_name][] = HTMLHelper::_('select.option', 'linkedjcommentscount', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDJCOMMENTSCOUNT'), 'value', 'text', $disable = false);
// 		}

		if (SYWK2::exists()) {
			//$groups[$group_name][] = HTMLHelper::_('select.option', 'k2_user', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2USER'), 'value', 'text', $disable = false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'k2commentscount', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2COMMENTSCOUNT'), 'value', 'text', $disable = false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'linkedk2commentscount', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_LINKEDK2COMMENTSCOUNT'), 'value', 'text', $disable = false);

			// get K2 extra fields

			$fields = self::getK2Fields();

			// supported field types
			$allowed_types = array('textfield', 'textarea', 'select', 'multipleSelect', 'radio', 'link', /*'labels',*/ 'date');

			$group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2EXTRAFIELDS');
			$groups[$group_name] = array();

			foreach ($fields as $field) {
				if (in_array($field->type, $allowed_types)) {
				    $groups[$group_name][] = HTMLHelper::_('select.option', 'k2field:'.$field->type.':'.$field->id, 'K2: '.$field->group_name.': '.$field->name, 'value', 'text', $disable = false);
				}
			}
		}

		// get Joomla! fields
		// test the fields folder first to avoid message warning that the component is missing
		if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields') && ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {

			$fields = self::getCoreFields();

			// supported field types
			$allowed_types = array('calendar', 'checkboxes', 'editor', 'integer', 'list', 'radio', 'text', 'textarea', 'url', 'sql');

			$config_params = self::getConfigParams();
			$additional_types = $config_params->get('additional_supported_fields', array());
			if (!empty($additional_types)) {
				$allowed_types = array_merge($additional_types, $allowed_types);
			}

			// organize the fields according to their group

			$fieldsPerGroup = array(
				0 => array()
			);

			$groupTitles = array(
				0 => Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_NOGROUPFIELD')
			);

			$fields_exist = false;
			foreach ($fields as $field) {

				if (!in_array($field->type, $allowed_types)) {
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

			if ($fields_exist) {

				$group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JOOMLAFIELDS');
				$groups[$group_name] = array();

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JOOMLAFIELDS'));

				foreach ($fieldsPerGroup as $group_id => $groupFields) {

					if (!$groupFields) {
						continue;
					}

					foreach ($groupFields as $field) {
						$groups[$group_name][] = HTMLHelper::_('select.option', 'jfield:'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', $disable = false);
					}
				}

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_JOOMLAFIELDS'));
			}
		}

		// get specific details from the plugins

		PluginHelper::importPlugin('latestnewsenhanced');

		// Trigger the form preparation event.
		$results = Factory::getApplication()->triggerEvent('onLatestNewsEnhancedPrepareDetailSelection');

		if (count($results) > 0) {

			$group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_PLUGINFIELDS');
			$groups[$group_name] = array();

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_PLUGINFIELDS'));

			$lang = Factory::getLanguage();
			foreach ($results as $result) {
				$lang->load('plg_latestnewsenhanced_'.$result['type'].'.sys', JPATH_ADMINISTRATOR, $lang->getTag(), true);
				foreach ($result['options'] as $option) {
					$groups[$group_name][] = HTMLHelper::_('select.option', $result['type'].':'.$option, Text::_('PLG_LATESTNEWSENHANCED_'.strtoupper($result['type']).'_DETAIL_LABEL_'.strtoupper($option)), 'value', 'text', $disable = false);
				}
			}

			//$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_PLUGINFIELDS'));
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
?>