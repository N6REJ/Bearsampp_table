<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class Updatesites
{
	/**
	 * Joomla Database Class
	 *
	 * @var  object
	 */
	private $db;

	/**
	 * The download key.
	 * 
	 * @var  string
	 */
	private $key;

	/**
	 *  Consturction method
	 *
	 *  @param  string  $key  Download Key
	 */
	public function __construct($key = null)
	{
		$this->db  = Factory::getDBO();
		$this->key = ($key) ? $key : $this->getDownloadKey();
   	}

   	/**
   	 *  Main method
   	 */
   	public function update()
   	{
		$this->removeDuplicates();
		$this->updateHttptoHttps();
		$this->removeInstallerUpdateSite();
	}
	   
	/**
	 *  Reads the Download Key saved in the framework plugin parameters
	 *
	 *  @return  string  The Download Key
	 */
	public function getDownloadKey()
	{
		$hash = 'nrframework_download_key';

		$cache = Cache::read($hash);

        if ($cache)
        {
            return $cache;
        }
		
		$query = $this->db->getQuery(true)
			->select('e.params')
			->from('#__extensions as e')
			->where('e.element = ' . $this->db->quote('nrframework'));

		$this->db->setQuery($query);

		if (!$params = $this->db->loadResult())
		{
			return;
		}

		$params = json_decode($params);

		if (!isset($params->key))
		{
			return;
		}

        return Cache::set($hash, trim($params->key));
	}

	/**
	 * Remove our Installer's update site left over from the database
	 *
	 * @return void
	 */
	private function removeInstallerUpdateSite()
	{
		$query = $this->db->getQuery(true)
			->delete('#__update_sites')
			->where($this->db->quoteName('name') . ' = ' . $this->db->quote('System - Novarain Installer'));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * Update http to https
	 *
	 * @return void
	 */
	private function updateHttptoHttps()
	{
		$query = $this->db->getQuery(true)
			->update('#__update_sites')
			->set($this->db->quoteName('location') . ' = REPLACE('
				. $this->db->quoteName('location') . ', '
				. $this->db->quote('http://') . ', '
				. $this->db->quote('https://')
				. ')')
			->where($this->db->quoteName('location') . ' LIKE ' . $this->db->quote('%tassos.gr%'));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * Remove duplicate update sites created by upgrading from Free to Pro version
	 *
	 * @return void
	 */
	private function removeDuplicates()
	{
		$db = $this->db;

		// Find duplicates first
		$query = 'SELECT name, COUNT(*) c FROM #__update_sites where location like "%tassos.gr%" GROUP BY name HAVING c > 1';
		$db->setQuery($query);

		if (!$duplicates = $db->loadObjectList())
		{
			return;
		}

		// OK we have duplicates. Let's remove them.
		foreach ($duplicates as $key => $duplicate)
		{
			// Get all IDs
			$query = $db->getQuery(true)
				->select('update_site_id')
				->from('#__update_sites')
				->where('name = ' . $db->quote($duplicate->name))
				->order('update_site_id DESC');

			$db->setQuery($query);

			if (!$update_site_ids = $db->loadObjectList())
			{
				return;
			}

			// Skip the 1st index which represents the last created and valid.
			unset($update_site_ids[0]);

			foreach ($update_site_ids as $key => $update_site_id)
			{
				$id = $update_site_id->update_site_id;

				$query->clear()
					->delete('#__update_sites')
					->where($db->quoteName('update_site_id') . ' = ' . (int) $id);

				$db->setQuery($query);
				$db->execute();
	
				$query->clear()
					->delete('#__update_sites_extensions')
					->where($db->quoteName('update_site_id') . ' = ' . (int) $id);

				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}