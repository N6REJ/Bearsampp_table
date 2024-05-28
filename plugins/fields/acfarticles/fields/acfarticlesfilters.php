<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class ACFArticlesFilters
{
    private $db;

    private $query;

    private $filters;
    
    public function __construct($query, $filters)
    {
        $this->db = Factory::getDbo();
        $this->query = $query;
        $this->filters = $filters;
    }

    public function apply()
    {
        $this->applyCategories();
        
        $this->applyTags();
        $this->applyAuthor();
        
        $this->applyStatus();
        $this->applyOrdering();
         
        $this->applyLimit();
         

        return $this->query;
    }
    
    public function applyCategories()
    {
        if (!isset($this->filters['filters_category_enabled']))
        {
            return;
        }

        if (!isset($this->filters['filters_category_value']))
        {
            return;
        }

        $categories = $this->filters['filters_category_value'];

        $inc_children = isset($this->filters['filters_category_inc_children']) ? (string) $this->filters['filters_category_inc_children'] : false;
        
        if (in_array($inc_children, ['1', '2']))
        {
            $categories = is_string($categories) ? array_map('trim', explode(',', $categories)) : $categories;
            
            $children_categories = $this->getCategoriesChildIds($categories);

            // Also include children categories
            if ($inc_children === '1')
            {
                $categories = array_unique(array_filter(array_merge($categories, $children_categories)));
            }
            // Use only children categories
            else
            {
                $categories = $children_categories;
            }

            $categories = implode(',', $categories);
        }

        $this->query->where($this->db->quoteName('a.catid') . ' IN (' . (is_string($categories) ? $categories : implode(',', $categories)) . ')');
    }

    
    public function applyTags()
    {
        if (!isset($this->filters['filters_tag_enabled']))
        {
            return;
        }

        if (!isset($this->filters['filters_tag_value']))
        {
            return;
        }
        
        if ($filtersTagEnabled = (string) $this->filters['filters_tag_enabled'] === '1' && $tags = $this->filters['filters_tag_value'])
        {
            $this->query
                ->join('INNER', $this->db->quoteName('#__contentitem_tag_map', 't') . ' ON t.content_item_id = a.id')
                ->where($this->db->quoteName('t.tag_id') . ' IN (' . (is_string($tags) ? $tags : implode(',', $tags)) . ')')
                ->group($this->db->quoteName('a.id'));
        }
    }

    public function applyAuthor()
    {
        if (!isset($this->filters['filters_author_enabled']))
        {
            return;
        }

        if (!isset($this->filters['filters_author_value']))
        {
            return;
        }

        if ($filtersAuthorEnabled = (string) $this->filters['filters_author_enabled'] === '1' && $authors = $this->filters['filters_author_value'])
        {
            $this->query->where($this->db->quoteName('a.created_by') . ' IN (' . (is_string($authors) ? $authors : implode(',', $authors)) . ')');
        }

    }
    

    public function applyStatus()
    {
        // Default status is to show published articles
        $status = [1];

        
        if (isset($this->filters['filters_status_value']) && is_array($this->filters['filters_status_value']) && count($this->filters['filters_status_value']))
        {
            $status = $this->filters['filters_status_value'];
        }
        

        // Set articles status
        $this->query->where($this->db->quoteName('a.state') . ' IN (' . (is_string($status) ? $status : implode(',', $status)) . ')');

    }

    private function applyOrdering()
    {
        // Apply ordering
        $order = is_string($this->filters['order']) ? [$this->filters['order']] : $this->filters['order'];
        if (!$order)
        {
            return;
        }

        $order = array_filter(array_map('trim', $order));
        $orders = [];

        foreach ($order as $item)
        {
            $lastUnderscorePos = strrpos($item, '_');
            $part1 = substr($item, 0, $lastUnderscorePos);
            $part2 = substr($item, $lastUnderscorePos + 1);

            if (!$part1 || !$part2)
            {
                break;
            }
            
            $orders[] = $part1 . ' ' . $part2;
        }

        if ($orders)
        {
            $this->query->order($this->db->escape(implode(',', $orders)));
        }
    }

    private function applyLimit()
    {
        // Apply limit
        $limit = isset($this->filters['limit']) ? (int) $this->filters['limit'] : false;
        if (!$limit)
        {
            return;
        }

        $this->query->setLimit($limit);
    }

    /**
     * Return all categories child ids.
     * 
     * @param   array  $categories
     * 
     * @return  array
     */
    private function getCategoriesChildIds($categories = [])
    {
        $query = $this->db->getQuery(true)
            ->select('a.id')
            ->from($this->db->quoteName('#__categories', 'a'))
            ->where('a.extension = ' . $this->db->quote('com_content'))
            ->where('a.published = 1');

        $children = [];

        while (!empty($categories))
        {
            $query
                ->clear('where')
                ->where($this->db->quoteName('a.parent_id') . ' IN (' . implode(',', $categories) . ')');
            
            $this->db->setQuery($query);

            $categories = $this->db->loadColumn();

            $children = array_merge($children, $categories);
        }

        return $children;
    }
}