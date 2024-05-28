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

defined('_JEXEC') or die;

if (!$value = $field->value)
{
	return;
}

require_once JPATH_SITE . '/plugins/fields/acfupload/fields/uploadhelper.php';

$limit_files = (int) $fieldParams->get('limit_files');

/**
 * This handles backwards compatibility
 */
$files = is_string($value) ? json_decode($value, true) ?? [['value' => $value]] : $value;

// Handle single file
if ($limit_files === 1 && is_array($files))
{
	if (isset($files['value']))
	{
		$files = [$files];
	}

	$files = [reset($files)];
}

$layout = $fieldParams->get('layout', 'link');
$buffer = [];
$total_files = count($files);

$index = 1;
foreach ($files as $value)
{
	// Make sure we have a value
	if (!$value)
	{
		continue;
	}

	$file = isset($value['value']) ? $value['value'] : $value;
	$title = isset($value['title']) && !empty($value['title']) ? $value['title'] : '';
	$description = isset($value['description']) && !empty($value['description']) ? html_entity_decode(urldecode($value['description'])) : '';

	$file_url  = ACFUploadHelper::absURL($file);

	switch ($layout)
	{
		// Image
		case 'img':
			$item = '<img src="' .  $file_url . '"/>';
			break;

		// Custom Layout
		case 'custom':
			if (!$subject = $fieldParams->get('custom_layout'))
			{
				return;
			}

			// Add the prefix "acf." to any "{file.*}" Smart Tags found
			$new_format = 'acf.file.';
			$subject = preg_replace('/{file\.([^}]*)}/', '{'.$new_format.'$1}', $subject);

			$file_full_path = JPATH_SITE . '/' . $file;
			$exists = is_file($file_full_path);

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);

			$st = new \NRFramework\SmartTags();

			$file_tags = [
				'index' => $index,
				'total' => $total_files,
				'name' => $filePathInfo['basename'],
				'basename' => $filePathInfo['basename'],
				'filename' => $filePathInfo['filename'],
				'ext' => $filePathInfo['extension'],
				'extension' => $filePathInfo['extension'],
				'path' => $file,
				'url' => $file_url,
				'size' => $exists ? ACFUploadHelper::humanFilesize(filesize($file_full_path)) : 0,
				'title' => $title,
				'description' => nl2br($description)
			];
			$st->add($file_tags, 'acf.file.');

			$item = $st->replace($subject);
			
			break;

		// Link
		default:
			$item = '<a href="' . $file_url . '"';

			if ($fieldParams->get('force_download', true))
			{
				$item .= ' download';
			}

			$link_text = $fieldParams->get('link_text', $file);

			$st = new \NRFramework\SmartTags();

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);
			
			$file_tags = [
				'index' => $index,
				'total' => $total_files,
				'basename' => $filePathInfo['basename'],
				'filename' => $filePathInfo['filename'],
				'extension' => $filePathInfo['extension'],
				'title' => $title,
				'description' => nl2br($description)
			];
			$st->add($file_tags, 'acf.file.');

			$item .= '>' . $st->replace($link_text) . '</a>';
			break;
	}

	$buffer[] = $layout === 'custom' ? $item : '<span class="acfup-item">' . $item . '</span>';

	$index++;
}

echo implode('', $buffer);