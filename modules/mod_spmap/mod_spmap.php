<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Novarain Framework is not installed', 500);
}

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/fields/SPMapMarkers.php';

$markers = (new SPMapMarkers($params));
$markers = $markers->getAll();

require ModuleHelper::getLayoutPath('mod_spmap', $params->get('layout', 'default'));