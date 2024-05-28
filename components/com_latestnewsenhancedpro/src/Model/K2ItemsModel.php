<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Utilities\ArrayHelper;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;
use SYW\Library\Cache as SYWCache;
use SYW\Library\K2 as SYWK2;
use SYW\Library\Text as SYWText;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Library\Version as SYWVersion;

require_once (JPATH_SITE.'/components/com_k2/helpers/route.php');
require_once (JPATH_SITE.'/components/com_k2/helpers/permissions.php');

/**
 * LatestNewsEnhancedPro model: retrieving a list of K2 items
 */
class K2ItemsModel extends ListModel
{
	protected $categories_string;
	protected $categories_list;

	protected $tags_selection;
	protected $tags_list;

	protected $authors_list;
	protected $alias_list;

	protected $field_filter_ids;
	
	protected $top_filters;
	protected $bottom_filters;

	/**
	 * Method to auto-populate the model state
	 *
	 * Note: calling getState in this method will result in recursion
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 * @return void
	 * @throws \Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

		// load the parameters
		$params = $app->getParams();

		// get pagination request variables

		if ($params->get('pag_type', 'std') == 'lm') {
			$this->setState('ajaxlist.limit', $app->getUserStateFromRequest('global.ajaxlist.limit', 'limit', $params->get('initial_count', 10)));
		} else {
			$pagination_limit_count = intVal($params->get('pag_l_c', ''));
			if ($pagination_limit_count > 0) {
				$limit = $app->input->getInt('limit', $pagination_limit_count);
			} else {
				$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			}
			$this->setState('list.limit', $limit);
		}

		$firstuse = false;
		if ($app->input->getInt('limitstart') === null) {
			$firstuse = true;
		}

		$limitstart = $app->input->getInt('limitstart', 0);
		if ($params->get('pag_type', 'std') == 'lm') {
			$this->setState('ajaxlist.start', $limitstart);
		} else {
			$this->setState('list.start', $limitstart);
		}

		$field_index_array = array();
		$pre_selected_category = 'none';

		$filters = $this->getTopFilters($params);
		if (!empty($filters)) {
			foreach ($filters as $filter) {
				switch ($filter->formfilter) {
				    case 'a': case 'i': case 't': case 'p': break;
					case 'c': $pre_selected_category = $params->get('preselect_cat', 'none'); break;
					default:
						$field_temp = explode(':', $filter->formfilter);
						$field_index_array[] = $field_temp[2];
				}
			}
		}

		$filters = $this->getBottomFilters($params);
		if (!empty($filters)) {
			foreach ($filters as  $filter) {
				switch ($filter->formfilter) {
				    case 'a': case 'i': case 't': case 'p': break;
					case 'c': $pre_selected_category = $params->get('preselect_cat', 'none'); break;
					default:
						$field_temp = explode(':', $filter->formfilter);
						$field_index_array[] = $field_temp[2];
				}
			}
		}

		foreach (array_unique($field_index_array) as $field_index) {
			$field = $app->input->getString('field_' . $field_index, '');
			$this->setState('field_' . $field_index, $field);
		}

		if ($firstuse && $pre_selected_category != 'none') {
			$this->setState('category', (int) $pre_selected_category);
			$app->input->set('category', $pre_selected_category);
		} else {
			$category = $app->input->getInt('category', 0);
			$this->setState('category', $category);
		}

		$tag = $app->input->getInt('tag', 0);
		$this->setState('tag', $tag);

		$author = $app->input->getString('author', '');
		if ($author) {
			$found = false;
			foreach ($this->getAuthorsList($params) as $author_object) {
				if ($author_object->id === $author) {
					$found = true;
				}
			}

			if (!$found) {
				$author = '';
			}
		}
		$this->setState('author', $author);
		
		$alias = $app->input->getString('alias', '');
		if ($alias) {
		    if (!in_array($alias, $this->getAliasList($params))) {
		    	$alias = '';
			}
		}
		$this->setState('alias', $alias);

		$period = $app->input->getString('period', '');
		if ($period) {
		    $period_split = explode('-', $period);
		    if (strlen($period_split[0]) != 4 || (isset($period_split[1]) && (intval($period_split[1]) < 1 || intval($period_split[1]) > 12))) {
		        $period = '';
		    }
		}		
		$this->setState('period', $period);

		$search = $app->input->getString('filter-search', '');
		$this->setState('filter-search', $search);

		$match = $app->input->getString('filter-match', $params->get('search_default', 'any'));
		if (!in_array(strtolower($match), ['all', 'any', 'exact'])) {
		    $match = $params->get('search_default', 'any');
		}
		$this->setState('filter-match', $match);

		$ordering = $app->input->getString('filter_order', '');
		if ($ordering) {
    		$sortable_fields_list = Helper::getSortableFieldsList($params);
    		if (!empty($sortable_fields_list)) {
    		    $sortable_fields = array();
    		    $initial_sort = '';
    		    foreach ($sortable_fields_list as $sortable_field) {
    		        $sortable_fields[] = $sortable_field[0];
    		        if (empty($initial_sort) && $sortable_field[2]) {
    		            $initial_sort = $sortable_field[0]; // the first initial sort is used for b/c with previous behavior
    		        }
    		    }
    		    if (!in_array($ordering, $sortable_fields)) {
    		        $ordering = '';
    		    } else {
					if (empty($ordering)) {
		    			$ordering = $initial_sort;
		    			$app->input->set('filter_order', $initial_sort);
					}
    		    }
    		} else {
    		    $ordering = '';
    		}
		}
		$this->setState('list.ordering', $ordering);

		$direction = $app->input->getString('filter_order_Dir', '');
		if (empty($direction)) {
		    // get initial direction
		    $direction = strtoupper($params->get('initial_sort_dir', 'asc'));
		    $app->input->set('filter_order_Dir', $direction);
		} else if (!in_array(strtoupper($direction), ['ASC', 'DESC', ''])) {
		    $direction = 'ASC';
		}
		$this->setState('list.direction', $direction);
		
		$this->setState('filter.language', Multilanguage::isEnabled() && $params->get('filter_lang', 1));

		$this->setState('params', $params);
	}

	/**
	 * Get all ids of the custom fields used as index filters
	 * @return array of ids
	 */
	protected function getFieldFilterIds()
	{
		if (!isset($this->field_filter_ids)) {

			$this->field_filter_ids = array();

			$params = $this->getState('params');

			$index_filters = array();

			$filters = $this->getTopFilters($params);
			if (!empty($filters)) {
				foreach ($filters as $filter) {
					$index_filters[] = $filter->formfilter;
				}
			}

			$filters = $this->getBottomFilters($params);
			if (!empty($filters)) {
				foreach ($filters as  $filter) {
				    $index_filters[] = $filter->formfilter;
				}
			}

			$index_filters = array_unique($index_filters);

			foreach ($index_filters as $index_filter) {
				if (strpos($index_filter, 'field') !== false) {
					$field_temp = explode(':', $index_filter);
					$this->field_filter_ids[] = $field_temp[2];
				}
			}
		}
		return $this->field_filter_ids;
	}

	/**
	 * Build an SQL query to load the list data
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
	 */
	protected function getListQuery()
	{
		if ($this->getTagsSelection() === false) {
			return ''; // case where there are no article associated tags and 'all' tags was selected
		}

		$app = Factory::getApplication();

		$db = $this->getDbo(); // 4.2+ $this->getDatabase();
		$query = $db->getQuery(true);

		$user = Factory::getUser(); // 4.2+ $this->getCurrentUser();
		$view_levels = $user->getAuthorisedViewLevels();

		$nowDate = $db->quote(Factory::getDate()->toSql());

		$params = $this->getState('params');

		$leading_head_type = $params->get('leading_head_type', 'none');
		$head_type = $params->get('head_type', 'none');

		$subquery1 = ' CASE WHEN ';
		$subquery1 .= $query->charLength('a.alias');
		$subquery1 .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$subquery1 .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$subquery1 .= ' ELSE ';
		$subquery1 .= $a_id.' END AS slug';

		$subquery2 = ' CASE WHEN ';
		$subquery2 .= $query->charLength('c.alias');
		$subquery2 .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$subquery2 .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$subquery2 .= ' ELSE ';
		$subquery2 .= $c_id.' END AS cat_slug';
		
		$fulltext_query = false;
		
		if ($params->get('leading_search_fulltext', 0)) {
		    if ($leading_head_type == 'image' || $leading_head_type == 'allimagesasc' || $leading_head_type == 'allimagesdesc') {
		        $fulltext_query = true;
		    }
		}
		
		if ($params->get('search_fulltext', 0)) {
		    if ($head_type == 'image' || $head_type == 'allimagesasc' || $head_type == 'allimagesdesc') {
		        $fulltext_query = true;
		    }
		}
		
		if ($fulltext_query) {
		    $query->select($db->quoteName('a.fulltext'));
		}

		$query->select($db->quoteName(array('a.id', 'a.catid', 'a.title', 'a.alias', 'a.introtext', 'a.extra_fields', 'a.params', 'a.metadata', 'a.metakey', 'a.metadesc', 'a.access', 'a.hits', 'a.featured', 'a.language')));
		$query->select($db->quoteName('a.published', 'state'));

		$query->select('CASE WHEN ' . $db->quoteName('a.fulltext') . ' IS NULL OR ' . $db->quoteName('a.fulltext') . ' = ' . $db->quote('') . ' THEN 0 ELSE 1 END AS ' . $db->quoteName('fulltexthascontent'));

		$query->select($db->quoteName(array('a.checked_out', 'a.checked_out_time', 'a.created', 'a.created_by', 'a.created_by_alias')));

		// Use created if modified is 0
		$query->select('CASE WHEN ' . $query->isNullDatetime('a.modified') . ' THEN ' . $db->quoteName('a.created') . ' ELSE ' . $db->quoteName('a.modified') . ' END AS ' . $db->quoteName('modified'));
		$query->select($db->quoteName(array('a.modified_by', 'uam.name'), array('modified_by', 'modified_by_name')));

		// Use created if publish_up is 0
		$query->select('CASE WHEN ' . $query->isNullDatetime('a.publish_up') . ' THEN ' . $db->quoteName('a.created') . ' ELSE ' . $db->quoteName('a.publish_up') . ' END AS  ' . $db->quoteName('publish_up'));
		$query->select($db->quoteName('a.publish_down'));

		$query->select($subquery1);
		$query->select($subquery2);

		$query->from($db->quoteName('#__k2_items', 'a'));

		// join over the categories
		$query->select($db->quoteName(array('c.name', 'c.access', 'c.alias'), array('category_title', 'category_access', 'category_alias'))); // TODO check: was cat_alias originally: error ?
		$query->join('LEFT', $db->quoteName('#__k2_categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

		$query->where($db->quoteName('a.trash') . ' = 0');
		$query->where($db->quoteName('c.published') . ' = 1');
		$query->where($db->quoteName('c.trash') . ' = 0');

		// join over the users for the author and modified_by names
		switch ($params->get('show_a', 'alias')) {
		    case 'full': $query->select($db->quoteName('ua.name', 'author')); break;
		    case 'user': $query->select($db->quoteName('ua.username', 'author')); break;
			default: $query->select('CASE WHEN ' . $db->quoteName('a.created_by_alias') . ' > ' . $db->quote(' ') . ' THEN ' . $db->quoteName('a.created_by_alias') . ' ELSE ' . $db->quoteName('ua.name') . ' END AS ' . $db->quoteName('author'));
		}

		$query->select($db->quoteName('ua.email', 'author_email'));

		$query->join('LEFT', $db->quoteName('#__users', 'ua'), $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by'));
		$query->join('LEFT', $db->quoteName('#__users', 'uam'), $db->quoteName('uam.id') . ' = ' . $db->quoteName('a.modified_by'));

		if ($leading_head_type == 'authork2user' || $head_type == 'authork2user') {
		    $query->select($db->quoteName('k2users.image', 'author_avatar'));
		    $query->join('LEFT', $db->quoteName('#__k2_users', 'k2users'), $db->quoteName('k2users.userID') . ' = ' . $db->quoteName('a.created_by'));
		}

		// search filter

		$search_content = $app->input->getString('filter-search', '') ? urldecode($app->input->getString('filter-search')) : $this->getState('filter-search');
		$search_content = trim($search_content);
		if ($search_content) {

			$match = $app->input->getString('filter-match', 'any') ? $app->input->getString('filter-match') : $this->getState('filter-match');

			$search_content_array = array();
			if ($match !== 'exact') {
				$search_content_array = array_unique(explode(" ", $db->escape($search_content)));
			} else {
				$search_content_array[] = $db->escape($search_content);
			}

			$match_condition = 'OR';
			if ($match === 'all') {
				$match_condition = 'AND';
			}
			
			$query_search = '';
			
			$query_search_array = array();
			foreach ($search_content_array as $search_content) {
			    $query_search_array[] = $db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $db->escape($search_content, true) . '%');
			}
			$query_search .= '(' . implode(' ' . $match_condition . ' ', $query_search_array) . ')';

		    //$query_search = '((a.title LIKE "%'.implode('%" ' . $match_condition . ' a.title LIKE "%', $search_content_array).'%")';

		    // look through intro text (but what if truncated ?), category, author
		    $field_datanames = array('ua.name', 'ua.username');

		    $look_thru = $params->get('look_thru', '');
		    if (is_array($look_thru)) {
		        foreach ($look_thru as $choice) {
		            switch ($choice) {
		                case 'introtext' : $field_datanames[] = 'a.introtext'; break;
		                case 'category' : $field_datanames[] = 'c.name'; break;
		            }
		        }
		    }

		    foreach ($field_datanames as $field_dataname) {
		        $query_search_array = array();
		        foreach ($search_content_array as $search_content) {
		            $query_search_array[] = $db->quoteName($field_dataname) . ' LIKE ' . $db->quote('%' . $db->escape($search_content, true) . '%');
		        }
		        $query_search .= ' OR (' . implode(' ' . $match_condition . ' ', $query_search_array) . ')';
		        
		        //$query_search .= ' OR ('.$field_dataname.' LIKE "%'.implode('%" ' . $match_condition . ' '.$field_dataname.' LIKE "%', $search_content_array).'%")';
		    }

		    // TODO efficient way to filter through the K2 extra field data?
		    // info is stored in extra_fields column of k2_items

		    $query->where('(' . $query_search . ')');

		    $this->setState('filter-search', $search_content);
		}

		// K2 extra filters

		$field_index_array = $this->getFieldFilterIds();

		if (!empty($field_index_array)) {

			// TODO efficient way to filter through the K2 extra field data?
			// info is stored in extra_fields column of k2_items
		}

		// access filter

		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));

		$show_unauthorized_items = $params->get('show_unauthorized', 0);

		if (!$show_unauthorized_items) { // show authorized items only
		    $query->whereIn($db->quoteName('a.access'), $view_levels);
		    $query->whereIn($db->quoteName('c.access'), $view_levels);
		}

		// filter by start and end dates

		$postdate = $params->get('post_d', 'published');

		if ($postdate != 'fin_pen' && $postdate != 'pending') {
		    $query->where('(' . $query->isNullDatetime('a.publish_up') . ' OR ' . $db->quoteName('a.publish_up') . ' <= ' . $nowDate . ')');
		}
		if ($postdate == 'pending') {
			$query->where($db->quoteName('a.publish_up') . ' > ' . $nowDate);
		}
		$query->where('(' . $query->isNullDatetime('a.publish_down') . ' OR ' . $db->quoteName('a.publish_down') . ' >= ' . $nowDate . ')');

		// filter by date range

		switch ($postdate)
		{
			case 'created' : $dateField = 'a.created'; break;
			case 'modified' : $dateField = 'a.modified'; break;
			case 'finished' : case 'fin_pen' : /*case 'pending' :*/ $dateField = 'a.publish_down'; break;
			default: $dateField = 'a.publish_up';
		}

		$query->select($db->quoteName($dateField, 'date'));
		$query->select('YEAR(' . $db->quoteName($dateField) . ') AS year');
		$query->select('MONTH(' . $db->quoteName($dateField) . ') AS month');

		if ($this->getState('period')) {

			$period_array = explode('-', $this->getState('period'));

			$query->where('YEAR(' . $db->quoteName($dateField) . ') = ' . $period_array[0]);
			if (isset($period_array[1])) {
			    $query->where('MONTH(' . $db->quoteName($dateField) . ') = ' . $period_array[1]);
			}

		} else {

			switch ($params->get('use_range', 0))
			{
			    case 1: // relative

			        // parameters are reversed (backward compatibility from early on version)

			        $range_from = $params->get('range_to', 'week'); // now, day, week, month, year options
			        $spread_from = $params->get('spread_to', 1);
			        $range_to = $params->get('range_from', 'now');
			        $spread_to = $params->get('spread_from', 1);

			        // test range 'from' and 'to' to see if it will be a future or a past range

			        $from = 0;
			        switch($range_from)
			        {
			            case 'day': $from += $spread_from; break;
			            case 'week': $from += $spread_from * 7; break;
			            case 'month': $from += $spread_from * 30; break; // arbitrary
			            case 'year': $from += $spread_from * 365; break; // arbitrary
			        }

			        $to = 0;
			        switch($range_to)
			        {
			            case 'day': $to += $spread_to; break;
			            case 'week': $to += $spread_to * 7; break;
			            case 'month': $to += $spread_to * 30; break; // arbitrary
			            case 'year': $to += $spread_to * 365; break; // arbitrary
			        }

			        if ($from < 0 && $to < 0 && $from <= $to) {
			        	// dates in the past (-3 to -2 months for instance)
			            $query->where($db->quoteName($dateField) . ' >= DATE_SUB(' . $nowDate . ', INTERVAL ' . abs($spread_from) . ' ' . $range_from . ')');
			            $query->where($db->quoteName($dateField) . ' <= DATE_SUB(' . $nowDate . ', INTERVAL ' . abs($spread_to) . ' ' . $range_to . ')');
			        }

			        if ($from < 0 && $to == 0) {
			        	// dates in the past (the last 2 months for instance)
			            $query->where($db->quoteName($dateField) . ' >= DATE_SUB(' . $nowDate . ', INTERVAL ' . abs($spread_from) . ' ' . $range_from . ')');
			            $query->where($db->quoteName($dateField) . ' <= ' . $nowDate);
			        }

			        if ($from < 0 && $to > 0) {
			        	// dates in the past and in the future (the last month to the next 2 months for instance)
			            $query->where($db->quoteName($dateField) . ' >= DATE_SUB(' . $nowDate . ', INTERVAL ' . abs($spread_from) . ' ' . $range_from . ')');
			            $query->where($db->quoteName($dateField) . ' <= DATE_ADD(' . $nowDate . ', INTERVAL ' . $spread_to . ' ' . $range_to . ')');
			        }

			        if ($from >= 0 && $to >= 0) {
			        	if ($from > $to) {
			        		// past dates
			        	    $query->where($db->quoteName($dateField) . ' >= DATE_SUB(' . $nowDate . ', INTERVAL ' . $spread_from . ' ' . $range_from . ')');
			        		if ($to == 0) {
			        		    $query->where($db->quoteName($dateField) . ' <= ' . $nowDate);
			        		} else {
			        		    $query->where($db->quoteName($dateField) . ' <= DATE_SUB(' . $nowDate . ', INTERVAL ' . $spread_to . ' ' . $range_to . ')');
			        		}
			        	} elseif ($from < $to) {
			        		// future dates
			        	    $query->where($db->quoteName($dateField) . ' <= DATE_ADD(' . $nowDate . ', INTERVAL ' . $spread_to . ' ' . $range_to . ')');
			        		if ($from == 0) {
			        		    $query->where($db->quoteName($dateField) . ' >= ' . $nowDate);
			        		} else {
			        		    $query->where($db->quoteName($dateField) . ' >= DATE_ADD(' . $nowDate . ', INTERVAL ' . $spread_from . ' ' . $range_from . ')');
			        		}
			        	} else {
			        		// $from and $to are equal
			        		if ($to == 0) {
			        		    $query->where($db->quoteName($dateField) . ' = ' . $nowDate);
			        		} else {
			        		    $query->where($db->quoteName($dateField) . ' = DATE_ADD(' . $nowDate . ', INTERVAL ' . $spread_from . ' ' . $range_from . ')');
			        		}
			        	}
			        }

					break;

				case 2: // range
					$startDateRange = $db->quote($params->get('start_date_range', $db->getNullDate()));
					$endDateRange = $db->quote($params->get('end_date_range', $db->getNullDate()));

					$query->where('(' . $db->quoteName($dateField) . ' >= ' . $startDateRange . ' AND ' . $db->quoteName($dateField) . ' <= ' . $endDateRange . ')');
					break;
			}
		}

		// category filter

		if ($this->getState('category') > 0) {
		    $category_id = $this->getState('category');
			$query->where($db->quoteName('a.catid') . ' = :categoryId');
			$query->bind(':categoryId', $category_id, ParameterType::INTEGER);
		} else {
			if ($this->getCategoriesString() != '') {
				$test_type = $params->get('cat_inex', 1) ? 'IN' : 'NOT IN';
				$query->where($db->quoteName('a.catid') . ' ' . $test_type . ' (' . $this->getCategoriesString() . ')');
			}
		}

		// metakeys filter

		$metakeys = array();
		$keys = array_filter(explode(',', trim($params->get('keys', ''), ' ,')));
		
		// assemble any non-blank word(s)
		foreach ($keys as $key) {
			$metakeys[] = trim($key);
		}

		if (!empty($metakeys)) {
			$concat_string = $query->concatenate(array('","', ' REPLACE(a.metakey, ", ", ",")', ' ","')); // remove single space after commas in keywords
			
			//$query->where('('.$concat_string.' LIKE "%'.implode('%" OR '.$concat_string.' LIKE "%', $metakeys).'%")');
			
			$query_meta_array = array();
			foreach ($metakeys as $key) {
			    $query_meta_array[] = $concat_string . ' LIKE ' . $db->quote('%' . $db->escape($key, true) . '%');
			}
			
			$query->where('(' . implode(' OR ', $query_meta_array) . ')');
		}

		// tags filter

		if ($this->getTagsSelection() || $this->getState('tag')) {

		    $query->select('COUNT(' . $db->quoteName('tags.id') . ') AS tags_count');
			$query->join('LEFT', $db->quoteName('#__k2_tags_xref', 'tags_xref'), $db->quoteName('tags_xref.itemID') . ' = ' . $db->quoteName('a.id'));
			$query->join('LEFT', $db->quoteName('#__k2_tags', 'tags'), $db->quoteName('tags_xref.tagID') . ' = ' . $db->quoteName('tags.id'));
			// no access in database table
			$query->where($db->quoteName('tags.published') . ' = 1');

			if ($this->getState('tag') > 0) {
			    $tag_id = $this->getState('tag');
				$query->where($db->quoteName('tags.id') . ' = :tagId');
				$query->bind(':tagId', $tag_id, ParameterType::INTEGER);
			} else {
				$tags_to_match = implode(',', $this->getTagsSelection());

				$test_type = $params->get('tags_inex', 1) ? 'IN' : 'NOT IN';
				$query->where($db->quoteName('tags.id') . ' ' . $test_type . ' (' . $tags_to_match . ')');

				if (!$params->get('tags_inex', 1)) { // EXCLUDE TAGS (does not work when 'all')
					$query->select('tags_per_items.tag_count_per_item');

				    $subquery = $db->getQuery(true);

				    // subquery gets all the tags for all items
				    $subquery->select($db->quoteName('ttags_xref.itemID', 'content_id'));
				    $subquery->select('COUNT(' . $db->quoteName('tt.id') . ') AS tag_count_per_item');
				    $subquery->from($db->quoteName('#__k2_tags_xref', 'ttags_xref'));
				    $subquery->join('LEFT', $db->quoteName('#__k2_tags', 'tt'), $db->quoteName('ttags_xref.tagID') . ' = ' . $db->quoteName('tt.id'));
				    $subquery->where($db->quoteName('tt.published') . ' = 1');
				    $subquery->group($db->quoteName('content_id'));

					$query->join('INNER', '(' . (string) $subquery . ') AS tags_per_items', $db->quoteName('tags_per_items.content_id') . ' = ' . $db->quoteName('a.id'));

					// we keep items that have the same amount of tags before and after removals
					$query->having('COUNT(' . $db->quoteName('tags.id') . ') = ' . $db->quoteName('tags_per_items.tag_count_per_item'));

				} else { // INCLUDE TAGS
					if ($params->get('tags_match', 'any') == 'all') {
						$query->having('COUNT(' . $db->quoteName('tags.id') . ') = ' . count($this->getTagsSelection()));
					}
				}
			}

			$query->group($db->quoteName('a.id'));
		}

		// user filter

		$where_state = 'a.published = 1';
		$where_createdby = '';

		if ($this->getState('author')) {
		    $where_createdby = 'a.created_by ='.$this->getState('author');
			if ($params->get('allow_edit', 0) && (int)$user->get('id') > 0) {
				// logged user can see his own unpublished items only
			    $where_state = '(a.published = 1) OR (a.published = 0 AND a.created_by = ' . (int) $user->get('id') . ')';
			}
		} else {
			$include = $params->get('author_inex', 1);
			$authors_array = $params->get('created_by', array());

			$array_of_authors_values = array_count_values($authors_array);
			if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected
			    $test_type = $include ? '>' : '<';
			    $where_createdby = 'a.created_by ' . $test_type . ' 0'; // necessary so that the OR match returns good results
			    if ($params->get('allow_edit', 0) && (int)$user->get('id') > 0) {
					// logged user can see his own unpublished items only
			        $where_state = '(a.published = 1) OR (a.published = 0 AND a.created_by = ' . (int) $user->get('id') . ')';
				}
			} else if (isset($array_of_authors_values['auto']) && $array_of_authors_values['auto'] > 0) { // 'auto' was selected
				$test_type = $include ? '=' : '<>';
				$where_createdby = 'a.created_by ' .$test_type.' '.(int) $user->get('id');
				if ($include && $params->get('allow_edit', 0) && (int)$user->get('id') > 0) {
				    $where_state = 'a.published IN (0, 1)'; // show all items for the logged author, published or not
				}
			} else {
				$authors = implode(',', $authors_array);
				if ($authors) {
					$test_type = $include ? 'IN' : 'NOT IN';
					$where_createdby = 'a.created_by '.$test_type.' ('.$authors.')';
				}

				if ($params->get('allow_edit', 0) && (int)$user->get('id') > 0) {
				    if (($include && in_array($user->get('id'), $authors_array)) || (!$include && !in_array($user->get('id'), $authors_array))) {
						// logged user can see his own unpublished items only
						$where_state = '(a.published = 1) OR (a.published = 0 AND a.created_by = ' . (int) $user->get('id') . ')';
					}
				}
			}
		}

		// author alias filter
		
		$include = $params->get('author_alias_inex', 1);
		$authors_array = $params->get('created_by_alias', array());
		$author_match = (int)$params->get('author_match', 0);

		$where_createdbyalias = '';

		if ($this->getState('alias')) {
		    $where_createdbyalias = 'a.created_by_alias = ' . $db->quote($this->getState('alias'));
		} else if ($author_match > 0 && count($authors_array) > 0) {

		    $array_of_authors_values = array_count_values($authors_array);
		    
		    if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected
		        
		        if ($include) {
		            $where_createdbyalias = 'a.created_by_alias != ' . $db->quote('');
		        } else {
		            $where_createdbyalias = 'a.created_by_alias = ' . $db->quote('');
		        }
		        
		    } else {
		        $quoted_array = array();
		        foreach ($authors_array as $author) {
		            $quoted_array[] = $db->quote($author);
		        }
		        
		        $authors = implode(',', $quoted_array);
		        if ($authors) {
		            $test_type = $include ? 'IN' : 'NOT IN';
		            $where_createdbyalias = 'a.created_by_alias '.$test_type.' ('.$authors.')';
		            if (!$include) {
		                $where_createdbyalias .= ' AND a.created_by_alias != ' . $db->quote('');
		            }
		        }
		    }
		}

		$query->where($where_state);

		if ($author_match === 2 && !empty($where_createdby) && !empty($where_createdbyalias)) {
		    $query->where('(' . $where_createdby . ' OR ' . '(' . $where_createdbyalias . ')' . ')');
		} else {
		    if ($where_createdby) {
		        $query->where($where_createdby);
		    }
		    if ($where_createdbyalias) {
		        $query->where('(' . $where_createdbyalias . ')');
		    }
		}

		// language filter

		if ($this->getState('filter.language')) {
		    $query->whereIn($db->quoteName('a.language'), [Factory::getLanguage()->getTag(), '*'], ParameterType::STRING);
		}

		$ordering = array();

		// author order

		switch ($params->get('author_order', ''))
		{
		    case 'selec_asc': $ordering[] = $db->quoteName('author') . ' ASC'; break;
		    case 'selec_dsc': $ordering[] = $db->quoteName('author') . ' DESC'; break;
		}

		// featured switch

		$featured = false;
		$featured_only = false;
		switch ($params->get('show_f', 3))
		{
			case '1': // only
				$featured = true;
				$featured_only = true;
				$query->where($db->quoteName('a.featured') . ' = 1');
				break;
			case '0': // hide
			    $query->where($db->quoteName('a.featured') . ' = 0');
				break;
			case '2': // first the featured ones
				$featured = true;
				$ordering[] = $db->quoteName('a.featured') . ' DESC';
				break;
			default: // no discrimination between featured/unfeatured items
				$featured = true;
				break;
		}

		// category order

		if (!$featured_only) {
		    if ($this->getCategoryOrderQuery() != '') {
		        $ordering[] = $this->getCategoryOrderQuery();
		    }
		}

		// general ordering

		switch ($params->get('order'))
		{
			case 'o_asc': 
			    if ($featured) { 
			        $ordering[] = 'CASE WHEN ' . $db->quoteName('a.featured') . ' = 1 THEN ' . $db->quoteName('a.featured_ordering') . ' ELSE ' . $db->quoteName('a.ordering') . ' END ASC';
			    } else { 
			        $ordering[] = $db->quoteName('a.ordering') . ' ASC';
			    } 
			    break;
			case 'o_dsc': 
			    if ($featured) { 
			        $ordering[] = 'CASE WHEN ' . $db->quoteName('a.featured') . ' = 1 THEN ' . $db->quoteName('a.featured_ordering') . ' ELSE ' . $db->quoteName('a.ordering') . ' END DESC';
			    } else { 
			        $ordering[] = $db->quoteName('a.ordering') . ' DESC';
			    } 
			    break;
			case 'p_asc': $ordering[] = $db->quoteName('a.publish_up') . ' ASC'; break;
			case 'p_dsc': $ordering[] = $db->quoteName('a.publish_up') . ' DESC'; break;
			case 'f_asc': $ordering[] = 'CASE WHEN ' . $db->quoteName('a.publish_down') . ' IS NULL THEN ' . $db->quoteName('a.publish_up') . ' ELSE ' . $db->quoteName('a.publish_down') . ' END ASC'; break;
			case 'f_dsc': $ordering[] = 'CASE WHEN ' . $db->quoteName('a.publish_down') . ' IS NULL THEN ' . $db->quoteName('a.publish_up') . ' ELSE ' . $db->quoteName('a.publish_down') . ' END DESC'; break;
			case 'm_asc': $ordering[] = $db->quoteName('a.modified') . ' ASC'; $ordering[] = $db->quoteName('a.created') . ' ASC'; break;
			case 'm_dsc': $ordering[] = $db->quoteName('a.modified') . ' DESC'; $ordering[] = $db->quoteName('a.created') . ' DESC'; break;
			case 'c_asc': $ordering[] = $db->quoteName('a.created') . ' ASC'; break;
			case 'c_dsc': $ordering[] = $db->quoteName('a.created') . ' DESC'; break;
			case 'mc_asc': $ordering[] = 'CASE WHEN ' . $db->quoteName('a.modified') . ' IS NULL THEN ' . $db->quoteName('a.created') . ' ELSE ' . $db->quoteName('a.modified') . ' END ASC'; break;
			case 'mc_dsc': $ordering[] = 'CASE WHEN ' . $db->quoteName('a.modified') . ' IS NULL THEN ' . $db->quoteName('a.created') . ' ELSE ' . $db->quoteName('a.modified') . ' END DESC'; break;
			case 'random': $ordering[] = $query->rand(); break;
			case 'hit': $ordering[] = $db->quoteName('a.hits') . ' DESC'; break;
			case 'title_asc': $ordering[] = $db->quoteName('a.title') . ' ASC'; break;
			case 'title_dsc': $ordering[] = $db->quoteName('a.title') . ' DESC'; break;
			case 'manual':
				$articles_to_include = array_filter(explode(',', trim($params->get('in', ''), ' ,')));
				if (!empty($articles_to_include)) {
					$manual_ordering = 'CASE a.id';
					foreach ($articles_to_include as $key => $id) {
					    $manual_ordering .= ' WHEN ' . $id . ' THEN ' . $key;
					}
					$ordering[] = $manual_ordering . ' ELSE 999 END, a.id'; // 'FIELD(a.id, ' . $articles_to_include . ')' is MySQL specific
				}
		}

		if ($this->getState('list.ordering')) { // TODO sort thru K2 extra fields
		    if ($this->getState('list.ordering') === 'rating') {
		        // no rating sort
		    } else if ($this->getState('list.ordering') === 'category') {
		        $query->order($db->quoteName('c.name') . ' ' . $this->getState('list.direction'));
			} else {
		        $query->order($this->getState('list.ordering') . ' ' . $this->getState('list.direction'));
		    }
		} else {
			if (count($ordering) > 0) {
				$query->order($ordering);
			}
		}

		// include only

		$articles_to_include = array_filter(explode(',', trim($params->get('in', ''), ' ,')));
		if (!empty($articles_to_include)) {
			$articles_to_include = ArrayHelper::toInteger($articles_to_include);
			$query->whereIn($db->quoteName('a.id'), $articles_to_include);
		}

		// exclude

		$articles_to_exclude = array_filter(explode(',', trim($params->get('ex', ''), ' ,')));
		if (!empty($articles_to_exclude)) {
			$articles_to_exclude = ArrayHelper::toInteger($articles_to_exclude);
			$query->whereNotIn($db->quoteName('a.id'), $articles_to_exclude);
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure
	 */
	public function getItems()
	{
		// Get a storage key
		$store = $this->getStoreId();

		// Try to load the data from internal storage
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$params = $this->getState('params');

		$items = null;
		try {
			$items_query = $this->_getListQuery();

			if (empty($items_query)) {
				$items = array();
			} else {
				if ($params->get('pag_type', 'std') == 'lm') {
					$items = $this->_getList($items_query, $this->getStart(), $this->getState('ajaxlist.limit'));
				} else {
					$items = $this->_getList($items_query, $this->getStart(), $this->getState('list.limit'));
				}
			}
		} catch (ExecutionFailureException $e) {
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return null;
		}

		if (!empty($items)) {

			$app = Factory::getApplication();
			$prefix_id = $app->input->get('view').'_'.$app->input->get('layout', 'blog');
			$menus = $app->getMenu('site');
			if ($menus->getActive()) {
				$prefix_id .= '_'.$menus->getActive()->id;
			}

			//$leading_count = ($params->get('pag_type', 'std') == 'lm' && $this->getState('ajaxlist.start') > 0) ? 0 : $params->get('leading_count', 0);
			$leading_count = $params->get('leading_count', 0);

			$leading_head_type = $params->get('leading_head_type', 'none');
			$leading_head_width = $params->get('leading_head_w', 128);
			$leading_head_height = $params->get('leading_head_h', 128);

			$leading_search_fulltext = $params->get('leading_search_fulltext', 0);

			$head_type = $params->get('head_type', 'none');
			$head_width = $params->get('head_w', 64);
			$head_height = $params->get('head_h', 64);

			$search_fulltext = $params->get('search_fulltext', 0);

			// image

			$image_types = array('image', 'imageintro', 'authorcontact', 'authork2user', 'allimagesasc', 'allimagesdesc');

			$default_picture = trim($params->get('default_pic', ''));

			$crop_picture = ($params->get('crop_pic', 0) && $params->get('create_thumb', 1));
			$create_highres_images = $params->get('create_highres', false);
			$lazyload = $params->get('lazyload', false);

			$allow_remote = $params->get('allow_remote', true);

			$thumbnail_mime_type = $params->get('thumb_mime_type', '');

			$border_width = $params->get('border_w', 0);

			$filter = $params->get('filter', 'none');

			$quality_jpg = $params->get('quality_jpg', 75);
			$quality_png = $params->get('quality_png', 3);
			$quality_webp = $params->get('quality_webp', 80);
			$quality_avif = $params->get('quality_avif', 80);

			if ($quality_jpg > 100) {
				$quality_jpg = 100;
			}
			if ($quality_jpg < 0) {
				$quality_jpg = 0;
			}

			if ($quality_png > 9) {
				$quality_png = 9;
			}
			if ($quality_png < 0) {
				$quality_png = 0;
			}

			if ($quality_webp > 100) {
				$quality_webp = 100;
			}
			if ($quality_webp < 0) {
				$quality_webp = 0;
			}

			if ($quality_avif > 100) {
			    $quality_avif = 100;
			}
			if ($quality_avif < 0) {
			    $quality_avif = 0;
			}

			$image_qualities = array('jpg' => $quality_jpg, 'png' => $quality_png, 'webp' => $quality_webp, 'avif' => $quality_avif);

			$clear_cache = Helper::IsClearPictureCache($params);

			$subdirectory = 'thumbnails/lnep';
			if ($params->get('thumb_path', 'cache') == 'cache') {
				$subdirectory = 'com_latestnewsenhancedpro';
			}
			$tmp_path = SYWCache::getTmpPath($params->get('thumb_path', 'cache'), $subdirectory);

			if ($clear_cache) {
// 				HelpersLatestNewsEnhancedPro::clearThumbnails($module->id, $tmp_path);

			    SYWVersion::refreshMediaVersion('com_latestnewsenhancedpro_' . $prefix_id);
			}

			// link

			$link_to = $params->get('link_to', 'item');
			$link_defaults_to_item = $params->get('link_to_default', 0);
			switch ($params->get('link_target', 'default')) {
				case 'same': $link_target = ''; break;
				case 'new': $link_target = 1; break;
				case 'modal': $link_target = 3; break;
				case 'popup': $link_target = 2; break;
				default: $link_target = 'default';
			}

			$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
			$show_unauthorized_items = $params->get('show_unauthorized', 0);

			$always_show_readmore = $params->get('readmore_always_show', true);

			// title

			$leading_title_type = $params->get('leading_title_type', 'title');
			$leading_title_letter_count = trim($params->get('leading_letter_count_title', ''));
			$leading_force_one_line = $params->get('leading_force_one_line', false);
			$leading_title_truncate_last_word = $params->get('leading_trunc_l_w_title', false);

			$title_type = $params->get('title_type', 'title');
			$title_letter_count = trim($params->get('letter_count_title', ''));
			$force_one_line = $params->get('force_one_line', false);
			$title_truncate_last_word = $params->get('trunc_l_w_title', false);

			// text

			$text_type = $params->get('text', 'intro');
			$show_unauthorized_text = $params->get('s_unauthorized_text', 1);
			$unauthorized_text = trim($params->get('unauthorized_text', ''));

			$leading_number_of_letters = -1;
			$leading_letter_count = trim($params->get('leading_l_count', ''));
			if ($leading_letter_count != '') {
				$leading_number_of_letters = (int) $leading_letter_count;
			}
			$leading_truncate_last_word = $params->get('leading_trunc_l_w', false);

			$number_of_letters = -1;
			$letter_count = trim($params->get('l_count', ''));
			if ($letter_count != '') {
				$number_of_letters = (int) $letter_count;
			}
			$truncate_last_word = $params->get('trunc_l_w', false);

			$beacon = '';
			if (!$always_show_readmore) {
				$beacon = '^';
			}

			$leading_strip_tags = $params->get('leading_strip_tags', 1);
			$leading_keep_tags = trim($params->get('leading_keep_tags', ''));
			$leading_trigger_contentprepare = $params->get('leading_trigger_events', false);

			$strip_tags = $params->get('strip_tags', 1);
			$keep_tags = trim($params->get('keep_tags', ''));
			$trigger_contentprepare = $params->get('trigger_events', false);

			// category

			$cat_link_type = $params->get('link_cat_to', 'none');
			$cat_view = $params->get('cat_views', '');

			//

			// keep it to allow users to add their information types for rating
			$requireVoteData = true; // Helper::isInfoTypeRequired('rating', $params);

			//

			$db = $this->getDbo();

			$item_count = $app->getUserState('global.ajaxlist.itemcount', 0);

			foreach ($items as $item) {

			    if ($item_count < $leading_count) { // leading items

					$item_prefix_id = $prefix_id.'_lead';

					$item_head_type = $leading_head_type;

					if (in_array($leading_head_type, $image_types) || substr($leading_head_type, 0, strlen('k2field:image')) === 'k2field:image') {
						$item_head_width = $leading_head_width - $border_width * 2;
						$item_head_height = $leading_head_height - $border_width * 2;
					} else {
						$item_head_width = $leading_head_width;
						$item_head_height = $leading_head_height;
					}

					$item_search_fulltext = $leading_search_fulltext;

					$item_title_type = $leading_title_type;
					$item_force_one_line = $leading_force_one_line;
					$item_title_letter_count = $leading_title_letter_count;
					$item_title_truncate_last_word = $leading_title_truncate_last_word;

					$item_number_of_letters = $leading_number_of_letters;
					$item_truncate_last_word = $leading_truncate_last_word;

					$item_strip_tags = $leading_strip_tags;
					$item_keep_tags = $leading_keep_tags;
					$item_trigger_contentprepare = $leading_trigger_contentprepare;

					if ($leading_head_type == 'authorcontact') {
						$contact = $this->getContactData($item->created_by);
						if ($contact) {
							$item->author_image = $contact->image;
						}
					}

				} else {

					$item_prefix_id = $prefix_id;

					$item_head_type = $head_type;

					if (in_array($head_type, $image_types) || substr($head_type, 0, strlen('k2field:image')) === 'k2field:image') {
						$item_head_width = $head_width - $border_width * 2;
						$item_head_height = $head_height - $border_width * 2;
					} else {
						$item_head_width = $head_width;
						$item_head_height = $head_height;
					}

					$item_search_fulltext = $search_fulltext;

					$item_title_type = $title_type;
					$item_force_one_line = $force_one_line;
					$item_title_letter_count = $title_letter_count;
					$item_title_truncate_last_word = $title_truncate_last_word;

					$item_number_of_letters = $number_of_letters;
					$item_truncate_last_word = $truncate_last_word;

					$item_strip_tags = $strip_tags;
					$item_keep_tags = $keep_tags;
					$item_trigger_contentprepare = $trigger_contentprepare;

					if ($head_type == 'authorcontact') {
						$contact = $this->getContactData($item->created_by);
						if ($contact) {
							$item->author_image = $contact->image;
						}
					}
				}

				$item_count++;

				$item_extra_fields = array();
				if ($item->extra_fields) {
				    $item_extra_fields = json_decode($item->extra_fields);
				}

				// category link

				$item->catlink = '';

				$link_temp = '';
				if ($cat_link_type == 'view') {
					if ($cat_view) {
						$link_temp = 'index.php?option=com_latestnewsenhancedpro&view=k2items&Itemid=' . $cat_view . '&category=' . $item->catid;
					}
				} else if ($cat_link_type == 'standard') {
					$link_temp = K2HelperRoute::getCategoryRoute($item->cat_slug);
				}

				if (!$show_unauthorized_items || in_array($item->category_access, $authorised)) {
					if ($link_temp) {
						$item->catlink = urldecode(Route::_($link_temp));
					}

					$item->category_authorized = true;
				} else {
					if ($link_temp) {
						$catlink = new Uri(Route::_('index.php?option=com_users&view=login', false));
						$catlink->setVar('return', base64_encode($link_temp));
						$item->catlink = $catlink;
					}

					$item->category_authorized = false;
				}

				// title

				if (substr($item_title_type, 0, strlen('k2field:textfield')) === 'k2field:textfield') {

				    $type_temp = explode(':', $item_title_type); // $title_type can be k2field:textfield:fieldid

				    $newtitle = '';

				    if ($item_extra_fields) {
				        foreach ($item_extra_fields as $item_extra_field) {
				            if ($item_extra_field->id == $type_temp[2]) {
				                $newtitle = $item_extra_field->value;
				            }
				        }
				    }

				    if (empty($newtitle)) { // get the default value
				        $query = $db->getQuery(true);

				        $query->select($db->quoteName('value'));
				        $query->from($db->quoteName('#__k2_extra_fields'));
				        $query->where($db->quoteName('id').' = '.$type_temp[2]);

				        $db->setQuery($query);

				        try {
				            $results = $db->loadResult();
				            $results = json_decode($results);
				            $newtitle = $results[0]->value;
				        } catch (ExecutionFailureException $e) {
				            //Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				        }
				    }

				    if ($newtitle) {
				        $item->title = $newtitle;
				    }
				}

				// item edit link

				if ($params->get('allow_edit', 0)) {

					K2HelperPermissions::setPermissions();

					if (K2HelperPermissions::canEditItem($item->created_by, $item->catid)) {
						$item->link_edit = Route::_('index.php?option=com_k2&view=item&task=edit&cid=' . $item->id . '&tmpl=component&template=system');
					}
				}

				// item link

				$item->link = '';

				$item->authorized = true;
				$item->isinternal = false;

				if (substr($link_to, 0, strlen('k2field:link')) === 'k2field:link') {

					$type_temp = explode(':', $link_to); // $link_to can be k2field:link:fieldid

					$field_values = '';

					if ($item_extra_fields) {
						foreach ($item_extra_fields as $item_extra_field) {
							if ($item_extra_field->id == $type_temp[2]) {
								$field_values = $item_extra_field->value;
							}
						}
					}

					if ($field_values) {

						switch (explode(":", $field_values[1])[0])
						{
							case 'http': case 'https':
								$item->link = $field_values[1];
								if (Uri::isInternal($item->link)) {
									$item->isinternal = true;
								}
								break;
							case 'ftp': case 'ftps': case 'file':
								break;
							case 'mailto':
								$item->link = $field_values[1];
								break;
							default: // internal
								$item->link = $field_values[1];
								if (strpos($item->link, Uri::base()) === false) {
									$item->link = Uri::base() . $field_values[1];
								}
								$item->isinternal = true;
						}

						if ($item->link) {

							if ($link_target !== 'default') {
								$item->linktarget = $link_target;
								if ($item->linktarget == 4) {
									$item->linktarget = '';
								}
							} else {
								switch ($field_values[2]) {
									case 'new' : $item->linktarget = 1; break;
									case 'popup' : $item->linktarget = 2 ; break;
									case 'lightbox' : $item->linktarget = 3 ; break; // limitation: use of Bootstrap modal, not lightbox
									default : $item->linktarget = ''; /*$item->isinternal = true;*/ // same
								}
							}

							$item->linktitle = $field_values[0];
							if (empty($item->linktitle)) {
								$item->linktitle = $item->title;
							}
						}
					}
				}

				if ($item->state == 1 && ((empty($item->link) && $link_defaults_to_item) || $link_to == 'item')) {

					//$item->linktarget = '';
					$item->isinternal = true;

					$item->linktitle = $item->title;

					$link_string = K2HelperRoute::getItemRoute($item->slug, $item->cat_slug);

					if ($item->category_authorized && (!$show_unauthorized_items || in_array($item->access, $authorised))) {

						if ($link_target !== 'default') {
							$item->linktarget = $link_target;
						} else {
							$item->linktarget = '';
						}

						$item->link = urldecode(Route::_($link_string));

					} else {

						$link = new Uri(Route::_('index.php?option=com_users&view=login', false));
						$link->setVar('return', base64_encode($link_string));

						$item->link = $link;
						$item->linktarget = ''; // cannot open in modal window in this case - too many cases where it might fail bacause the login form	opens first
						$item->authorized = false;
					}
				}

				// rating

				$item->vote = '';
				$item->vote_count = 0;

				if ($requireVoteData) {

    				$query = $db->getQuery(true);

    				$query->select('ROUND(' . $db->quoteName('v.rating_sum') . ' / ' . $db->quoteName('v.rating_count') . ', 1) AS ' . $db->quoteName('rating'));
    				$query->select($db->quoteName('v.rating_count', 'rating_count'));
    				$query->from($db->quoteName('#__k2_rating', 'v'));
    				$query->where($db->quoteName('v.itemID') . ' = ' . (int) $item->id);

    				$db->setQuery($query);

    				try {
    					$ratings = $db->loadObjectList();
    					foreach ($ratings as $rating) {
    						$item->vote = $rating->rating;
    						$item->vote_count = $rating->rating_count;
    					}
    				} catch (ExecutionFailureException $e) {
    					//$app->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
    				}
				}

				// tags

				$query = $db->getQuery(true);

				$query->select('tag.*');
				$query->from($db->quoteName('#__k2_tags', 'tag'));
				$query->join('LEFT', $db->quoteName('#__k2_tags_xref', 'xref'), $db->quoteName('tag.id') . ' = ' . $db->quoteName('xref.tagID'));
				$query->where($db->quoteName('tag.published') . ' = 1');
				$query->where($db->quoteName('xref.itemID') . ' = ' . (int) $item->id);
				$query->order($db->quoteName('tag.name') . ' ASC');

				$db->setQuery($query);

				$item->tags = array();
				try {
					$tags_array = $db->loadObjectList();
					if (count($tags_array) > 0) {
						$item->tags = $tags_array;
					}
				} catch (ExecutionFailureException $e) {
					//$app->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				}

				//

				$item->imagetag = '';
				$item->error = array();

				// icon

				// n/a

				// video

				// n/a

				// thumbnail image creation

				if (in_array($item_head_type, $image_types) || substr($item_head_type, 0, strlen('k2field:image')) === 'k2field:image') {

				    $image_to_attach = '';
				    $image_width = 0;
				    $image_height = 0;

				    // note: original images are not cached, therefore looking thru article content will be inefficient

					if (!$clear_cache && $params->get('create_thumb', 1) && Factory::getApplication()->input->get('format', 'html') !== 'feed') {
					    $thumbnail_src = Helper::thumbnailExists($item_prefix_id, $item->id, $tmp_path, $create_highres_images);
					    if ($thumbnail_src !== false) {
					        $image_to_attach = $thumbnail_src; // found a corresponding thumbnail
						}
					}

					$imagesrc = '';

					if (empty($image_to_attach)/* || Factory::getApplication()->input->get('format', 'html') === 'feed'*/) {

						if ($item_head_type == 'imageintro') { // K2 image

							$k2imagesrc = 'media/k2/items/cache/'.md5("Image".$item->id).'_Generic.jpg';
							if (is_file(JPATH_ROOT.'/'.$k2imagesrc)) { // makes sure the K2 image exists
								$imagesrc = $k2imagesrc;
							}

						} else if ($item_head_type == 'image') {
						    if (isset($item->fulltext) && $item_search_fulltext) {
								$imagesrc = Helper::getImageSrcFromContent($item->introtext, $item->fulltext);
							} else {
								$imagesrc = Helper::getImageSrcFromContent($item->introtext);
							}

						} else if ($item_head_type == 'authorcontact') {
							if (isset($item->author_image)) {
								$imagesrc = $item->author_image;
							}

						} else if ($item_head_type == 'authork2user') {
							if (isset($item->author_avatar)) {
								$k2imagesrc = 'media/k2/users/'.$item->author_avatar;
								if (is_file(JPATH_ROOT.'/'.$k2imagesrc)) { // makes sure the K2 image exists
									$imagesrc = $k2imagesrc;
								}
							}

						} else if ($item_head_type == 'allimagesasc') {
						    if (isset($item->fulltext) && $item_search_fulltext) {
								$imagesrc = Helper::getImageSrcFromContent($item->introtext, $item->fulltext);
							} else {
								$imagesrc = Helper::getImageSrcFromContent($item->introtext);
							}

							// if images not found, look into K2 image
							if (empty($imagesrc)) {
								$k2imagesrc = 'media/k2/items/cache/'.md5("Image".$item->id).'_Generic.jpg';
								if (is_file(JPATH_ROOT.'/'.$k2imagesrc)) { // makes sure the k2 image exists
									$imagesrc = $k2imagesrc;
								}
							}

						} else if ($item_head_type == 'allimagesdesc') {

							$k2imagesrc = 'media/k2/items/cache/'.md5("Image".$item->id).'_Generic.jpg';
							if (is_file(JPATH_ROOT.'/'.$k2imagesrc)) { // makes sure the k2 image exists
								$imagesrc = $k2imagesrc;
							}

							// if K2 image not found, look into the item images
							if (empty($imagesrc)) {

							    if (isset($item->fulltext) && $item_search_fulltext) {
									$imagesrc = Helper::getImageSrcFromContent($item->introtext, $item->fulltext);
								} else {
									$imagesrc = Helper::getImageSrcFromContent($item->introtext);
								}
							}
						} else if (substr($item_head_type, 0, strlen('k2field:image')) === 'k2field:image') {

							$type_temp = explode(':', $item_head_type); // $item_head_type can be k2field:image:fieldid

							if ($item_extra_fields) {
								foreach ($item_extra_fields as $item_extra_field) {
									if ($item_extra_field->id == $type_temp[2]) {
										$imagesrc = $item_extra_field->value;
									}
								}
							}
						}

						// last resort, use default image if it exists
						$used_default_image = false;
						if (empty($imagesrc)) {
							if ($default_picture) {
								$imagesrc = $default_picture;
								$used_default_image = true;
							} else {
							    $imagesrc = '';
							}
						}
					}

					if ($imagesrc) { // found an image
						if (Factory::getApplication()->input->get('format', 'html') === 'feed') {

						    $item->imagesrc = $imagesrc; // keep the image src, useful for feeds

						} else {
						    if (!$params->get('create_thumb', 1) || $item_head_width <= 0 || $item_head_height <= 0) { // no thumbnails are created, use the original image
						        // Use the original
						        $image_to_attach = $imagesrc;

						    } else {
						        // Create the thumbnail
						        $result_array = Helper::getImageFromSrc($item_prefix_id, $item->id, $imagesrc, $tmp_path, $item_head_width, $item_head_height, $crop_picture, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);

						        if (isset($result_array['url']) && $result_array['url']) {
						            $image_to_attach = $result_array['url'];
						        }

						        if (isset($result_array['error']) && $result_array['error']) {

						            $item->error[] = $result_array['error'];

    								// if error for the file found, try and use the default image instead
    								if (!$used_default_image && $default_picture) { // if the default image was the one chosen, no use to retry
    								    $result_array = Helper::getImageFromSrc($item_prefix_id, $item->id, $default_picture, $tmp_path, $item_head_width, $item_head_height, $crop_picture, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);

    								    if (isset($result_array['url']) && $result_array['url']) {
    								        $image_to_attach = $result_array['url'];
    								    }

    								    if (isset($result_array['error']) && $result_array['error']) {
    								        $item->error[] = $result_array['error'];
    								    }
    								}
						        }
						    }
						}
					}

					if ($image_to_attach) {

						$img_attributes = array();

					    if ($crop_picture) {
					        if ($item_head_width > 0 && $item_head_height > 0) {
					            $image_width = $item_head_width;
					            $image_height = $item_head_height;
					        }
					    } else {
					        try {
					            $image_properties = Image::getImageFileProperties($image_to_attach);
					            $image_width = $image_properties->width;
					            $image_height = $image_properties->height;
					        } catch (\Exception $e) {
					            $image_width = 0;
					            $image_height = 0;
					        }
					    }
						
						if ($image_width > 0 && $image_height > 0) {
						    $img_attributes = array('width' => $image_width, 'height' => $image_height);
						}

						$extra_attributes = trim($params->get('image_attributes', ''));
						if ($extra_attributes) {
							$xml = new \SimpleXMLElement('<element ' . $extra_attributes . ' />');
							foreach ($xml->attributes() as $attribute_name => $attribute_value) {
								$img_attributes[$attribute_name] = $attribute_value;
							}
						}

						$item->imagetag = SYWUtilities::getImageElement($image_to_attach, $item->title, $img_attributes, $lazyload, $create_highres_images, null, true, SYWVersion::getMediaVersion('com_latestnewsenhancedpro_' . $prefix_id));
					}
				}

				if ($item->date == $db->getNullDate() || $item->date == null) {
					$item->date = '';
				}

				// ago

				if ($item->date) {
					$details = Helper::date_to_counter($item->date);

					$item->nbr_seconds  = intval($details['secs']);
					$item->nbr_minutes  = intval($details['mins']);
					$item->nbr_hours = intval($details['hours']);
					$item->nbr_days = intval($details['days']);
					$item->nbr_months = intval($details['months']);
					$item->nbr_years = intval($details['years']);
				}

				// calendar shows an extra field of type 'date'

				if ($item_head_type == 'calendar') {
					$item->calendar_date = $item->date;
				} else if (substr($item_head_type, 0, strlen('k2field:date')) === 'k2field:date') {

					$type_temp = explode(':', $item_head_type); // $head_type can be k2field:date:fieldid

					$item->calendar_date = null;

					if ($item_extra_fields) {
						foreach ($item_extra_fields as $item_extra_field) {
							if ($item_extra_field->id == $type_temp[2]) {
								$item->calendar_date = $item_extra_field->value;
							}
						}
					}
				}

				// title

				if (!$item_force_one_line) {
					if (strlen($item_title_letter_count) > 0) {
						$item->title = SYWText::getText($item->title, 'txt', (int)$item_title_letter_count, true, '', true, $item_title_truncate_last_word);
					}
				}

				// text

				$item->text = '';

				if (!$item->authorized && !$show_unauthorized_text) {
				    $item->text = '';
				    if ($unauthorized_text) {
				        $item->text = Text::_($unauthorized_text);
				    }
				} else {

				    if (substr($text_type, 0, strlen('k2field:textarea')) === 'k2field:textarea') {

				    	$type_temp = explode(':', $text_type); // $title_type can be k2field:textarea:fieldid

				        if ($item_extra_fields) {
				            foreach ($item_extra_fields as $item_extra_field) {
				                if ($item_extra_field->id == $type_temp[2]) {
				                    $item->text = $item_extra_field->value;
				                    if ($item->text) {
				                        $item->text = nl2br($item->text);
				                        if ($item_trigger_contentprepare) { // will trigger events from plugins
				                            $item->text = HTMLHelper::_('content.prepare', $item->text);
				                        }
				                        $item->text = SYWText::getText($item->text.$beacon, 'html', $item_number_of_letters, $item_strip_tags, $item_keep_tags, true, $item_truncate_last_word);
				                    }
				                }
				            }
				        }
				    } else {

				        switch ($text_type)
				        {
				            case 'intrometa': $use_intro = (trim($item->introtext) != '') ? true : false; break;
				            case 'metaintro': $use_intro = (trim($item->metadesc) != '') ? false : true; break;
				            case 'meta': $use_intro = false; break;
				            default: case 'intro': $use_intro = true;
				        }

    				    if ($use_intro) { // use intro text
    				        $item->text = $item->introtext;
    				        if ($item->text) { // will trigger events from plugins
        				        if ($item_trigger_contentprepare) {
        				        	$item->text = HTMLHelper::_('content.prepare', $item->text);
        				        }
        				        $item->text = SYWText::getText($item->text.$beacon, 'html', $item_number_of_letters, $item_strip_tags, $item_keep_tags, true, $item_truncate_last_word);
    				        }
    				    } else { // use meta text
    				    	$item->text = SYWText::getText($item->metadesc.$beacon, 'txt', $item_number_of_letters, false, '', true, $item_truncate_last_word);
    				    }
				    }
				}

				// the text won't be cropped if the ^ character is still present after processing (hopefully no ^ at the end of the text)
				$item->cropped = true;
				if (!$always_show_readmore) {
					$text_length = strlen($item->text);
					$item->text = rtrim($item->text, "^");
					if (strlen($item->text) < $text_length && !$item->fulltexthascontent) {
						$item->cropped = false;
					}
				}
			}

			// TODO add code for ads here
		}

		// Add the items to the internal cache
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	public function getStart()
	{
		$store = $this->getStoreId('getstart');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$app = Factory::getApplication();

		// load the parameters
		$params = $app->getParams();

		// pagination request variables

		if ($params->get('pag_type', 'std') == 'lm') {
			$start = $this->getState('ajaxlist.start');
		} else {
			$start = $this->getState('list.start');
		}

		if ($start > 0)
		{
			if ($params->get('pag_type', 'std') == 'lm') {
				$limit = $this->getState('ajaxlist.limit');
			} else {
				$limit = $this->getState('list.limit');
			}
			$total = $this->getTotal();

			if ($start > $total - $limit)
			{
				$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
			}
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	/**
	 * Get the authors as an array of id, name
	 */
	public function getAuthorsList($params = null)
	{
		if (!isset($this->authors_list))
		{
		    if (!isset($params)) {
		        $params = $this->getState('params');
		    }

			$this->authors_list = array();

			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$author_order = $params->get('author_index_order', 'selection') == 'selection' ? $params->get('author_order', '') : $params->get('author_index_order', 'selection');

			$include = $params->get('author_inex', 1);
			$authors_array = $params->get('created_by', array());
			
			$array_of_authors_values = array_count_values($authors_array);
			if ((isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) || $params->get('show_a', 'alias') == 'alias' || !$include) {

				// need to go through all articles to find the authors

			    $query->select('DISTINCT ' . $db->quoteName('a.created_by') . ' AS id');
				$query->from($db->quoteName('#__k2_items', 'a'));
				$query->where($db->quoteName('a.trash') . ' = 0');

				switch ($params->get('show_a', 'alias')) {
					case 'full': $query->select($db->quoteName('ua.name', 'name')); break;
					case 'user': $query->select($db->quoteName('ua.username', 'name')); break;
					default: $query->select('CASE WHEN ' . $db->quoteName('a.created_by_alias') . ' > ' . $db->quote(' ') . ' THEN ' . $db->quoteName('a.created_by_alias') . ' ELSE ' . $db->quoteName('ua.name') . ' END AS name');
				}

				$query->join('LEFT', $db->quoteName('#__users', 'ua'), $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by'));

				// limit the search for authors in the categories selected
				if ($this->getCategoriesString($params) != '') {
					$test_type = $params->get('cat_inex', 1) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.catid') . ' ' . $test_type . ' (' . $this->getCategoriesString($params) . ')');
				}

				if (isset($array_of_authors_values['all']) && $array_of_authors_values['all'] > 0) { // 'all' was selected

				} else if (isset($array_of_authors_values['auto']) && $array_of_authors_values['auto'] > 0) { // 'auto' was selected: equivalent to check if the author is logged in
				    $test_type = $include ? '=' : '<>';
				    $query->where($db->quoteName('a.created_by') . ' ' . $test_type . ' ' . (int) Factory::getUser()->get('id'));
				} else {
					$authors = implode(',', $authors_array);
					if ($authors) {
					    $test_type = $include ? 'IN' : 'NOT IN';
					    $query->where($db->quoteName('a.created_by') . ' ' . $test_type . ' (' . $authors . ')');
					}
				}

			} else {

				// the authors are known, no need to go through the articles - much more efficient
				// do not use this with 'exclude' or else one can end up with thousands of users, as this is not tied to article owners

			    $query->select($db->quoteName('ua.id', 'id'));
				$query->from($db->quoteName('#__users', 'ua'));

				switch ($params->get('show_a', 'alias')) {
					case 'full': $query->select($db->quoteName('ua.name', 'name')); break;
					default: $query->select($db->quoteName('ua.username', 'name'));
					// cannot get an alias here
				}

				if (isset($array_of_authors_values['auto']) && $array_of_authors_values['auto'] > 0) { // 'auto' was selected: equivalent to check if the author is logged in
				    $query->where($db->quoteName('ua.id') . ' = ' . (int) Factory::getUser()->get('id'));
				} else {
					$authors = implode(',', $authors_array);
					if ($authors) {
					    $query->where($db->quoteName('ua.id') . ' IN (' . $authors . ')');
					}
				}
			}

			$author_picture_source = '';
			if ($params->get('show_author_pic', 0)) {
				$author_picture_source = $params->get('author_pic_source', '');
				if ($author_picture_source == 'k2user') {
					$query->select($db->quoteName('k2users.image', 'image'));
					$query->join('LEFT', $db->quoteName('#__k2_users', 'k2users'), $db->quoteName('k2users.userID') . ' = ' . $db->quoteName('ua.id'));
				}
			}

			switch ($author_order)
			{
			    case 'selec_asc': $query->order($db->quoteName('name') . ' ASC'); break;
			    case 'selec_dsc': $query->order($db->quoteName('name') . ' DESC'); break;
			}

			$db->setQuery($query);

			try {
				$authors = $db->loadObjectList();

				foreach ($authors as $author) {
					if (!isset($this->authors_list[$author->id])) {
						$author_object = new CMSObject();
						$author_object->set('id', $author->id);
						$author_object->set('name', array($author->name));

						$author_object->set('image', '');
						if ($author_picture_source) {
							if (isset($author->image) && $author->image) {
								$author_object->set('image', 'media/k2/users/' . $author->image);
							}
						}

						$this->authors_list[$author->id] = $author_object;
					} else {
						$name = $this->authors_list[$author->id]->get('name');
						//$this->authors_list[$author->id]->set('name', $name . '/' . $author->name);
						$this->authors_list[$author->id]->name[] = $author->name;
					}
				}

				// clean up the names
				foreach ($this->authors_list as $author) {
					$author->name = array_unique($author->name);
					$author->name = implode('/', $author->name);
				}
			} catch (ExecutionFailureException $e) {
				Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				return null;
			}
		}

		return $this->authors_list;
	}

	/**
	 * Get the author aliases
	 */
	public function getAliasList($params = null)
	{
	    if (!isset($this->alias_list))
	    {
	        if (!isset($params)) {
	            $params = $this->getState('params');
	        }

	        $this->alias_list = array();
	        
	        $include = $params->get('author_alias_inex', 1);
	        $alias_array = $params->get('created_by_alias', array());
	        
	        $array_of_alias_values = array_count_values($alias_array);
	        if ((isset($array_of_alias_values['all']) && $array_of_alias_values['all'] > 0)) {
	            if ($include) {
	                // need to go through all articles to find the aliases
	                
	                $db = $this->getDbo();
	                $query = $db->getQuery(true);
	                
	                $query->select('DISTINCT ' . $db->quoteName('a.created_by_alias'));
	                $query->from($db->quoteName('#__k2_items', 'a'));
	                $query->where($db->quoteName('a.trash') . ' = 0');
	                $query->where($db->quoteName('a.created_by_alias') . ' != ' . $db->quote(''));
	                
	                // limit the search for authors in the categories selected
	                if ($this->getCategoriesString($params) != '') {
	                    $test_type = $params->get('cat_inex', 1) ? 'IN' : 'NOT IN';
	                    $query->where($db->quoteName('a.catid') . ' ' . $test_type . ' (' . $this->getCategoriesString($params) . ')');
	                }
	                
	                $query->order($db->quoteName('created_by_alias') . ' ASC');
	                
	                $db->setQuery($query);
	                
	                try {
	                    $this->alias_list = $db->loadAssocList('created_by_alias', 'created_by_alias');
	                } catch (ExecutionFailureException $e) {
	                    Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	                    return null;
	                }
	            }
	        } else {
	            if ($include) {
	                $this->alias_list = $alias_array;
	            } else {
	                
	                $db = $this->getDbo();
	                $query = $db->getQuery(true);
	                
	                $query->select('DISTINCT ' . $db->quoteName('a.created_by_alias'));
	                $query->from($db->quoteName('#__k2_items', 'a'));
	                $query->where($db->quoteName('a.trash') . ' = 0');
	                
	                $quoted_array = array();
	                foreach ($alias_array as $alias) {
	                    $quoted_array[] = $db->quote($alias);
	                }
	                
	                $aliases = implode(',', $quoted_array);
	                
	                $query->where($db->quoteName('a.created_by_alias') . ' NOT IN (' . $aliases . ')');
	                $query->where($db->quoteName('a.created_by_alias') . ' != ' . $db->quote(''));
	                
	                // limit the search for authors in the categories selected
	                if ($this->getCategoriesString($params) != '') {
	                    $test_type = $params->get('cat_inex', 1) ? 'IN' : 'NOT IN';
	                    $query->where($db->quoteName('a.catid') . ' ' . $test_type . ' (' . $this->getCategoriesString($params) . ')');
	                }
	                
	                $query->order($db->quoteName('created_by_alias') . ' ASC');
	                
	                $db->setQuery($query);
	                
	                try {
	                    $this->alias_list = $db->loadAssocList('created_by_alias', 'created_by_alias');
	                } catch (ExecutionFailureException $e) {
	                    Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	                    return null;
	                }
	            }
	        }
	    }
	    
	    return $this->alias_list;
	}

	protected function getContactData($user_id)
	{
		static $contacts = array();

		if (isset($contacts[$user_id]))
		{
			return $contacts[$user_id];
		}

		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->select('MAX(' . $db->quoteName('contact.id') . ') AS id');
		$query->select($db->quoteName(array('contact.image', 'contact.sortname1', 'contact.sortname2', 'contact.sortname3')));
		$query->from($db->quoteName('#__contact_details', 'contact'));
		$query->where($db->quoteName('contact.published') . ' = 1');
		$query->where($db->quoteName('contact.user_id') . ' = ' . (int) $user_id);

		if (Multilanguage::isEnabled()) {
		    $query->where('(' . $db->quoteName('contact.language') . ' IS NULL OR ' . $db->quoteName('contact.language') . ' IN (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . '))');
		}

		$db->setQuery($query);

		try {
			$contacts[$user_id] = $db->loadObject();
		} catch (ExecutionFailureException $e) {
			return null;
		}

		return $contacts[$user_id];
	}

	/**
	 * Get the tags as an array
	 */
	protected function getTagsSelection()
	{
		if (!isset($this->tags_selection))
		{
			$this->tags_selection = $this->getState('params')->get('k2tags', array());

			if (!empty($this->tags_selection)) {

				// if all selected, get all available tags
				$array_of_tag_values = array_count_values($this->tags_selection);
				if (isset($array_of_tag_values['all']) && $array_of_tag_values['all'] > 0) { // 'all' was selected

					// get all tags

					$db = Factory::getDbo();
					$query = $db->getQuery(true);

					$query->select($db->quoteName('id'));
					$query->from($db->quoteName('#__k2_tags'));
					$query->where($db->quoteName('published') . ' = 1');

					$db->setQuery($query);

					try {
						$tags = $db->loadColumn();
					} catch (ExecutionFailureException $e) {
						//$app->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
						$tags = array();
					}

					if (empty($tags) && $this->getState('params')->get('tags_inex', 1)) { // won't return any k2 item if no k2 item has been associated to any tag (when include tags only)
						return false;
					} else {
						$this->tags_selection = $tags;
					}
				}
			}
		}

		return $this->tags_selection;
	}

	/**
	 * Get the tags as an array of tag objects
	 */
	public function getTagsList()
	{
		if (!isset($this->tags_list))
		{
			$this->tags_list = null;

			$order = 'name';
			$order_dir = 'ASC';
			$tag_order = $this->getState('params')->get('tag_index_order', '');
			if ($tag_order) {
			    switch ($tag_order)
			    {
			        case 't_asc': $order = 'name'; $order_dir = 'ASC'; break;
			        case 't_dsc': $order = 'name'; $order_dir = 'DESC'; break;
			    }
			}

			if ($this->getTagsSelection()) {
				 // get full objects of the selected tags
			    $tag_objects = SYWK2::getTags(true, $this->getTagsSelection(), $this->getState('params')->get('tags_inex', 1), $order, $order_dir);
				if ($tag_objects !== false) {
					$this->tags_list = $tag_objects;
				}
			} else {
				// get all tags associated to articles
			    $tag_objects = SYWK2::getTags(true, array(), true, $order, $order_dir);
				if ($tag_objects !== false) {
					$this->tags_list = $tag_objects;
				}
			}

			if (!is_null($this->tags_list)) {
				foreach($this->tags_list as $tag_item) {

					if (Factory::getLanguage()->hasKey($tag_item->title)) {
						$tag_item->title = Text::_($tag_item->title);
					}

					$tag_item->image = '';
					$tag_item->image_alt = '';

					$tag_item->link_class = 'label label-info';
				}
			}
		}

		return $this->tags_list;
	}

	/**
	 * Get the categories as a string
	 */
	protected function getCategoriesString($params = null)
	{
		if (!isset($this->categories_string))
		{
		    if (!isset($params)) {
		        $params = $this->getState('params');
		    }

			$this->categories_string = '';

			$categories_array = $params->get('k2catid', array());

			$array_of_category_values = array_count_values($categories_array);
			if (isset($array_of_category_values['all']) && $array_of_category_values['all'] > 0) { // 'all' was selected
				return $this->categories_string;
			} else {

				if (!empty($categories_array)) {

					// sub-category inclusion
					$get_sub_categories = $params->get('includesubcategories', 'no');
					if ($get_sub_categories != 'no') {

						$sub_categories_array = array();

						if ($get_sub_categories == 'all') {

						    foreach ($categories_array as $category_id) {
						        $sub_categories_array[$category_id] = self::getCategoryChildren($category_id, -1, !$params->get('show_unauthorized', 0));
						    }

						} else {

						    $levels = $params->get('levelsubcategories', 1);

						    foreach ($categories_array as $category_id) {
						        $sub_categories_array[$category_id] = self::getCategoryChildren($category_id, $levels, !$params->get('show_unauthorized', 0));
						    }
						}

						foreach ($sub_categories_array as $subcategory) {
						    $categories_array = array_merge($categories_array, $subcategory);
						}
					}

					if (!empty($categories_array)) {
						$this->categories_string = implode(',', $categories_array);
					}
				}
			}
		}

		return $this->categories_string;
	}

	/**
	 * Get the categories as an array of title, image ...
	 */
	public function getCategoriesList()
	{
		if (!isset($this->categories_list))
		{
			$this->categories_list = null;

			$user = Factory::getUser(); // 4.2+ $this->getCurrentUser();
			$view_levels = $user->getAuthorisedViewLevels();

			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select($db->quoteName(array('c.id', 'c.name', 'c.image', 'c.name', 'c.description', 'c.parent'), array('id', 'title', 'image', 'image_alt', 'description', 'parent_id')));
			$query->from($db->quoteName('#__k2_categories', 'c'));
			if ($this->getCategoriesString() != '') {
				$test_type = $this->getState('params')->get('cat_inex', 1) ? 'IN' : 'NOT IN';
				$query->where($db->quoteName('c.id') . ' ' . $test_type . ' (' . $this->getCategoriesString() . ')');
			}
			$query->whereIn($db->quoteName('c.access'), $view_levels);
			$query->where($db->quoteName('c.published') . ' = 1');
			$query->where($db->quoteName('c.trash') . ' = 0');

			$order = $this->getState('params')->get('cat_index_order', '');
			if ($order) {
			    if ($order === 'selection') {
			        $order = $this->getState('params')->get('cat_order', '');
			    }
			    switch ($order)
			    {
			        case 'o_asc': $query->order($db->quoteName('c.parent') . ' ASC,' . $db->quoteName('c.ordering') . ' ASC'); break;
			        case 'o_dsc': $query->order($db->quoteName('c.parent') . ' DESC,' . $db->quoteName('c.ordering') . ' DESC'); break;
			        case 't_asc': $query->order($db->quoteName('c.name') . ' ASC'); break;
			        case 't_dsc': $query->order($db->quoteName('c.name') . ' DESC'); break;
			    }
			}

			$db->setQuery($query);

			try {
			    $this->categories_list = $db->loadObjectList('id');
			} catch (ExecutionFailureException $e) {
				Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
				return null;
			}
		}

		return $this->categories_list;
	}

	/**
	 * Get the category order query
	 */
	public function getCategoryOrderQuery()
	{
	    $db = $this->getDbo(); // 4.2+ $this->getDatabase();

	    switch ($this->getState('params')->get('cat_order', ''))
	    {
	        case 'o_asc': return $db->quoteName('c.parent') . ' ASC,' . $db->quoteName('c.ordering') . ' ASC'; break;
	        case 'o_dsc': return $db->quoteName('c.parent') . ' DESC,' . $db->quoteName('c.ordering') . ' DESC'; break;
	        case 't_asc': return $db->quoteName('c.name') . ' ASC'; break;
	        case 't_dsc': return $db->quoteName('c.name') . ' DESC'; break;
	    }

	    return '';
	}

	/**
	 * Get the top filters
	 *
	 * @return object the list of top filters
	 */
	public function getTopFilters($params)
	{
	    if (!isset($this->top_filters)) {
	        $this->top_filters = $params->get('top_filters', null);
	        if (is_string($this->top_filters)) {
	            $this->top_filters = null;
	        } else if (is_array($this->top_filters)) {
	            $this->top_filters = json_decode(json_encode($this->top_filters), FALSE);
	        }
	    }
	    return $this->top_filters;
	}
	
	/**
	 * Get the bottom filters
	 *
	 * @return object the list of bottom filters
	 */
	public function getBottomFilters($params)
	{
	    if (!isset($this->bottom_filters)) {
	        $this->bottom_filters = $params->get('bot_filters', null);
	        if (is_string($this->bottom_filters)) {
	            $this->bottom_filters = null;
	        } else if (is_array($this->bottom_filters)) {
	            $this->bottom_filters = json_decode(json_encode($this->bottom_filters), FALSE);
	        }
	    }
	    return $this->bottom_filters;
	}

	public function getTextArticleBefore()
	{
		$article_id = $this->getState('params')->get('a_before_id', '');

		if ($article_id) {
			return $this->getTextArticle($article_id);
		}

		return '';
	}

	public function getTextArticleAfter()
	{
		$article_id = $this->getState('params')->get('a_after_id', '');

		if ($article_id) {
			return $this->getTextArticle($article_id);
		}

		return '';
	}

	protected function getTextArticle($article_id)
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');
		$content_model = BaseDatabaseModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));

		$app = Factory::getApplication();
		$content_model->setState('params', $app->getParams('com_content'));

		$article = $content_model->getItem($article_id); // returns 404 if no article found!

		if ($content_model->getError()) {
			return '';
		}

		if ($article->params->get('show_intro', '1') == '1') {
			$article->text = $article->introtext.' '.$article->fulltext;
		} elseif ($article->fulltext) {
			$article->text = $article->fulltext;
		} else {
			$article->text = $article->introtext;
		}

		if (!isset($article->tags)) {
			$article->tags = new TagsHelper();
			$article->tags->getItemTags('com_content.article', $article_id);
		}

		if ($this->getState('params')->get('allow_plugins', 0)) {
			PluginHelper::importPlugin('content');
			Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.article', &$article, &$article->params, 0));
		}

		// Note: the article is not merged with the general params,
		// therefore, all options need to be specifically set in the article
		// in order to work with the plugin 'Article Details'

		return $article->text;
	}

	protected static function getCategoryChildren($category_id, $level = -1, $limited_access = true, $reset = true)
	{
	    static $array = array();
	    if ($reset) {
	        $array = array();
	    }

	    $db = Factory::getDbo();

	    $query = $db->getQuery(true);

	    $query->select($db->quoteName('id'));
	    $query->from($db->quoteName('#__k2_categories'));
	    $query->where($db->quoteName('parent') . '=' . (int) $category_id);
	    $query->where($db->quoteName('published') . ' = 1');
	    $query->where($db->quoteName('trash') . ' = 0');

	    if ($limited_access) {
	        $query->whereIn($db->quoteName('access'), Factory::getApplication()->getIdentity()->getAuthorisedViewLevels());
	    }

	    if (Factory::getApplication()->getLanguageFilter()) {
	        $query->whereIn($db->quoteName('language'), [Factory::getLanguage()->getTag(), '*'], ParameterType::STRING);
	    }

	    $db->setQuery($query);

	    try {
	        $rows = $db->loadColumn();
	        foreach ($rows as $row) {
	            array_push($array, $row);
	            if ($level < 0) {
	                self::getCategoryChildren($row, -1, $limited_access, false);
	            } else if ($level > 1) {
	                self::getCategoryChildren($row, $level - 1, $limited_access, false);
	            }
	        }
	    } catch (ExecutionFailureException $e) {
	        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
	        return null;
	    }

	    return $array;
	}

}
