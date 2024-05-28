<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         9.0.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2023 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\RegularLabsExtensionsManager\Administrator\Model;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\MVC\Model\ListModel;
use RegularLabs\Component\RegularLabsExtensionsManager\Administrator\Helper\ExtensionsHelper;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

class MainModel extends ListModel
{
    protected $config;

    /**
     * @var     string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    /**
     * Constructor.
     *
     * @param array    An optional associative array of configuration settings.
     *
     * @see        JController
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $is_modal = JFactory::getApplication()->input->getString('layout') === 'modal';

        if ($is_modal)
        {
            $this->context .= '_modal';
        }

        $this->config = RL_Parameters::getComponent('regularlabsmanager');
    }

    public function getItems($refresh = false)
    {
        return ExtensionsHelper::get();
    }
}
