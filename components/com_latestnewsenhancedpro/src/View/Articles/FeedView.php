<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\View\Articles;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\AbstractView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Document\Feed\FeedEnclosure;
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

/**
 * RSS View class for the Latest News Enhanced Pro component
 */
class FeedView extends AbstractView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$doc = Factory::getDocument();
		$params = $app->getParams();

		$doc->link = Route::_('index.php?option=com_latestnewsenhancedpro&view=articles');

		$app->input->set('limitstart', 0);

		if ($params->get('feed_limit_default', 1)) {
		    $app->input->set('limit', $app->get('feed_limit', 10));
		} else {
		    $app->input->set('limit', $params->get('feed_limit', 10));
		}

		$app->setUserState('global.ajaxlist.itemcount', 0);

		$siteEmail = $app->get('mailfrom');
		$fromName = $app->get('fromname');
		$feedEmail = $app->get('feed_email', 'none');

		$doc->editor = $fromName;

		if ($feedEmail != "none") {
			$doc->editorEmail = $siteEmail;
		}

		// get data from the model
		$items = $this->get('Items');

		// custom field ids of fields used in the view
		$customfield_ids = $this->getCustomFieldIds();

		foreach ($items as $item) {

			$feeditem = new FeedItem;

			// title

			$title = htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');
			$feeditem->title = html_entity_decode($title, ENT_COMPAT, 'UTF-8'); // strip HTML from feed item title

			// link

			$feeditem->link = $item->link;
			
			// image
			
			$enclosure_for_image = false;
			
			if (isset($item->imagesrc) && $item->imagesrc) {
			    
			    $length = 0;
			    $filesize = @filesize(JPATH_SITE . '/' . $item->imagesrc);
			    if ($filesize !== false) {
			        $length = $filesize;
			    }
			    
			    $type = 'unknown';
			    if ($length > 0) {
    			    if (\function_exists('mime_content_type')) {
    			        // We have mime magic.
    			        $type = mime_content_type(JPATH_SITE . '/' . $item->imagesrc);
    			    } elseif (\function_exists('finfo_open')) {
    			        $finfo = finfo_open(FILEINFO_MIME_TYPE);
    			        $type = finfo_file($finfo, JPATH_SITE . '/' . $item->imagesrc);
    			        finfo_close($finfo);
    			    }
			    }
			    
			    if ($length !== 0 && $type !== 'unknown') {
			        
			        $enclosure = new FeedEnclosure();
			        
			        $enclosure->url = preg_match('/http/', $item->imagesrc) ? $item->imagesrc : Uri::root() . $item->imagesrc;
			        $enclosure->length = $length;
			        $enclosure->type = $type;
			        
			        $feeditem->enclosure = $enclosure;
			        
			        $enclosure_for_image = true;
			    }
			}

			// description

			$feeditem->description = '';
			
			// custom fields
			$fields = $this->getCustomFields($item, $customfield_ids);
			
			if (!empty($fields) && $params->get('fields_in_feed', 0) == 2) { // add fields as syntax
		        foreach ($fields as $field) {
                    $feeditem->description .= '{field alias=[' . $field['alias'] . '] name=[' . $field['name'] . '] type=[' . $field['type'] . ']}' . $field['rawvalue'] . '{/field}';
		        }
			}

			// add image, if any
			if (!$enclosure_for_image && isset($item->imagesrc) && $item->imagesrc) {
				$image = preg_match('/http/', $item->imagesrc) ? $item->imagesrc : Uri::root() . $item->imagesrc;
				$feeditem->description .= '<p class="item_image"><img src="' . $image . '" /></p>';
			}

			if (!empty($fields) && $params->get('fields_in_feed', 0) == 1) { // add fields as HTML

				$output = '';
				foreach ($fields as $field) {

				    $field['value'] = trim($field['value']);

				    if ($field['value'] !== '' && $field['display'] !== 'after_information_blocks') {
						$output .= '<dt class="' . $field['type'] . '">' . $field['name'] . '</dt>';
						$output .= '<dd>' . $field['value'] . '</dd>';
				    }
				}

				if ($output) {
					$feeditem->description .= '<dl class="fields">' . $output . '</dl>';
				}
			}

			$feeditem->description .= $item->text;

			if (!empty($fields) && $params->get('fields_in_feed', 0) == 1) { // add fields as HTML

		        $output = '';
		        foreach ($fields as $field) {

		            $field['value'] = trim($field['value']);

		            if ($field['value'] !== '' && $field['display'] === 'after_information_blocks') {
                        $output .= '<dt class="' . $field['type'] . '">' . $field['name'] . '</dt>';
                        $output .= '<dd>' . $field['value'] . '</dd>';
    		        }
		        }

		        if ($output) {
		            $feeditem->description .= '<dl class="fields">' . $output . '</dl>';
		        }
			}

			// date

 			$feeditem->date = '';
 			if (isset($item->calendar_date) && $item->calendar_date) {
 				$feeditem->date = date('r', strtotime($item->calendar_date));
 			} else {
 				$feeditem->date = date('r', strtotime($item->date));
 			}

 			// category

 			$feeditem->category = $item->category_title;

 			// author

 			$feeditem->author = $item->author;

			if ($feedEmail == 'site') {
				$feeditem->authorEmail = $siteEmail;
			} elseif ($feedEmail === 'author') {
				$feeditem->authorEmail = $item->author_email;
			}

			// loads item info into RSS array
			$doc->addItem($feeditem);
		}
	}

	protected function getCustomFields($item, $field_ids = [])
	{
		$fields_to_use = [];
	    
	    $fields = FieldsHelper::getFields('com_content.article', $item, true);

		foreach ($fields as $field) {
			foreach ($field_ids as $display => $field_id_array) {
				foreach ($field_id_array as $field_id) {
					if ((int)$field->id === (int)$field_id && !empty($field->value)) {

						$raw_value = $field->rawvalue;
						if (is_array($field->rawvalue)) {
							$raw_value = implode(',', $field->rawvalue);
						}

						$fields_to_use[$field_id] = ['alias' => $field->name, 'name' => $field->label, 'type' => $field->type, 'rawvalue' => $raw_value, 'value' => $field->value, 'display' => $display];
					}
				}
			}
		}

		return $fields_to_use;
	}

	protected function getCustomFieldIds()
	{
        $field_ids = [];

		$app = Factory::getApplication();
		$params = $app->getParams();

		$displays = ['over_head_information_blocks', 'before_title_information_blocks', 'before_information_blocks', 'after_information_blocks'];

		foreach ($displays as $display) {
            $field_ids[$display] = [];
			$information_blocs = $params->get($display); // array of objects
			if (!empty($information_blocs) && is_array($information_blocs)) {
				foreach ($information_blocs as $information_bloc) {
					$information_bloc = (object)$information_bloc;

					if ($information_bloc->info != 'none' && strpos($information_bloc->info, 'jfield') !== false) {
						$field_ids[$display][] = (explode(':', $information_bloc->info))[2];
					}
				}
			}
		}

		return $field_ids;
	}

}
