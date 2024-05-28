<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace SmilePack\Widgets;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Component\ComponentHelper;
use NRFramework\Widgets\Widget;

class PayPal extends Widget
{
	/**
	 * The component params.
	 * 
	 * @var Registry
	 */
	private $params;
	
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		/**
		 * Enable test mode.
		 * 
		 * Both the PayPal Client ID and Secret must be sandbox credentials.
		 * 
		 * This is used for creating the plan id in AJAX call.
		 */
		'testmode' => false,
		
		/**
		 * The plan ID is required for recurring subscriptions.
		 * 
		 * This is optional but if given it'll be used to create the subscription based on the provided plan_id.
		 * 
		 * Tip: You can create a plan id manually via your PayPal dashboard: https://www.paypal.com/billing/plans
		 */
		'plan_id' => '',
		
		// The type of PayPal button to load
		'label' => 'paypal',
		
		/**
		 * The button layout.
		 * 
		 * Default: vertical
		 * 
		 * Available options:
		 * 
		 * - vertical: Display the buttons vertically
		 * - horizontal: Display the buttons horizontally. Supports up to 2 buttons. Doesn't support credit card option.
		 */
		'button_layout' => 'vertical',

		/**
		 * The button color.
		 * 
		 * Available options:
		 * - gold
		 * - blue
		 * - silver
		 * - black
		 * - white
		 */
		'color' => 'gold',

		/**
		 * The button corner style.
		 * 
		 * Available options:
		 * 
		 * - sharp
		 * - rect
		 * - pill
		 */
		'corner_style' => 'rect',

		// The button max width.
		'max_width' => '',

		/**
		 * Show the PayPal tagline.
		 * 
		 * This works only for the horizontal layout.
		 */
		'tagline' => true,

		/**
		 * The product type.
		 * 
		 * Available options:
		 * - PHYSICAL: A physical product
		 * - DIGITAL: A digital product
		 * - SERVICE: A service
		 */
		'product_type' => '',

		// The product name
		'item_name' => '',

		// The billing amount
		'billing_amount' => '',

		/**
		 * The billing cycle.
		 * 
		 * Available options:
		 * - onetime
		 * - week
		 * - month
		 * - year
		 * - custom (Set the recurring interval in the recurring_interval option)
		 */
		'billing_cycle' => 'onetime',

		// The recurring interval in days
		'recurring_interval' => '',

		/**
		 * The currency code.
		 * 
		 * Currency codes: https://developer.paypal.com/reference/currency-codes/
		 */
		'currency' => 'USD',

		// The tax rate (1-100)%
		'tax_rate' => 0,

		// The shipping amount
		'shipping_amount' => 0,

		/**
		 * The locale that'll be used on the PayPal checkout page.
		 * 
		 * Locale codes: https://developer.paypal.com/reference/locale-codes/
		 */
		'locale' => '',

		/**
		 * The payment payments.
		 * 
		 * Available options:
		 * 
		 * - auto: Show all available payment methods
		 * - custom: Allows the use of include_payment_methods and exclude_payment_methods payment methods
		 */
		'payment_methods' => 'auto',

		// Includes specific payment methods.
		'include_payment_methods' => [],

		// Excludes specific payment methods.
		'exclude_payment_methods' => [],

		// Set whether to show the shipping address within the PayPal checkout form
		'show_shipping_address' => true,

		/**
		 * The success action.
		 * 
		 * Available options:
		 * - message
		 * - redirect
		 */
		'action' => 'message',

		// The success message
		'success_message' => '',

		// The success redirect URL
		'success_url' => '',

		// Set whether to hide the payment buttons after a successful payment
		'success_hide_payment_buttons' => true
	];

	/**
	 * Renders the widget with the given layout
	 * 
	 * @return  void
	 */
	public function render()
	{
		$this->params = $this->getParams();

		if (!$this->isReady())
		{
			return;
		}

		$this->prepare();
		
		$this->styles();

		$this->loadMedia();

		$defaultPath  = implode(DIRECTORY_SEPARATOR, [JPATH_ADMINISTRATOR, 'components', 'com_smilepack', 'layouts']);

		$layout = new FileLayout('widgets.' . $this->getName() . '.' . $this->options['layout'], null, ['debug' => false]);
		$layout->addIncludePaths($defaultPath);

		return $layout->render($this->options);
	}

	/**
	 * Checks if the widget is ready to be rendered.
	 * 
	 * @return  boolean
	 */
	private function isReady()
	{
		if (is_bool($this->params))
		{
			echo Text::_('COM_SMILEPACK_CANNOT_FIND_PAYPAL_PARAMS');
			return false;
		}

		\NRFramework\Functions::loadLanguage('com_smilepack');

		$prefix = $this->options['testmode'] ? 'sandbox' : 'live';
		$mode_label = Text::_('COM_SMILEPACK_' . strtoupper($prefix));

		$url = Uri::base() . 'administrator/index.php?option=com_config&view=component&component=com_smilepack';

		$keys = $this->getKeys($this->options['testmode']);

		$error = false;
		
		// Abort if the client ID is missing
		if (empty($keys['client_id']))
		{
			echo '<p>' . Text::sprintf('COM_SMILEPACK_PAYPAL_CLIENT_ID_KEY_MISSING', $mode_label, $mode_label, $url) . '</p>';
			$error = true;
		}
		
		

		return !$error;
	}

	/**
	 * Prepare the widget.
	 * 
	 * @return  void
	 */
	private function prepare()
	{
		

		$this->replaceLocalSmartTags();
	}

	private function replaceLocalSmartTags()
	{
		if (!$this->options['pro'])
		{
			return;
		}

		$replacements = [
			'{this.product_name}' => $this->options['item_name'],
			'{this.billing_amount}' => $this->getTotalAmount(),
			'{this.currency}' => $this->options['currency']
		];

		$replacement_keys = [
			'item_name'
		];

		// Replace success message
		if ($this->options['action'] === 'message')
		{
			$replacement_keys[] = 'success_message';
		}
		
		
		
		foreach ($replacement_keys as $key)
		{
			$this->options[$key] = str_replace(array_keys($replacements), array_values($replacements), $this->options[$key]);
		}
	}

	/**
	 * Add custom styles.
	 * 
	 * @return  void
	 */
	public function styles()
	{
		if (!$this->options['load_css_vars'])
		{
			return;
		}
	
		$controls = [
            [
                'property' => '--max-width',
                'value' => $this->options['max_width']
            ],
		];

		$selector = '.sp-paypal-button.' . $this->options['id'];
		
		$controlsInstance = new \NRFramework\Controls\Controls(null, $selector);

        if (!$controlsCSS = $controlsInstance->generateCSS($controls))
        {
            return;
        }

        Factory::getDocument()->addStyleDeclaration($controlsCSS);
	}

	/**
	 * Loads media files
	 * 
	 * @return  void
	 */
	public function loadMedia()
	{
		$queryStrings = [];

		// Add currency
		$queryStrings[] = 'currency=' . $this->options['currency'];

		// Add locale
		if (!empty($this->options['locale']))
		{
			$queryStrings[] = 'locale=' . $this->options['locale'];
		}

		

		$keys = $this->getKeys($this->options['testmode']);
		
		HTMLHelper::script('https://www.paypal.com/sdk/js?client-id=' . $keys['client_id'] . '&' . implode('&', $queryStrings));
		HTMLHelper::script('com_smilepack/widgets/paypal-button.js', ['version' => 'auto', 'relative' => true]);
		HTMLHelper::stylesheet('com_smilepack/widgets/paypal-button.css', ['version' => 'auto', 'relative' => true]);
	}

	

	/**
	 * Returns the total amount.
	 * 
	 * This includes the billing amount, shipping amount and tax rate.
	 * 
	 * @return  float
	 */
	private function getTotalAmount()
	{
		$amount = $this->options['billing_amount'];

		// Prepare the amount, because it might be a Smart Tag
		$amount = (float) HTMLHelper::_('content.prepare', $amount);

		$shipping_amount = (float) $this->options['shipping_amount'];

		$tax_rate = (float) $this->options['tax_rate'];

		$amount = $amount + $shipping_amount + ($amount * $tax_rate / 100);

		return $amount;
	}

	/**
	 * Returns the PayPal API URL.
	 * 
	 * @param   boolean  $testmode  Whether to use the sandbox API URL
	 * 
	 * @return  string
	 */
	private function getPayPalAPIURL($testmode = false)
	{
		return 'https://api-m' . ($testmode ? '.sandbox' : '') . '.paypal.com/v1/';
	}

	/**
	 * Returns the PayPal keys.
	 * 
	 * @param   boolean  $testmode  Whether to use the sandbox keys
	 * 
	 * @return  array
	 */
	public function getKeys($testmode = false)
	{
		$prefix = $testmode ? 'sandbox' : 'live';
		
		return [
			'client_id' => $this->params->get($prefix . '_paypal_client_id', ''),
			'client_secret' => $this->params->get($prefix . '_paypal_client_secret', '')
		];
	}
	
	/**
	 * Returns the component params.
	 * 
	 * @return  Registry|boolean
	 */
	private function getParams()
	{
		$params = ComponentHelper::getComponent('com_smilepack', true);

		if (!$params)
		{
			return false;
		}

		if (!$params->enabled)
		{
			return false;
		}

		return $params->params;
	}
}