<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Load Framework
if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Tassos Framework is not installed', 500);
}

// Initialize Smile Pack Library
require_once JPATH_ADMINISTRATOR . '/components/com_smilepack/autoload.php';

$app = Factory::getApplication();

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_smilepack'))
{
	$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

use NRFramework\Functions;
use NRFramework\Extension;

// Load framework's and component's language files
Functions::loadLanguage();
Functions::loadLanguage('com_smilepack');
Functions::loadLanguage('plg_system_smilepack');

// Check required extensions
if (!Extension::pluginIsEnabled('nrframework'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('COM_SMILEPACK'), Text::_('PLG_SYSTEM_NRFRAMEWORK')), 'error');
}

if (!Extension::pluginIsEnabled('smilepack'))
{
	$app->enqueueMessage(Text::sprintf('NR_EXTENSION_REQUIRED', Text::_('COM_SMILEPACK'), Text::_('PLG_SYSTEM_SMILEPACK')), 'error');
}

// Perform the Request task
$controller	= BaseController::getInstance('SmilePack');
$controller->execute($app->input->get('task'));
$controller->redirect();