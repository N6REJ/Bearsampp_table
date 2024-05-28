<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

class SmilepackViewSmilepack extends HtmlView
{
	public $modules = [];

	public $smartTags = [];
	
	/**
	 * Items view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		$this->smartTags = \SmilePack\SmartTags::getSmartTags();

		if ($this->getLayout() == 'button')
		{
			// Load plugin language file
			NRFramework\Functions::loadLanguage("plg_editors-xtd_smilepacksmarttags");

			// Get editor name
			$eName = Factory::getApplication()->input->getCmd('e_name');

	        // Template properties
	        $this->eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);

			parent::display($tpl);
			return;
		}

		// Set the toolbar
		$this->addToolBar();

		$this->modules = \SmilePack\Modules::getModules();

		// Display the template
		parent::display($tpl);
	}

	/**
	 *  Add Toolbar to layout
	 */
	protected function addToolBar()
	{
		ToolbarHelper::title(Text::_('SMILEPACK'));

		$canDo      = Joomla\CMS\Helper\ContentHelper::getActions('com_smilepack');
		$state      = $this->get('State');
		$viewLayout = Factory::getApplication()->input->get('layout', 'default');

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::preferences('com_smilepack');
		}

		ToolbarHelper::help("Help", false, 'https://www.tassos.gr/joomla-extensions/smile-pack/docs');
	}
}