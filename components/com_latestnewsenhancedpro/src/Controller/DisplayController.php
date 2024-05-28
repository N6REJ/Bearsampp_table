<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Latest News Enhanced Pro display controller
 */
class DisplayController extends BaseController
{
    /**
     * Constructor
     */
    public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        $input = Factory::getApplication()->input;

        parent::__construct($config, $factory, $app, $input);
    }

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  static  This object to support chaining.
     */
    public function display($cachable = false, $urlparams = array())
    {
        // Set the default view name and format from the Request
        $vName = $this->input->get('view', 'articles');
        $this->input->set('view', $vName);

        if (Factory::getUser()->get('id')) {
            $cachable = false;
        }

        // TODO add custom fields... how?

        $safeurlparams = array(
            'catid'=>'INT', // ok
            'id'=>'INT', // ok

            'category'=>'INT', // ok
            'tag'=>'INT', // ok
            'author'=>'STRING', // ok
            'alias'=>'STRING', // ok
        	'period'=>'STRING', // ok

            'anchor'=>'STRING', // ok

            'limit'=>'UINT', // ok
            'limitstart'=>'UINT', // ok

            'return'=>'BASE64', // ok return page after form submission

            'filter_order'=>'CMD',
            'filter_order_Dir'=>'CMD',
            'filter-search'=>'STRING', // ok
            'filter-match'=>'STRING', // ok

            'print'=>'BOOLEAN', // ok
            'lang'=>'CMD', // ok
            'Itemid' => 'INT' // ok makes sure that when cached, menu items are different from each other
        );
        
        parent::display($cachable, $safeurlparams);

        return $this;
    }
}
