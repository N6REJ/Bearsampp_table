<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class JFormFieldACFVideo extends TextField
{
    /**
	 * Renders the input field with the video previewer.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$this->setHint();

		if (!$this->isPreviewerEnabled())
		{
			return parent::getInput();
		}

		$provider = (string) $this->element['provider'];
		
		$this->class = 'acf-video-url-input-value';

		if (!defined('nrJ4'))
		{
			$this->class .= ' input-xxlarge';
		}
		
		$this->assets();

		$input = '';
		
		if ($provider === 'SelfHostedVideo' && defined('nrJ4'))
		{
			$xml = '
				<field name="' . $this->fieldname . '"
					class="acf-video-url-input-value"
					type="media"
					preview="false"
					types="videos"
					hiddenLabel="true"
				/>
			';

			$this->form->setField(new SimpleXMLElement($xml));
			$field = $this->form->getField($this->fieldname, null, $this->value);
			$field->name = $this->name;
			$field->id = $this->id;
			
			$input = $field->getInput();
		}
		else
		{
			$input = parent::getInput();
		}
		
		return $input . $this->getPreviewerHTML();
	}

	private function setHint()
	{
		$provider = $this->element['provider'] ? (string) $this->element['provider'] : '';
		
		switch ($provider)
		{
			case 'YouTube':
				$this->hint = 'https://www.youtube.com/watch?v=IWVJq-4zW24';
				break;
			case 'Vimeo':
				$this->hint = 'https://vimeo.com/146782320';
				break;
			case 'Dailymotion':
				$this->hint = 'https://www.dailymotion.com/video/x8mvsem';
				break;
			case 'FacebookVideo':
				$this->hint = 'https://www.facebook.com/watch/?v=441279306439983';
				break;
			case 'SelfHostedVideo':
				$this->hint = '/media/video.mp4';
				break;
		}
	}

	private function assets()
	{
		HTMLHelper::stylesheet('plg_fields_acfvideo/previewer.css', ['relative' => true, 'version' => 'auto']);
		HTMLHelper::script('plg_fields_acfvideo/previewer.js', ['relative' => true, 'version' => 'auto']);
	}

	private function getPreviewerHTML()
	{
		return '<div class="acf-video-previewer-wrapper" data-root-url="' . Uri::root() . '" title="' . Text::_('ACF_VIDEO_PREVIEW_VIDEO') . '"></div>';
	}

	private function isPreviewerEnabled()
	{
		$plugin = PluginHelper::getPlugin('fields', 'acfvideo');
		$params = new Registry($plugin->params);

		return $params->get('enable_previewer', '1') === '1';
	}
}
