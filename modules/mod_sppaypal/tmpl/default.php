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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

$language = $params->get('language', '');
if ($language === 'auto')
{
	$locale = Factory::getLanguage()->getLocale();

	if (is_array($locale) && count($locale))
	{
		$locale = explode('.', $locale[0]);
		$language = reset($locale);
	}
}

$arrayParams = $params->toArray();

$payload = [
	'label' => $params->get('label', 'paypal'),
	'button_layout' => $params->get('button_layout', 'vertical'),
	'color' => $params->get('color', 'gold'),
	'corner_style' => $params->get('corner_style', 'rect'),
	'tagline' => $params->get('tagline', '1') === '1',
	'testmode' => $params->get('testmode', '1') === '1',
	'success_hide_payment_buttons' => $params->get('success_hide_payment_buttons', '0') === '1',
	'item_name' => $params->get('item_name', ''),
	'billing_amount' => $params->get('billing_amount', ''),
	'currency' => $params->get('currency', 'USD'),
	'locale' => $language,
	'pro' => \NRFramework\Extension::isPro('com_smilepack'),
	'max_width' => isset($arrayParams['max_width']) ? $arrayParams['max_width'] : null,
	'show_shipping_address' => $params->get('show_shipping_address', '1') === '1',
	'css_class' => ' id-' . $module->id,
	
	'action' => 'message',
	
	'success_message' => $params->get('success_message', ''),
	
];

echo (new \SmilePack\Widgets\PayPal($payload))->render();