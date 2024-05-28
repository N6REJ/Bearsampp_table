<?php
/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

class PlgButtonSmilePackSmartTags extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;

    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

	/**
	 * Smile Pack Smart Tags Button
	 *
	 * @param  string  $name  The name of the button to add
	 *
	 * @return JObject  The button object
	 */
	public function onDisplay($name)
	{
		$component = $this->app->input->getCmd('option');
		$basePath  = $this->app->isClient('administrator') ? '' : 'administrator/';
		$link      = $basePath . 'index.php?option=com_smilepack&amp;view=smilepack&amp;layout=button&amp;tmpl=component&e_name=' . $name . '&e_comp='. $component;

		$button          = new CMSObject;
		$button->modal   = true;
		$button->link    = $link;
		$button->text    = Text::_('PLG_EDITORS-XTD_SMILEPACKSMARTTAGS_INSERTER');
		$button->name    = 'smile-pack-smart-tags-inserter';

		if (defined('nrJ4'))
		{
			$button->options = [
				'modalWidth' => '60',
			];
		}
		else 
		{
			$button->options = "{handler: 'iframe', size: {x: 800, y: 400}}";
		}

		return $button;
	}
}