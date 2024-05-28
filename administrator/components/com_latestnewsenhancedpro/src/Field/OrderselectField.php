<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class OrderselectField extends ListField
{
	public $type = 'Orderselect';

	protected $option;

	protected function getOptions()
	{
		// category

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_CATEGORY'));

		$options[] = HTMLHelper::_('select.option', 'image', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGE'), 'value', 'text', $disable = false);

		//$options[] = HTMLHelper::_('select.optgroup', Text::_('COM_LATESTNEWSENHANCEDPRO_VALUE_IMAGEGROUP'));

		return $options;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->option = isset($this->element['option']) ? $this->element['option'] : '';
		}

		return $return;
	}
}
?>