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

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFChainedFields extends ACF_Field
{
	/**
	 *  Override the field type
	 *
	 *  @var  string
	 */
	protected $overrideType = 'NRChainedFields';

	/**
	 * Prepares the field value for the (front-end) layout
	 *
	 * @param   string    $context  The context.
	 * @param   stdclass  $item     The item.
	 * @param   stdclass  $field    The field.
	 *
	 * @return  string
	 */
	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		// Check if the field should be processed by us
		if (!$this->isTypeSupported($field->type))
		{
			return parent::onCustomFieldsPrepareField($context, $item, $field);
		}

		$data_source = $field->fieldparams->get('data_source', 'custom');
		
		$dataset = '';
		switch ($data_source)
		{
			case 'custom':
				$dataset = $field->fieldparams->get('data_source_custom', '');
				break;

			case 'csv_file':
				$csv_file = $field->fieldparams->get('data_source_csv', '');

				if (!file_exists($csv_file))
				{
					return $parent;
				}

				$dataset = $csv_file;
				break;
		}

		if (!$choices = \NRFramework\Helpers\ChainedFields::loadCSV($dataset, $data_source, $field->fieldparams->get('separator'), $field->name . '_', $field->name))
		{
			return $parent;
		}

		$field->choices = $choices;
		
		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}
}
