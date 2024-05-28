<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$file = $field->value;

if (!$file || $file == '-1')
{
	return;
}

// Setup Variables
$class     = $fieldParams->get('class');
$label     = $fieldParams->get('label', 'ACF_DOWNLOADBUTTON_DOWNLOAD');
$directory = ltrim($fieldParams->get('directory', 'images'), '/');
$directory = rtrim($directory, '/');
$filepath  = Uri::root() . $directory . '/' . $file;

// Output
$buffer = '<a href="' . $filepath . '" class="' . $class . '" download>' . Text::_($label) . '</a>';

echo $buffer;