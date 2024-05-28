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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	Factory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFPHP extends ACF_Field
{
	/**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   Form        $form    The form.
	 *
	 * @return  DOMElement
	 *
	 * @since   3.7.0
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, Joomla\CMS\Form\Form $form)
	{
		if (!$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form))
		{
			return;
		}

		$fieldNode->setAttribute('type', 'textarea');
		$fieldNode->setAttribute('filter', 'raw');
		$fieldNode->setAttribute('class', 'span12 w-100');
		$fieldNode->setAttribute('rows', '10');
		$fieldNode->setAttribute('hint', '$name = "John Doe";\nreturn $name;');

		return $fieldNode;
	}

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
				
		// Get plugin params
		$plugin = PluginHelper::getPlugin('fields', 'acfphp');
		$params = new Registry($plugin->params);

		$payload = [
			'field' => $field,
			'item'  => $item
		];

		// Enable buffer output
		$executer = new \NRFramework\Executer($field->value, $payload);

		$field->value = $executer
			->setForbiddenPHPFunctions($params->get('forbidden_php_functions'))
			->run();

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}
}
