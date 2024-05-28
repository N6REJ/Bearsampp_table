<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\ArticleDetailsProfiles\Administrator\View\Info;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Article Details Profiles Info View
 */
class HtmlView extends BaseHtmlView
{
	public $tmpl;

	/**
	 * display method of view
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$HTTPClient = new Http();

		// installed extension version

		$this->extension_version = strval(simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_articledetailsprofiles/articledetailsprofiles.xml')->version);

		// available version

		$model = $this->getModel('info');
		$model->setState('option', 'articledetailsprofiles');
		$this->version_array = $model->getUpdates($HTTPClient);

		// extended plugins

		$this->extended_plugins = $model->getExtendedPlugins();

		// license information

		$this->license_array = $model->getLicenseState($HTTPClient);

		$this->license_is_valid = (!empty($this->license_array['download_id']) && isset($this->license_array['enabled']) && $this->license_array['enabled'] && isset($this->license_array['expiration_date']) && ($this->license_array['expiration_date'] == '' || strtotime('now') < strtotime($this->license_array['expiration_date']))) ? true: false;

		$this->addToolbar();

		return parent::display($tpl);
	}

	protected function getIcon($link, $icon, $text, $target = '', $title = '', $class='')
	{
		$data = array('link' => $link, 'image' => $icon, 'text' => $text);
		if ($target) {
			$data['target'] = $target;
		}
		
		if ($title) {
			$data['title'] = $title;
		}
		
		if ($class) {
			$data['class'] = $class;
		}
		
		$layout = new FileLayout('joomla.quickicons.icon');
		return $layout->render($data);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolbar()
	{
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_( 'COM_ARTICLEDETAILSPROFILES_INFO' ), 'none SYWicon-info');

		if (Factory::getUser()->authorise('core.admin', 'com_articledetailsprofiles')) {
		    $toolbar->preferences('com_articledetailsprofiles');
		}

		$toolbar->help(null, false, 'https://simplifyyourweb.com/documentation/article-details');
	}

}
?>
