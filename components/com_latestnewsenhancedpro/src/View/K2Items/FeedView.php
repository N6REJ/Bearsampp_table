<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Component\LatestNewsEnhancedPro\Site\View\K2Items;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\AbstractView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Document\Feed\FeedEnclosure;
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use SYW\Component\LatestNewsEnhancedPro\Site\Helper\Helper;

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

		$doc->link = Route::_('index.php?option=com_latestnewsenhancedpro&view=k2items');

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

		// extra field ids of fields used in the view
		$extrafield_ids = $this->getExtraFieldIds();

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
 			
 			// add extra fields
 			$fields = $this->getExtraFields($item, $extrafield_ids);
 			
 			if (!empty($fields) && $params->get('fields_in_feed', 0) == 2) { // add fields as syntax
 			    foreach ($fields as $field) {
 			        $feeditem->description .= '{field alias=[' . $field['alias'] . '] name=[' . $field['name'] . '] type=[' . $field['type'] . ']}' . trim($field['value']) . '{/field}';
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

	protected function getExtraFields($item, $field_ids = [])
	{
		$flow_content = '<a><abbr><address><audio><b><bdi><bdo><blockquote><br><button><canvas><cite><code><data><datalist><del><details><dfn><div><dl><em><embed><fieldset><figure><form><h1><h2><h3><h4><h5><h6><hgroup><hr><i><iframe><img><input><ins><kbd><label><main><map><mark><math><menu><meter><object><ol><output><p><picture><pre><progress><q><ruby><s><samp><select><small><span><strong><sub><sup><svg><table><textarea><time><u><ul><var><video><wbr>';
		// allowed in <dd> and <li>
		// removed: $sectioning_content = '<article><aside><header><footer><nav><section>';
		// removed: $script_supporting = '<noscript><script><template>';

		$fields_to_use = [];
		
		if ($item->extra_fields) {

			$item_extra_fields = json_decode($item->extra_fields);

			foreach ($item_extra_fields as $item_extra_field) {
			    foreach ($field_ids as $display => $field_id_array) {
		    	    foreach ($field_id_array as $field_id) {
		    	        if ((int)$item_extra_field->id === (int)$field_id) { // we don't get all extra fields, just the ones selected in the view
							$field_values = $item_extra_field->value; // can be array or string
							if ($field_values) {
								$field_info = Helper::getK2ExtraFieldInfo($field_id);
								if (!empty($field_info)) {
									$alias = '';

									if ($field_info['type'] == 'textarea') {
										$editor_value = HTMLHelper::_('content.prepare', nl2br($field_values));
										$field_values = strip_tags($editor_value, $flow_content);
									} else if ($field_info['type'] == 'select' || $field_info['type'] == 'multipleSelect') {
										$default_values = json_decode($field_info['value']);
										if (count($default_values) > 1) { // multi-value field
											$real_values = array(); // we want the name of the value, not the raw value. ex: 'high' instead of '1'
											foreach ($default_values as $default_value) {
												if (in_array((string)$default_value->value, $field_values)) {
													$real_values[] = $default_value->name;
												}
											}
											if (!empty($real_values)) {
												$field_values = $real_values;
											}
										} else { // single-value field
// 											if (isset($default_values[0]->alias)) {
// 												$alias = strtolower($default_values[0]->alias); // get alias from single value - no use, many fields have an empty alias and when created, it is not Joomla standardized
// 											}
										}
									}

									if (empty($alias)) {
										// build alias from name, if no alias provided
										$alias = ApplicationHelper::stringURLSafe($field_info['name']);
									}

									$fields_to_use[$field_id] = ['alias' => $alias, 'name' => $field_info['name'], 'type' => $field_info['type'], 'value' => (is_array($field_values) ? implode(', ', $field_values) : $field_values), 'display' => $display];
								}
							}
						}
		    	    }
				}
			}
		}

		return $fields_to_use;
	}

	protected function getExtraFieldIds()
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

					if ($information_bloc->info != 'none' && strpos($information_bloc->info, 'k2field') !== false) {
					    $field_ids[$display][] = (explode(':', $information_bloc->info))[2];
					}
				}
			}
		}

		return $field_ids;
	}

}
