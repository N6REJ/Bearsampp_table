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

use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Uri\Uri;

class ACFGalleryHelper
{
	/**
	 * Parses and returns the style.
	 * 
	 * @param   string  $style
	 * 
	 * @return  string
	 */
	public static function getStyle($style)
	{
		// Remove 'z' character from "zjustified" style
		// This is done for previewing purposes to display it at the end.
		return ltrim($style, 'z');
	}
	
	/**
	 * Prepares the Gallery Manager Widget uploaded files prior to being passed
	 * to the Gallery Widget to display the Gallery on the front-end.
	 * 
	 * @param   array  $items
	 * 
	 * @return  array
	 */
	public static function prepareItems($items)
	{
		$tagsHelper = new TagsHelper();
		
		$parsedTagIds = [];
		
		foreach ($items as $key => &$item)
		{
			// Skip items that have not saved properly(items were still uploading and we saved the item)
			if ($key === 'ITEM_ID')
			{
				unset($items[$key]);
				continue;
			}

			// Get tag names from stored IDs
			$itemTags = [];

			$tags = isset($item['tags']) ? $item['tags'] : [];
			if (is_array($tags) && count($tags))
			{
				foreach ($tags as $tagId)
				{
					if (isset($parsedTagIds[$tagId]))
					{
						$itemTags[] = $parsedTagIds[$tagId];
					}
					else
					{
						if (!$tag = $tagsHelper->getTagNames([$tagId]))
						{
							continue;
						}
						
						$itemTags[] = $tag[0];
						$parsedTagIds[$tagId] = $tag[0];
					}
				}
			}

			$item = array_merge($item, [
				'url' =>  Uri::root() . $item['image'],
				'thumbnail_url' => Uri::root() . $item['thumbnail'],
				'tags' => $itemTags
			]);
		}

		return $items;
	}
}