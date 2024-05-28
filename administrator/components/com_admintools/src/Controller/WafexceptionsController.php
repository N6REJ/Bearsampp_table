<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Controller;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCopyTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerRegisterTasksTrait;
use Joomla\CMS\MVC\Controller\AdminController;

class WafexceptionsController extends AdminController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerCopyTrait;
	use ControllerRegisterTasksTrait;

	protected $text_prefix = 'COM_ADMINTOOLS_WAFEXCEPTIONS';

	public function getModel($name = 'Wafexception', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}