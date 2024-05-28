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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class ModSPPayPalInstance
{
    public function onBeforeRender($app)
    {
        /**
         * PayPal requires the cross-origin opener policy header to be set to "same-origin-allow-popups".
         * 
         * This ensure that the PayPal payment window opens without issues.
         * 
         * Otherwise, there will be two modals opened, one will be blank and will need to be closed manually
         * in order to view the actual modal that contains the PayPal payment form.
         */
        $app->setHeader('cross-origin-opener-policy', 'same-origin-allow-popups', true);
    }
    
    /**
     * Check if the PayPal Client ID and Secret are set
     * 
     * @param   Form    $form   The form object
     * @param   object  $data   The data object
     * 
     * @return  Form    The form object
     */
	public function onContentPrepareForm(Form &$form, $data)
    {
        if ($data->module !== 'mod_sppaypal')
        {
            return $form;
        }
        
		$component = ComponentHelper::getComponent('com_smilepack', true);

		$testmode = isset($data->params['testmode']) && $data->params['testmode'] === '1';
		$prefix = $testmode ? 'sandbox' : 'live';
		$mode_label = Text::_('COM_SMILEPACK_' . strtoupper($prefix));

		$url = Uri::base() . 'index.php?option=com_config&view=component&component=com_smilepack';
        
        if (empty($component->params->get($prefix . '_paypal_client_id', '')))
        {
            Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SMILEPACK_PAYPAL_CLIENT_ID_KEY_MISSING', $mode_label, $mode_label, $url), 'warning');
        }

        

        return $form;
    }
}