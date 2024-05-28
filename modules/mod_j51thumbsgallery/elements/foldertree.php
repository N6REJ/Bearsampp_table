<?php
/**
* @title		Simple image gallery module
* @website		http://www.joomshaper.com
* @copyright	Copyright (C) 2010 JoomShaper.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\FormField;

class JFormFieldFolderTree extends FormField
{
	protected $type = 'FolderTree';

	protected function getInput()
	{
		$options = array();
		// path to images directory
		$path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$this->element['directory'];
		$filter		= $this->element['filter'];
		$folders	= Folder::listFolderTree($path, $filter);

		foreach ($folders as $folder)
		{
			$options[] = HTMLHelper::_('select.option', str_replace(DIRECTORY_SEPARATOR,"/",$folder['relname']), str_replace(DIRECTORY_SEPARATOR,"/",substr($folder['relname'], 1)));
		}

		return HTMLHelper::_('select.genericlist', $options, $this->name, 'class="form-select"', 'value', 'text', $this->value);
	}
}
