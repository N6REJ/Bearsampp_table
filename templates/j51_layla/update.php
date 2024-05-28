<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @since  1.0.6v1
 */
class J51_LaylaInstallerScript extends InstallerScript
{

	/**
	 * Ensure the core templates are correctly moved to the new mode.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	protected function fixTemplateMode(): void
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$template = 'j51_layla';
		$clientId = 0;
		$query = $db->getQuery(true)
			->update($db->quoteName('#__template_styles'))
			->set($db->quoteName('inheritable') . ' = 1')
			->where($db->quoteName('template') . ' = ' . $db->quote($template))
			->where($db->quoteName('client_id') . ' = ' . $clientId);
    
		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			echo Text::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br>';

			return;
		}
	}
	
	public function postflight($type, $installer)
 	{
		$foldersToDelete = array(
			'/templates/j51_layla/html/com_contact',
			'/templates/j51_layla/html/com_content/article',
			'/templates/j51_layla/html/com_finder',
			'/templates/j51_layla/html/com_media',
			'/templates/j51_layla/html/com_search',
			'/templates/j51_layla/html/com_users',
			'/templates/j51_layla/html/layout/joomla',
			'/templates/j51_layla/html/mod_breadcrumbs',
			'/templates/j51_layla/html/mod_custom',
			'/templates/j51_layla/html/mod_finder',
			'/templates/j51_layla/html/mod_login',
			'/templates/j51_layla/html/mod_search',
			'/templates/j51_layla/html/mod_tags_popular',
			'/templates/j51_layla/html/plg_content_pagenavigation',
			'/templates/j51_layla/html/plg_content_vote'
		);

		$filesToDelete = array(
			'/templates/j51_layla/html/modules.php'
		);

		foreach ($foldersToDelete as $folder)
		{
			if ($folderExists = Folder::exists(JPATH_ROOT . $folder))
			{
				Folder::delete(JPATH_ROOT . $folder);
			}
		}

		foreach ($filesToDelete as $file)
		{
			if ($fileExists = File::exists(JPATH_ROOT . $file))
			{
				File::delete(JPATH_ROOT . $file);
			}
		}

		// Ensure templates are moved to the correct mode
		$this->fixTemplateMode();
 	}
}