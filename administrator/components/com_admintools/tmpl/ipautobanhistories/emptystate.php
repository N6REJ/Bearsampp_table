<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_ADMINTOOLS_IPAUTOBANHISTORIES',
	'formURL'    => 'index.php?option=com_admintools&view=ipautobanhistories',
	//'helpURL'    => 'https://docs.joomla.org/Special:MyLanguage/Adding_a_new_article',
	'icon'       => 'fa fa-history',
	'createURL'  => 'index.php?option=com_admintools&view=Webapplicationfirewall',
];

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
