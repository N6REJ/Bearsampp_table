<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Field;

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Field\DynamicsingleselectField;

class CalendarselectField extends DynamicsingleselectField
{
	public $type = 'Calendarselect';

	protected function getOptions()
	{
		$options = array();

		$lang = Factory::getLanguage();
		$lang->load('plg_content_articledetailsprofiles.sys');

		$path = '/media/plg_content_articledetailsprofiles/styles/calendars';
		$imagepath = '/media/plg_content_articledetailsprofiles/images/calendars';

		$optionsArray = Folder::folders(JPATH_SITE.$path);

		foreach ($optionsArray as $option) {
			
			if (!File::exists(JPATH_ROOT . $imagepath . '/' . $option . '.png')) {
				continue;
			}

			$upper_option = strtoupper($option);

			if ($upper_option != 'ORIGINAL') {
				$lang->load('plg_content_articledetailsprofiles_style_calendar_'.$option);
			}

			$translated_option = Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_STYLE_CALENDAR_'.$upper_option.'_LABEL');

			$description = '';
			if (empty($translated_option) || substr_count($translated_option, 'ARTICLEDETAILSPROFILES') > 0) {
				$translated_option = ucfirst($option);
			} else {
				$description = Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_STYLE_CALENDAR_'.$upper_option.'_DESC');
				if (substr_count($description, 'ARTICLEDETAILSPROFILES') > 0) {
					$description = '';
				}
			}

			$image_hover = '';
			if (File::exists(JPATH_ROOT . $imagepath . '/' . $option . '_hover.png')) {
				$image_hover = Uri::root(true) . $imagepath . '/' . $option . '_hover.png';
			}

			$options[] = array($option, $translated_option, $description, Uri::root(true) . $imagepath . '/' . $option . '.png', $image_hover);
		}

		return $options;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->width = 192;
			$this->height = 96;
		}

		return $return;
	}
}
?>