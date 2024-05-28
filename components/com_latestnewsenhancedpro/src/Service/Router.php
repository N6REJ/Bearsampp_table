<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * Routing class of com_latestnewsenhancedpro
 */
class Router extends RouterView
{
	/**
	 * Flag to remove IDs
	 *
	 * @var    boolean
	 */
	protected $noIDs = false;

	/**
	 * The category factory
	 *
	 * @var CategoryFactoryInterface
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var  array
	 */
	private $categoryCache = [];

	/**
	 * The db
	 *
	 * @var DatabaseInterface
	 */
	private $db;

	/**
	 * Latest News Enhanced Pro Component router constructor
	 *
	 * @param   SiteApplication           $app              The application object
	 * @param   AbstractMenu              $menu             The menu object to work with
	 * @param   CategoryFactoryInterface  $categoryFactory  The category object
	 * @param   DatabaseInterface         $db               The database object
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$this->categoryFactory = $categoryFactory;
		$this->db = $db;

		$params = ComponentHelper::getParams('com_latestnewsenhancedpro');
		$content_params = ComponentHelper::getParams('com_content');
		
		$this->noIDs = (bool) $content_params->get('sef_ids');

		$this->registerView(new RouterViewConfiguration('articles'));
		$this->registerView(new RouterViewConfiguration('k2items'));

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}	
	
	public function getArticlesSegment($id, $query)
	{
	    return array();
	}
	
	public function getArticlesId($segment, $query)
	{
	    return (int) $segment;
	}
	
	public function getK2itemsSegment($id, $query)
	{
	    return array();
	}
	
	public function getK2itemsId($segment, $query)
	{
	    return (int) $segment;
	}
	
	public function preprocess($query)
	{
	    if ($this->menu->getActive() && !isset($query['Itemid'])) {
	        $query['Itemid'] = $this->menu->getActive()->id;
	    }
	    
	    // Process the parsed variables based on custom defined rules
	    foreach ($this->rules as $rule)
	    {
	        $rule->preprocess($query);
	    }
	    
	    return $query;
	}

}
