<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

namespace SYW\Module\LatestNewsEnhanced\Site\Field;

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use SYW\Library\Field\DynamicsingleselectField;
use SYW\Library\K2 as SYWK2;

class DatasourceselectField extends DynamicsingleselectField
{
	public $type = 'Datasourceselect';

	protected function getOptions()
	{
	    $options = parent::getOptions();

		$options[] = array('k2', Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_VALUE_K2ITEMS'), '', '', '', !SYWK2::exists());

		if (PluginHelper::isEnabled('content', 'lnedatasources')) {

		    PluginHelper::importPlugin('latestnewsenhanced');

    		// Trigger the form preparation event.
    		$results = Factory::getApplication()->triggerEvent('onLatestNewsEnhancedPrepareSelection');

    		$lang = Factory::getLanguage();
    		foreach ($results as $result) {
    			$lang->load('plg_latestnewsenhanced_'.$result['type'].'.sys', JPATH_ADMINISTRATOR, $lang->getTag(), true);

    			if (File::exists(JPATH_SITE . '/media/plg_latestnewsenhanced_'.$result['type'].'/images/'.$result['type'].'.png')) {
    			    $options[] = array($result['type'], Text::_('PLG_LATESTNEWSENHANCED_'.strtoupper($result['type']).'_TITLE'), '', Uri::root(true).'/media/plg_latestnewsenhanced_'.$result['type'].'/images/'.$result['type'].'.png', '', !$result['enabled']);
    			} else {
    			    $options[] = array($result['type'], Text::_('PLG_LATESTNEWSENHANCED_'.strtoupper($result['type']).'_TITLE'), '', '', '', !$result['enabled']);
    			}
    		}
		}

		$imagefolder = '/media/mod_latestnewsenhanced/images/datasources';

		foreach ($options as &$option) {

		    if (empty($option[3])) {
    		    if ($option[0] == 'articles') {
    		        $image = 'articles';
    		    } else if ($option[0] == 'k2') {
    		        $image = 'k2';
    		    } else {
    		        $image = 'unknown';
    		    }

    		    $option[3] = Uri::root(true).$imagefolder.'/'.$image.'.png';
		    }
		}

		return $options;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
	    $return = parent::setup($element, $value, $group);

	    if ($return) {
	        $this->width = 100;
	        $this->height = 100;
	    }

	    // if we were to enable editing of the module in the frontend, use modules.apply
	    if (Factory::getApplication()->isClient('administrator')) {

		    $wam = Factory::getApplication()->getDocument()->getWebAssetManager();
		    $wam->useScript('webcomponent.core-loader');

		    $wam->addInlineScript('
				document.addEventListener("readystatechange", function(event) {
					if (event.target.readyState == "complete") {
						document.getElementById("' . $this->id . '_id").addEventListener("change", function(event) {
							let spinner = document.createElement("joomla-core-loader");
							document.body.appendChild(spinner);
							Joomla.submitbutton("module.apply");
						});
					}
				});
			');
	    }

	    return $return;
	}
}
?>