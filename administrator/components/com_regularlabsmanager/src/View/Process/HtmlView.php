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

namespace RegularLabs\Component\RegularLabsExtensionsManager\Administrator\View\Process;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    /**
     * @var  object
     */
    protected $config;
    /**
     * @var  array
     */
    protected $items;

    /**
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  False if unsuccessful, otherwise void.
     *
     * @throws  GenericDataException
     */
    public function display($tpl = null)
    {
        $this->items  = $this->get('Items');
        $this->config = RL_Parameters::getComponent('regularlabsmanager');

        $process = JFactory::getApplication()->input->get('process');

        if ($process)
        {
            $this->setLayout($process);
        }

        parent::display($tpl);
    }
}
