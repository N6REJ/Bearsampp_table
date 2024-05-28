<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Module\LatestNewsEnhanced\Site\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use SYW\Library\K2 as SYWK2;

class HeadselectField extends GroupedlistField
{
    public $type = 'Headselect';

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

        if (SYWK2::exists()) {
            // get K2 extra fields
            $k2extrafields = self::getK2Fields(array('date', 'image'));
        }

        if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && ComponentHelper::isEnabled('com_fields')) {

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

        // images

        $group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEGROUP');
        $groups[$group_name] = array();

        //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEGROUP'));

        $groups[$group_name][] = HTMLHelper::_('select.option', 'image', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGE'), 'value', 'text', false);

        if (SYWK2::exists()) {
            $groups[$group_name][] = HTMLHelper::_('select.option', 'imageintro', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEINTRO_WITHK2'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'imagefull', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEFULL_WITHK2'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesasc', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ALLIMAGESASC_WITHK2'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesdesc', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ALLIMAGESDESC_WITHK2'), 'value', 'text', false);
        } else {
            $groups[$group_name][] = HTMLHelper::_('select.option', 'imageintro', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEINTRO'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'imagefull', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEFULL'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesasc', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ALLIMAGESASC'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'allimagesdesc', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ALLIMAGESDESC'), 'value', 'text', false);
            $groups[$group_name][] = HTMLHelper::_('select.option', 'categoryimage', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CATEGORYIMAGE'), 'value', 'text', false);
        }

        $groups[$group_name][] = HTMLHelper::_('select.option', 'author', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AUTHORCONTACT'), 'value', 'text', false);
        if (SYWK2::exists()) {
            $groups[$group_name][] = HTMLHelper::_('select.option', 'authork2user', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_AUTHORK2USER'), 'value', 'text', false);
        }

        if (isset($customfields['com_users'])) {
        	$group_options = self::getFieldGroup('com_users', $customfields['com_users'], 'media');
        	$groups[$group_name] = array_merge($groups[$group_name], $group_options);
        }

        if (isset($customfields['com_content'])) {
        	$group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'media');
        	$groups[$group_name] = array_merge($groups[$group_name], $group_options);
        }

        if (SYWK2::exists()) {
            $group_options = self::getFieldGroup('com_k2', $k2extrafields, 'image');
            $groups[$group_name] = array_merge($groups[$group_name], $group_options);
        }

        //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_IMAGEGROUP'));

        // icons

        if (PluginHelper::isEnabled('fields', 'sywicon')) {
        	$group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'sywicon');
            if (!empty($group_options)) {

            	$group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ICONGROUP');
            	$groups[$group_name] = array();

                //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ICONGROUP'));
                $groups[$group_name] = array_merge($groups[$group_name], $group_options);
                //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_ICONGROUP'));
            }
        }

        // calendars

        $group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CALENDARGROUP');
        $groups[$group_name] = array();

        //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CALENDARGROUP'));

        $groups[$group_name][] = HTMLHelper::_('select.option', 'calendar', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CALENDAR'), 'value', 'text', false);

        $group_options = self::getFieldGroup('com_content', $customfields['com_content'], 'calendar');
        $groups[$group_name] = array_merge($groups[$group_name], $group_options);

        if (SYWK2::exists()) {
            $group_options = self::getFieldGroup('com_k2', $k2extrafields, 'date');
            $groups[$group_name] = array_merge($groups[$group_name], $group_options);
        }

        //$options[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_CALENDARGROUP'));

        // videos

        if (count($video_types_fields) > 0) {
            $options_tmp = array();
            $got_video_content = false;

            $group_name = Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_VIDEOGROUP');
            $groups[$group_name] = array();

            //$options_tmp[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_VIDEOGROUP'));

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

            //$options_tmp[] = HTMLHelper::_('select.optgroup', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_VIDEOGROUP'));

            if ($got_video_content) {
                $groups[$group_name] = array_merge($groups[$group_name], $options_tmp);
            }
        }

        $groups = array_merge(parent::getGroups(), $groups);

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
                0 => Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_NOGROUPFIELD')
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
}
?>