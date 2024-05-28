<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\System\AdminTools\Feature;

defined('_JEXEC') || die;

use Exception;
use Joomla\CMS\User\User;
use Akeeba\Plugin\System\AdminTools\Utility\RescueUrl;

class CustomCriticalFilesMonitoring extends Base
{
	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$criticalFilesGlobal = $this->wafParams->getValue('criticalfiles_global', []);

		return !empty($criticalFilesGlobal);
	}

	public function onAfterRender(): void
	{
		$mustSaveData = false;

		$criticalFiles = $this->wafParams->getValue('criticalfiles_global', []);

		if (is_string($criticalFiles))
		{
			$criticalFiles = array_map('trim', explode(',', $criticalFiles));
		}

		$criticalFiles = array_map(
			function ($x) {
				return is_array($x) ? $x[0] : $x;
			}, is_array($criticalFiles) ? $criticalFiles : []
		);

		$loadedFiles  = $this->load();
		$alteredFiles = [];
		$filesToSave  = [];

		if (is_string($criticalFiles))
		{
			$criticalFiles = array_map('trim', explode(',', $criticalFiles));
		}

		foreach ($criticalFiles ?: [] as $relPath)
		{
			$curInfo = $this->getFileInfo($relPath);

			if ($curInfo == false)
			{
				// Did that file exist? If so, we need to save the critical files list.
				if (is_array($loadedFiles) && array_key_exists($relPath, $loadedFiles))
				{
					$mustSaveData = true;
				}

				continue;
			}

			$filesToSave[$relPath] = $curInfo;

			// Did the file change?
			$oldInfo = null;

			if (isset($loadedFiles[$relPath]))
			{
				$oldInfo = $loadedFiles[$relPath];
			}

			// File changed or it was added later
			if ($oldInfo !== $curInfo)
			{
				$mustSaveData = true;

				// If it was added later, there's no need to send and email
				if ($oldInfo !== null)
				{
					$alteredFiles[$relPath] = [$oldInfo, $curInfo];
				}
			}
		}

		if ($mustSaveData)
		{
			$this->save($filesToSave);
		}

		if (!empty($alteredFiles))
		{
			$this->sendEmail($alteredFiles);
		}
	}

	/**
	 * Returns information about a file
	 *
	 * @param   string  $relPath  The path to the file relative to the site's root
	 *
	 * @return  null|array  Null if the file is not there, object with information otherwise
	 */
	protected function getFileInfo($relPath)
	{
		$absolutePath = JPATH_SITE . '/' . $relPath;

		if (!file_exists($absolutePath))
		{
			return null;
		}

		return [
			'size'      => @filesize($absolutePath),
			'timestamp' => filemtime($absolutePath),
			'md5'       => @md5_file($absolutePath),
			'sha1'      => @sha1_file($absolutePath),
		];
	}

	/**
	 * Load the critical file information from the database
	 *
	 * @return  array
	 */
	protected function load()
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->quoteName('value'))
			->from($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('criticalfiles_global'));
		$db->setQuery($query);

		$error = 0;

		try
		{
			$jsonData = $db->loadResult();
		}
		catch (Exception $e)
		{
			$error = $e->getCode();
		}

		if (method_exists($db, 'getErrorNum') && $db->getErrorNum())
		{
			$error = $db->getErrorNum();
		}

		if ($error)
		{
			$jsonData = null;
		}

		if (empty($jsonData))
		{
			return [];
		}

		return json_decode($jsonData, true);
	}

	/**
	 * Save the critical file information to the database
	 *
	 * @param   array  $fileList  The list of critical file information
	 *
	 * @return  void
	 */
	protected function save(array $fileList)
	{
		$db   = $this->db;
		$data = json_encode($fileList);

		$db->transactionStart();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('criticalfiles_global'));
		$db->setQuery($query);
		$db->execute();

		$object = (object) [
			'key'   => 'criticalfiles_global',
			'value' => $data,
		];

		try
		{
			$db->insertObject('#__admintools_storage', $object);
		}
		catch (Exception $e)
		{
			// Ignore this
		}

		$db->transactionCommit();
	}

	/**
	 * Sends a warning email to the addresses set up to receive security exception emails
	 *
	 * @param   array  $alteredFiles  The files which were modified
	 *
	 * @return  void
	 */
	private function sendEmail($alteredFiles)
	{
		if (empty($alteredFiles))
		{
			// What are you doing here? There are no altered files.
			return;
		}

		// Load the component's administrator translation files
		$jlang = $this->app->getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Convert the list of modified files to plain text and HTML lists
		$plainTextInfo = implode("\n", array_map(function ($line) {
			return "* $line";
		}, array_keys($alteredFiles)));

		$htmlInfo = implode("\n", array_map(function ($line) {
			return "<li>$line</li>";
		}, array_keys($alteredFiles)));

		$htmlInfo = empty($htmlInfo) ? '' : "<ul>\n$htmlInfo</ul>";

		// Construct the replacement table
		$substitutions = [
			'INFO'      => $plainTextInfo,
			'INFO_HTML' => $htmlInfo,
		];

		try
		{
			$recipients = $this->wafParams->getValue('emailbreaches', '');
			$recipients = is_array($recipients) ? $recipients : explode(',', $recipients);
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				$recipientUser            = new User();
				$recipientUser->username  = $recipient;
				$recipientUser->name      = $recipient;
				$recipientUser->email     = $recipient;
				$recipientUser->guest     = 0;
				$recipientUser->block     = 0;
				$recipientUser->sendEmail = 1;
				$data                     = array_merge(RescueUrl::getRescueInformation($recipient), $substitutions);

				$this->exceptionsHandler->sendEmail('com_admintools.criticalfiles_global', $recipientUser, $data);
			}
		}
		catch (Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}

}
