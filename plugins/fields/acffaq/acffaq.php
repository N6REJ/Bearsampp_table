<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFFAQ extends ACF_Field
{
	/**
	 *  Override the field type
	 *
	 *  @var  string
	 */
	protected $overrideType = 'FAQ';

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param   JForm     $form  The form
	 * @param   stdClass  $data  The data
	 *
	 * @return  void
	 */
	public function onContentPrepareForm(Form $form, $data)
	{
		$data = (object) $data;

		// Make sure we are manipulating the right field.
		if (isset($data->type) && $data->type != $this->_name)
		{
			return;
		}

		/**
		 * Set the configuration for the templates.
		 * 
		 * These are handed over to Javascript and
		 * whenever we click on a preset, these values
		 * are set to each setting on the backend.
		 */
		if (Factory::getApplication()->isClient('administrator'))
		{
			Text::script('ACF_FIELD_PREVIEWER');
			Text::script('ACF_FIELD_PREVIEWER_INFO_ICON_TITLE');
			
			// Include presets
			include 'fields/helper.php';

			$script = 'window.ACFFAQPresetsData = ' . json_encode($presets) . ';';
			Factory::getDocument()->addScriptDeclaration($script);
			HTMLHelper::script('plg_system_nrframework/tffieldsvaluesapplier.js', ['relative' => true, 'version' => 'auto']);
			HTMLHelper::script('plg_fields_acffaq/faq.js', ['relative' => true, 'version' => 'auto']);
			
			$script = 'window.ACFFieldsPreviewerData = ' . json_encode([
				'fullscreenActions' => true,
				'responsiveControls' => true
			]) . ';';
			Factory::getDocument()->addScriptDeclaration($script);
			HTMLHelper::script('plg_fields_acffaq/previewer.js', ['relative' => true, 'version' => 'auto']);
		}
		
		
		return parent::onContentPrepareForm($form, $data);
	}
}