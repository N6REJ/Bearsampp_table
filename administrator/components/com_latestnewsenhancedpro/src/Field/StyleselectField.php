<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Field;

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Field\DynamicsingleselectField;

class StyleselectField extends DynamicsingleselectField
{
	public $type = 'Styleselect';

	protected function getOptions()
	{
		$options = array();

		$lang = Factory::getLanguage();

		$path = '/media/com_latestnewsenhancedpro/styles/themes';
		$imagepath = '/media/mod_latestnewsenhanced/images/themes';

		$optionsArray = Folder::folders(JPATH_SITE.$path);

		foreach ($optionsArray as $option) {
			
			if (!File::exists(JPATH_ROOT . $imagepath . '/' . $option . '.png')) {
				continue;
			}

			$upper_option = strtoupper($option);

			$lang->load('com_latestnewsenhancedpro_style_overall_'.$option);

			$translated_option = Text::_('COM_LATESTNEWSENHANCEDPRO_STYLE_OVERALL_'.$upper_option.'_LABEL');

			$description = '';
			if (empty($translated_option) || substr_count($translated_option, 'LATESTNEWSENHANCEDPRO') > 0) {
				$translated_option = ucfirst($option);
			} else {
				$description = Text::_('COM_LATESTNEWSENHANCEDPRO_STYLE_OVERALL_'.$upper_option.'_DESC');
				if (substr_count($description, 'LATESTNEWSENHANCEDPRO') > 0) {
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
			$this->width = 150;
			$this->height = 100;
		}

		return $return;
	}
}
?>