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

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

class SPGalleryHelper
{
	/**
	 * Prepares the Gallery Manager Widget uploaded files prior to being passed
	 * to the Gallery Widget to display the Gallery on the front-end.
	 * 
	 * @param   array  $items
	 * @param   array  $tags
	 * 
	 * @return  array
	 */
	public static function prepareItems($items = [], $tags = [])
	{
		foreach ($items as $key => &$item)
		{
			// Skip items that have not saved properly(items were still uploading and we saved the item)
			if ($key === 'ITEM_ID')
			{
				unset($items[$key]);
				continue;
			}

			$itemTags = [];

			$stored_item_tags = isset($item['tags']) ? $item['tags'] : [];
			if (is_array($stored_item_tags) && count($stored_item_tags))
			{
				foreach ($stored_item_tags as $tagId)
				{
					if (!array_key_exists($tagId, $tags))
					{
						continue;
					}

					$itemTags[] = $tags[$tagId];
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