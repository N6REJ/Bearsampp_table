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

use NRFramework\File;
use NRFramework\Functions;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File as JoomlaFile;
use Joomla\Filesystem\Path;
use Joomla\CMS\Uri\Uri;

/**
 *  Upload Helper class mainly used by the FileUpload field
 */
class ACFUploadHelper
{
	/**
	 * How long the files can stay in the temp folder.
	 * 
	 * After each save a clean up is run and all files older
	 * than this value in days are removed.
	 * 
	 * @var  int
	 */
	private static $temp_files_cleanup_days = 1;
	
	/**
	 * Moves all given `tmp` items over to the destination folder.
	 * 
	 * @param   array   $value
	 * @param   object  $field
	 * @param   object  $item
	 * 
	 * @return  void
	 */
	public static function moveTempItemsToDestination($value, $field, $item)
	{
		// Make field params use Registry
		if (!$field->fieldparams instanceof Registry)
		{
			$field->fieldparams = new Registry($field->fieldparams);
		}

		/**
		 * Prepare the items for backwards compatibility
		 */
		$items = is_string($value) ? json_decode($value, true) ?? [['value' => $value]] : $value;

		if (isset($items['value']))
		{
			$items = [$items];
		}

		$limit_files = (int) $field->fieldparams->get('limit_files', 1);

		// Handle single file
		if ($limit_files === 1 && is_array($items))
		{
			$items = [reset($items)];
		}
		
		$ds = DIRECTORY_SEPARATOR;

		$destination_folder = trim(ltrim($field->fieldparams->get('upload_folder'), $ds), $ds);

		// Add field Smart Tags
		$field_custom_tags = [
			'id' => $field->id
		];

		// Add item smart tags
		$custom_tags = [
			'id' => $item->id,
			'author_id' => isset($item->created_by) ? $item->created_by : '',
			'alias' => isset($item->alias) ? $item->alias : '',
			'cat_id' => isset($item->catid) ? $item->catid : '',
			'cat_alias' => isset($item->catid) ? self::getCategoryAlias($item->catid) : ''
		];
		
		// Move all files from `tmp` folder over to the `upload folder`
		foreach ($items as $key => &$item)
		{
			$item_path = implode($ds, [JPATH_ROOT, ltrim($item['value'], $ds)]);
			if (!Functions::startsWith(ltrim($item['value'], $ds), self::getTempFolder()) || !file_exists($item_path))
			{
				continue;
			}

			// Smart Tags Instance
			$st = new \NRFramework\SmartTags();

			// Add field Smart Tags
			$st->add($field_custom_tags, 'acf.field.');
			
			// Add item Smart Tags
			$st->add($custom_tags, 'acf.item.');
			
			// Add file Smart Tags
			$source_file_info = File::pathinfo($item['value']);
			$source_basename = $source_file_info['basename'];
			$source_file_info['filename'] = $source_file_info['filename'];
			$source_file_info['basename'] = $source_basename;
			$source_file_info['extension'] = $source_file_info['extension'];
			$source_file_info['index'] = $key + 1;
			$source_file_info['total'] = count($items);

			$st->add($source_file_info, 'acf.file.');

			$_destination_folder = $st->replace($destination_folder);

			$destination_file = implode($ds, [JPATH_ROOT, $_destination_folder]);
			
			// Validate destination file by adding the extension at the end
			$destination_file_info = File::pathinfo($destination_file);
			if (!isset($destination_file_info['extension']))
			{
				$destination_file = implode($ds, [$destination_file_info['dirname'], $destination_file_info['basename'], $source_basename]);
			}

			// Move item
			$destionation_path = File::move($item_path, $destination_file);
			
			// Get relative path
			$relative_path = ltrim(rtrim(str_replace(JPATH_ROOT, '', $destionation_path), $ds), $ds);

			// Update destination path
			$item['value'] = $relative_path;
		}

		return $items;
	}

	/**
	 * Returns a category alias by its ID.
	 * 
	 * @param   int     $cat_id
	 * 
	 * @return  string
	 */
	private static function getCategoryAlias($cat_id = null)
	{
		if (!$cat_id)
		{
			return;
		}
		
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('alias'))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('id') . ' = ' . (int) $cat_id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Delete an uploaded file
	 *
	 * @param string $filename	The filename
	 * @param string $upload_folder	The uploaded folder
	 *
	 * @return bool
	 */
	public static function deleteFile($filename)
	{
		if (empty($filename))
		{
			return;
		}

		$file = Path::clean(implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, $filename]));

		if (!is_file($file))
		{
			return;
		}

		return JoomlaFile::delete($file);
	}

	/**
	 * Return absolute full URL of a path
	 *
	 * @param	string	$path
	 *
	 * @return	string
	 */
	public static function absURL($path)
	{
		$path = str_replace([JPATH_SITE, JPATH_ROOT, Uri::root()], '', $path);
		$path = Path::clean($path);

		// Convert Windows Path to Unix
		$path = str_replace('\\','/',$path);

		$path = ltrim($path, '/');
		$path = Uri::root() . $path;

		return $path;
	}

	/**
	 * Get a human readable file size in PHP:
	 * Credits to: http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
	 *
	 * @param integer	$filename	The file size in bytes
	 * @param int 		$decimals
	 *
	 * @return string	The human readable string
	 */
	public static function humanFilesize($bytes, $decimals = 2)
	{
		$size = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
	}

	/**
	 * Cleans the temp folder.
	 * 
	 * Removes any image that is 1 day or older.
	 * 
	 * @return  void
	 */
	public static function clean()
	{
		$temp_folder = self::getFullTempFolder();
		
		if (!is_dir($temp_folder))
		{
			return;
		}

		// Get images
		$files = array_diff(scandir($temp_folder), ['.', '..', '.DS_Store', 'index.html']);

		$found = [];

		foreach ($files as $key => $filename)
		{
			$file_path = implode(DIRECTORY_SEPARATOR, [$temp_folder, $filename]);
			
			// Skip directories
			if (is_dir($file_path))
			{
				continue;
			}

			$diff_in_miliseconds = time() - filemtime($file_path);

			// Skip the file if it's not old enough
			if ($diff_in_miliseconds < (60 * 60 * 24 * self::$temp_files_cleanup_days))
			{
				continue;
			}

			$found[] = $file_path;
		}

		if (!$found)
		{
			return;
		}

		// Delete found old files
		foreach ($found as $file)
		{
			unlink($file);
		}
	}

	/**
	 * Full temp directory where images are uploaded
	 * prior to them being saved in the final directory.
	 * 
	 * @return  string
	 */
	private static function getFullTempFolder()
	{
		return implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, self::getTempFolder()]);
	}

	/**
	* Temp folder where files are uploaded
	* prior to them being saved in the final directory.
	* 
	* @var  string
	*/
	public static function getTempFolder()
	{
		return implode(DIRECTORY_SEPARATOR, ['media', 'acfupload', 'tmp']);
	}
}