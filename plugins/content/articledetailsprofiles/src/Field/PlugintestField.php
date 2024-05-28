<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Field;

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

/*
 * Checks if the plugin is enabled and report on the position used
 */
class PlugintestField extends FormField
{
	public $type = 'Plugintest';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$html = '';

		$lang = Factory::getLanguage();
		$lang->load('plg_content_articledetailsprofiles.sys');

		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

		if (PluginHelper::isEnabled('content', 'articledetailsprofiles')) {

			$config_params = ComponentHelper::getParams('com_articledetailsprofiles');
			
			$position = trim($config_params->get('position', 'adprofile'));

			$html .= '<div class="alert alert-info">';
			$html .= '<span>'.Text::sprintf('PLG_CONTENT_ARTICLEDETAILSPROFILES_INFO_PLUGINPOSITION', $position).'</span><br />';
			$html .= '</div>';

		} else {
			$html .= '<div class="alert alert-warning" style="margin-bottom: 0">';
			$html .= '<span>'.Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_WARNING_PLUGINDISABLED').'</span><br /><br />';
			$html .= '<a class="btn btn-sm btn-primary hasTooltip" title="'.Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_VALUE_ENABLE').'" href="index.php?option=com_plugins&view=plugins&filter_folder=content&filter_enabled=0">'.Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_VALUE_ENABLE').'</span></a><br />';
			$html .= '</div>';
		}

		return $html;
	}

}
?>