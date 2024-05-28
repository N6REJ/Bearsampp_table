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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use SYW\Library\K2 as SYWK2;

class HeadselectField extends GroupedlistField
{
	public $type = 'Headselect';

	protected $option;
	protected $syw_useglobal;

	static $core_fields = null;
	static $k2_fields = null;

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

		$k2extrafields = array();
		$customfields = array();

        $video_types_fields = array();

		if ($this->option == 'com_k2' && SYWK2::exists()) {
			// get K2 extra fields
			$k2extrafields = self::getK2Fields(array('date', 'image'));
		}

		if ($this->option == 'com_content' && Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields')) {

			$field_types = array('calendar', 'media');

			if (PluginHelper::isEnabled('fields', 'sywicon')) {
				$field_types[] = 'sywicon';
			}

            $config_params = ComponentHelper::getParams('com_latestnewsenhancedpro');

            $video_types_fields['dailymotion'] = $config_params->get('supported_dailymotion_fields', array());
            $video_types_fields['facebookvideo'] = $config_params->get('supported_facebookvideo_fields', array());
            $video_types_fields['html5video'] = $config_params->get('supported_html5video_fields', array());
            $video_types_fields['vimeo'] = $config_params->get('supported_vimeo_fields', array());
            $video_types_fields['youtube'] = $config_params->get('supported_youtube_fields', array());

            foreach ($video_types_fields as $video_type => $video_fields) {
                foreach ($video_fields as $video_field) {
                    if (PluginHelper::isEnabled('fields', $video_field)) {
                        $field_types[] = $video_field;
                    }
                }
			}

			// get the content custom fields
			if (ComponentHelper::getParams('com_content')->get('custom_fields_enable', '1')) {
				$customfields['com_content'] = self::getCoreFields($field_types);
			}

			// get the user custom fields
			if (ComponentHelper::getParams('com_users')->get('custom_fields_enable', '1')) {
				$customfields['com_users'] = self::getCoreFields(array('media'), 'com_users.user');
			}
		}

		//$groups[] = array(HTMLHelper::_('select.option', 'none', Text::_('JNONE'), 'value', 'text', false));

		// images

		$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEGROUP');
		$groups[$group_name] = array();

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEGROUP'));

		$groups[$group_name][] = HTMLHelper::_('select.option', 'image', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGE'), 'value', 'text', false);

		if ($this->option == 'com_content') {
			$groups[$group_name][] = HTMLHelper::_('select.option', 'imageintro', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEINTRO'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'imagefull', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEFULL'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesasc', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ALLIMAGESASC'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesdesc', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ALLIMAGESDESC'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'categoryimage', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CATEGORYIMAGE'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'authorcontact', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_AUTHORCONTACT'), 'value', 'text', false);

			if (isset($customfields['com_users'])) {
				$group_options = self::getFieldGroup('com_users', $customfields['com_users'], 'media');
				$groups[$group_name] = array_merge($groups[$group_name], $group_options);
			}

			if (isset($customfields['com_content'])) {
				$group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'media');
				$groups[$group_name] = array_merge($groups[$group_name], $group_options);
			}
		}

		if ($this->option == 'com_k2' && SYWK2::exists()) {
			$groups[$group_name][] = HTMLHelper::_('select.option', 'imageintro', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2IMAGE'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesasc', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2ALLIMAGESASC'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesdesc', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_K2ALLIMAGESDESC'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'authorcontact', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_AUTHORCONTACT'), 'value', 'text', false);
			$groups[$group_name][] = HTMLHelper::_('select.option', 'authork2user', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_AUTHORK2USER'), 'value', 'text', false);

			$group_options = self::getFieldGroup('com_k2', $k2extrafields, 'image');
			$groups[$group_name] = array_merge($groups[$group_name], $group_options);
		}

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEGROUP'));

		// icons

		if ($this->option == 'com_content' && PluginHelper::isEnabled('fields', 'sywicon')) {
			$group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'sywicon');
			if (!empty($group_options)) {

				$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ICONGROUP');
				$groups[$group_name] = array();

				//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ICONGROUP'));
				$groups[$group_name] = array_merge($groups[$group_name], $group_options);
				//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_ICONGROUP'));
			}
		}

		// calendars

		$group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CALENDARGROUP');
		$groups[$group_name] = array();

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CALENDARGROUP'));

		$groups[$group_name][] = HTMLHelper::_('select.option', 'calendar', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CALENDAR'), 'value', 'text', false);

		if ($this->option == 'com_content') {
			$group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'calendar');
			$groups[$group_name] = array_merge($groups[$group_name], $group_options);
		}

		if ($this->option == 'com_k2' && SYWK2::exists()) {
			$group_options = self::getFieldGroup('com_k2', $k2extrafields, 'date');
			$groups[$group_name] = array_merge($groups[$group_name], $group_options);
		}

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CALENDARGROUP'));

		// videos

		if ($this->option == 'com_content' && count($video_types_fields) > 0) {

		    $options_tmp = array();
		    $got_video_content = false;

		    $group_name = Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_VIDEOGROUP');
		    $groups[$group_name] = array();

		    //$options_tmp[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_VIDEOGROUP'));

		    $found_video_fields = array();
		    foreach ($video_types_fields as $video_type => $video_fields) {
		        foreach ($video_fields as $video_field) {
		            if (PluginHelper::isEnabled('fields', $video_field)) {
		            	$group_options = self::getFieldGroup('com_content', $customfields['com_content'], $video_field);
		                if (!empty($group_options)) {
		                    foreach ($group_options as $key => $option) {
		                        if (in_array($option->value, $found_video_fields)) {
		                            unset($group_options[$key]);
		                        } else {
		                            $found_video_fields[] = $option->value;
		                        }
		                    }
		                    $options_tmp = array_merge($options_tmp, $group_options);
		                    $got_video_content = true;
		                }
		            }
		        }
		    }

		    //$options_tmp[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_VIDEOGROUP'));

		    if ($got_video_content) {
		    	$groups[$group_name] = array_merge($groups[$group_name], $options_tmp);
		    }
		}

		// merge any additional options in the XML definition
		$groups = array_merge(parent::getGroups(), $groups);

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

			array_unshift($groups, array(HTMLHelper::_('select.option', '', $global_text, 'value', 'text', false)));
		}

		return $groups;
	}

	protected function getFieldGroup($option, $fields, $type)
	{
		$options = array();

		if (empty($fields)) {
			return $options;
		}

		if ($option == 'com_k2') {

			$fields_count = 0;
			foreach ($fields as $field) {

				if ($field->type != $type) {
					continue;
				}

				$options[] = HTMLHelper::_('select.option', 'k2field:'.$field->type.':'.$field->id, 'K2: '.$field->group_name.': '.$field->name, 'value', 'text', false);

				$fields_count++;
			}
		} else {

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
						$options[] = HTMLHelper::_('select.option', $prefix.':'.$field->type.':'.$field->id, $groupTitles[$group_id].': '.$field->title, 'value', 'text', false);
					}
				}
			}
		}

		return $options;
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