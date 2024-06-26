<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

class NRText
{

	public static function prepareSelectItem($string, $published = 1, $type = '', $remove_first = 0)
	{
		if (empty($string))
		{
			return '';
		}

		$string = str_replace(array('&nbsp;', '&#160;'), ' ', $string);
		$string = preg_replace('#- #', '  ', $string);

		for ($i = 0; $remove_first > $i; $i++)
		{
			$string = preg_replace('#^  #', '', $string);
		}

		if (preg_match('#^( *)(.*)$#', $string, $match))
		{
			list($string, $pre, $name) = $match;

			$pre = preg_replace('#  #', ' ·  ', $pre);
			$pre = preg_replace('#(( ·  )*) ·  #', '\1 »  ', $pre);
			$pre = str_replace('  ', ' &nbsp; ', $pre);

			$string = $pre . $name;
		}

		switch (true)
		{
			case ($type == 'separator'):
				$string = $string;
				break;
			case (!$published):
				$string = $string . ' [' . Text::_('JUNPUBLISHED') . ']';
				break;
			case ($published == 2):
				$string = $string . ' [' . Text::_('JARCHIVED') . ']';
				break;
		}

		return $string;
	}
}