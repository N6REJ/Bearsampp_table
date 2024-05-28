<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Feed\Feed;
use Joomla\CMS\Feed\FeedEntry;
use Joomla\CMS\Feed\FeedFactory;
use Joomla\CMS\Feed\FeedLink;
use Joomla\CMS\Feed\FeedPerson;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use SYW\Library\Cache as SYWCache;
use SYW\Library\Text as SYWText;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Library\Version as SYWVersion;
use SYW\Module\LatestNewsEnhanced\Site\Helper\Helper as LNEHelper;

// problem: newsfeeds with https cannot be fetched if allowed_url_fopen is 'on'. When 'off', ok but cannot fetch images
// https://www.nasa.gov/rss/dyn/breaking_news.rss
// https://www.smashingmagazine.com/feed example that gets small image first

// newsfeeds with http can be fetched if allowed_url_fopen is 'on'. Images can be fetched also.
// http://feeds.feedburner.com/Frandroid
// http://www.gsmarena.com/rss-news-reviews.php3 (images available in content)
// http://www.simplifyyourweb.com/index.php/latest-news?format=feed&type=rss
// https://www.joomla.org/announcements.feed?type=rss
// http://feeds.boingboing.net/boingboing/iBag

/*
 * support namespace 'media'
 */
class MyMediaParser extends Joomla\CMS\Feed\Parser\Rss\MediaRssParser
{
	public function processElementForFeedEntry(FeedEntry $entry, \SimpleXMLElement $el)
	{
		// following the specification: https://www.rssboard.org/media-rss/#media-content
		// could be used to find videos as well

		$children = $el->children('media', true); // get all nodes with the 'media' prefix
		foreach($children as $child) {
			$attributes = $child->attributes();
			if (isset($attributes['medium'])) {
				if ($attributes['medium'] == 'image') {
					if (isset($attributes['url'])) {
						$entry->media_image_url = (string) $attributes['url'];
					}
				}
			} else {
				// sometimes, no 'medium' attribute
				if (isset($attributes['type'])) { // see if there is a MIME type
					if (isset($attributes['url'])) {
						// if mime type of image, get it
					    $mime_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif');
						if (in_array((string) $attributes['type'], $mime_types)) {
							$entry->media_image_url = (string) $attributes['url'];
						}
					}
				} else { // sometimes, not even the 'type' attribute
					if (isset($attributes['url'])) { // no idea what the url is, so test the link to make sure it is an image
						$extension_types = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'avif');

						$uri_object = Uri::getInstance((string) $attributes['url']);
						$extension_type = File::getExt($uri_object->getPath());
						if (in_array(strtolower($extension_type), $extension_types)) {
							$entry->media_image_url = (string) $attributes['url'];
						}
					}
				}
			}
		}

		return;
	}
}

/*
 * support namespace 'content'
 */
class MyContentParser implements Joomla\CMS\Feed\Parser\NamespaceParserInterface
{
	public function processElementForFeed(Feed $feed, \SimpleXMLElement $el)
	{
		return;
	}

	public function processElementForFeedEntry(FeedEntry $entry, \SimpleXMLElement $el)
	{
		$children = $el->children('content', true); // get all nodes with the 'content' prefix
		foreach($children as $child) {
			$entry->content_text = (string)$child;
		}

		return;
	}
}

class MyRssParser extends Joomla\CMS\Feed\Parser\RssParser
{
    public function __construct(\XMLReader $stream, InputFilter $inputFilter = null)
	{
	    parent::__construct($stream, $inputFilter);
		$this->namespaces['media'] = new MyMediaParser;
		$this->namespaces['content'] = new MyContentParser;
	}

	/**
	 * By using the original RSSParser, we get the link from atom
	 * <link>http://www.nasa.gov/</link>
	 * <atom:link rel="self" href="http://www.nasa.gov/rss/dyn/breaking_news.rss" />
	 */
	protected function handleLink(Feed $feed, \SimpleXMLElement $el)
	{
		$link = new FeedLink;
		$link->uri = (string) $el;
		if ($link->uri) {
			$feed->link = $link;
		}
	}

	protected function processFeedEntry(FeedEntry $entry, \SimpleXMLElement $el) {

		parent::processFeedEntry($entry, $el);

		// Add source element support

		if (isset($el->source) && !empty($el->source)) {

			$source = new Feed;

			$source->title = $this->inputFilter->clean((string) $el->source, 'html');

			if (isset($el->source['url'])) {
				$source->uri = (string)$el->source['url'];
			}

			$entry->__set('source', $source);
		}
	}

	/**
	 * parse the author data
	 * Joomla parser assumes the author string to be [author_email_address (Author's name)]
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Feed\Parser\RssParser::processPerson()
	 */
	protected function processPerson($data)
	{
		// Create a new person object.
		$person = new FeedPerson;

		$data = explode(' ', trim($data), 2); // trim to avoid leading and trailing spaces - resulting in 2 non-empty elements at most

		$first_part = (string) $data[0];
		if (filter_var($first_part, FILTER_VALIDATE_EMAIL) !== false) { // the first part is an email address
			$person->email = filter_var($first_part, FILTER_VALIDATE_EMAIL);
			if (isset($data[1])) {
				$person->name = trim($this->inputFilter->clean($data[1], 'html'), ' ()');
			}
		} else {
			$person->name = trim(implode(' ', $data), ' ()');
		}

		return $person;
	}
}

class plgLatestNewsEnhancedNewsfeeds extends CMSPlugin
{
	protected $type = 'newsfeeds';
	protected $autoloadLanguage = true;

	/*
	 * Return the data type and availability
	 */
	function onLatestNewsEnhancedPrepareSelection()
	{
		return array('type' => $this->type, 'enabled' => true);
	}

	/*
	 * Form save
	 */
	function onLatestNewsEnhancedBeforeSave($item, $isNew)
	{
		$params = new Registry($item->params);

		if ($params->get('datasource') == $this->type) {

			// get the data from the form to get fields specific to the data source
			$jformData = Factory::getApplication()->input->get('jform', [], 'array');

			if (array_key_exists('newsfeeds_url', $jformData['params'])) {
				$params->set('newsfeeds_url', $jformData['params']['newsfeeds_url']);
			}
			
			if (array_key_exists('newsfeeds_count_for_feed', $jformData['params'])) {
				$params->set('newsfeeds_count_for_feed', $jformData['params']['newsfeeds_count_for_feed']);
			}

			if (array_key_exists('newsfeeds_order', $jformData['params'])) {
				$params->set('newsfeeds_order', $jformData['params']['newsfeeds_order']);
			}

			$item->params = $params->toString();
		}
	}

	/*
	 * Form loading
	 */
	function onLatestNewsEnhancedPrepareForm($form, $data)
	{
		// Add the newsfeeds fields to the form

	    if (isset($data->params['datasource']) && $data->params['datasource'] == $this->type) {

			// remove fields from module form that will be re-ordered and re-placed in the form

			// parameters are replaced if identical
			// all others do not show because of the showon attribute, but are still saved
			// therefore would be good to remove the unneeded parameters that are linked to datasource:articles or k2 (not only in selection)

			$form->removeField('related', 'params');
			$form->removeField('cat_inex', 'params');
			$form->removeField('catid', 'params');
			$form->removeField('k2catid', 'params');
			$form->removeField('includesubcategories', 'params');
			$form->removeField('levelsubcategories', 'params');
			$form->removeField('cat_order', 'params');
			$form->removeField('tags_inex', 'params');
			$form->removeField('tags', 'params');
			$form->removeField('k2tags', 'params');
			$form->removeField('include_tag_children', 'params');
			$form->removeField('tags_match', 'params');
			$form->removeField('any_count_min', 'params');
			$form->removeField('any_count_max', 'params');
			$form->removeField('tag_order', 'params');
			$form->removeField('keys', 'params');
			$form->removeField('show_a', 'params');
			$form->removeField('author_match', 'params');
			$form->removeField('author_inex', 'params');
			$form->removeField('author_alias_inex', 'params');
			$form->removeField('created_by', 'params');
			$form->removeField('created_by_alias', 'params');
			$form->removeField('k2_created_by', 'params');
			$form->removeField('k2_created_by_alias', 'params');
			$form->removeField('author_order', 'params');
			$form->removeField('post_d', 'params');
			$form->removeField('when_no_date', 'params');
			$form->removeField('ex_current_item', 'params');
			$form->removeField('ex', 'params');
			$form->removeField('in', 'params');
			$form->removeField('show_unauthorized', 'params');
			$form->removeField('show_f', 'params');
			$form->removeField('order', 'params');
			$form->removeField('filter_lang', 'params');
			$form->removeField('count_for', 'params');

// 			$form->removeField('use_range', 'params');
// 			$form->removeField('start_date_range', 'params');
// 			$form->removeField('end_date_range', 'params');
// 			$form->removeField('range_to', 'params');
// 			$form->removeField('spread_to', 'params');
// 			$form->removeField('range_from', 'params');
// 			$form->removeField('spread_from', 'params');
// 			$form->removeField('relative_example1', 'params');
// 			$form->removeField('relative_example2', 'params');
// 			$form->removeField('relative_example3', 'params');
// 			$form->removeField('count', 'params');
// 			$form->removeField('startat', 'params');

			Form::addFormPath(dirname(__FILE__).'/forms');
			$form->loadFile('newsfeeds', true);
			
			$elements = $form->getXml()->xpath('//fieldset[@name="selection_dynamic"]');
			foreach ($elements as $element) {
			    $element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_categories"]');
			foreach ($elements as $element) {
				$element->attributes()->label = Text::_('PLG_LATESTNEWSENHANCED_NEWSFEEDS_FIELD_FEEDS_LABEL');
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_tags"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_keywords"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_cfields"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_authors"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_exin"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_featured"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}

			$elements = $form->getXml()->xpath('//fieldset[@name="selection_language"]');
			foreach ($elements as $element) {
				$element->attributes()->class = 'd-none';
			}
		}
	}

	/*
	 * Data retrieval
	 *
	 * Each item needs the following data:
	 *
	 * $item->id
	 * $item->tags
	 * $item->date
	 * $item->nbr_seconds
	 * $item->nbr_minutes
	 * $item->nbr_hours
	 * $item->nbr_days
	 * $item->nbr_months
	 * $item->nbr_years
	 * $item->title
	 * $item->text
	 * $item->link
	 * $item->linktarget
	 * $item->linktitle
	 * $item->author
	 * $item->vote
	 * $item->vote_count
	 * $item->imagetag
	 * $item->error (array)
	 * $item->cropped
	 * $item->hits
	 * $item->metakey
	 * $item->urls
	 * $item->category_title
	 * $item->catlink
	 */
	function onLatestNewsEnhancedGetData($type, $params, $module)
	{
		if ($type != $this->type) {
			return null;
		}

		$feed_urls = trim( (string) $params->get('newsfeeds_url', ''));

		if (empty($feed_urls)) {
			return null;
		}

		date_default_timezone_set('UTC');

		$cache = Factory::getCache('plg_latestnewsenhanced_newsfeeds', '');
		$cache->setCaching(true);
		$cache->setLifeTime(intval($this->params->get('feed_cache', 60))); // (in minutes)

		$cacheid = md5('newsfeeds_' . $module->id); // must be unique by value

		$items_feeds = array();
		if ($cache->contains($cacheid)) {
			$items_feeds = $cache->get($cacheid);
		}

		if (empty($items_feeds)) {

			$feedFactory = new FeedFactory;

			$feedFactory = $feedFactory->registerParser('rss', 'MyRssParser', true);

			$feed_urls = array_map('trim', (array) explode("\n", $feed_urls));
			foreach ($feed_urls as $feed_url) {

				try {
					$feed = $feedFactory->getFeed($feed_url);
				} catch (\Exception $e) {
				    //throw new \RuntimeException('Unable to retrieve the feed: ' . $e->getMessage());
				    continue;
				}

				if (empty($feed)) {
					//throw new \RuntimeException('Empty feed.');
					continue;
				}

				//$feed_description = $feed->__get('description') != null ? $feed->__get('description') : null;

				if ($params->get('newsfeeds_order', 'date_dsc_mix') == 'date_asc_mix' || $params->get('newsfeeds_order', 'date_dsc_mix') == 'date_asc') {
					$feed->reverseItems();
				}

				$feed_items = self::getFeedItems($feed, $params); // each feed is sorted by date or reversed dates

				if (!empty($feed_items)) {
					// restrict feed items to range, if necessary

					if ($params->get('use_range', 0) == 1) { // check date range

						$range_from = $params->get('range_from', 'now');
						$spread_from = $params->get('spread_from', 1);
						$range_to = $params->get('range_to', 'week');
						$spread_to = $params->get('spread_to', 1);
						$direction = '-';

						if ($range_from != 'now') {
							$limit_from = strtotime($direction.$spread_from.' '.$range_from); // ex: '-1 month'
						} else {
							$limit_from = strtotime('now');
						}

						if ($range_to != 'now') {
							$limit_to = strtotime($direction.$spread_to.' '.$range_to);
						} else {
							$limit_to = strtotime('now');
						}

						foreach ($feed_items as $key => $feed_item) {

							if ($limit_from < $limit_to) {
								if ($feed_item->timestamp > $limit_to || $feed_item->timestamp < $limit_from) {
									unset($feed_items[$key]);
								}
							} elseif ($limit_from > $limit_to) {
								if ($feed_item->timestamp > $limit_from || $feed_item->timestamp < $limit_to) {
									unset($feed_items[$key]);
								}
							} else {
								if ($feed_item->timestamp != $limit_from) { // equality should never happen but can still do!
									unset($feed_items[$key]);
								}
							}
						}
					}

					if ($params->get('use_range', 0) == 2) { // between fixed dates

						$startDateRange = strtotime($params->get('start_date_range', 'now'));
						$endDateRange = strtotime($params->get('end_date_range', 'now'));

						foreach ($feed_items as $key => $feed_item) {

							if ($startDateRange < $endDateRange) {
								if ($feed_item->timestamp > $endDateRange || $feed_item->timestamp < $startDateRange) {
									unset($feed_items[$key]);
								}
							} elseif ($startDateRange > $endDateRange) {
								if ($feed_item->timestamp > $startDateRange || $feed_item->timestamp < $endDateRange) {
									unset($feed_items[$key]);
								}
							} else {
								if ($feed_item->timestamp != $endDateRange) { // equality should never happen but can still do!
									unset($feed_items[$key]);
								}
							}
						}
					}
				}

				if (!empty($feed_items)) {

					// sort by title if necessary

					if ($params->get('newsfeeds_order', 'date_dsc_mix') == 'title_asc_mix' || $params->get('newsfeeds_order', 'date_dsc_mix') == 'title_asc') {
						usort($feed_items, array(__CLASS__, 'sortTitleAsc'));
					} else if ($params->get('newsfeeds_order', 'date_dsc_mix') == 'title_dsc_mix' || $params->get('newsfeeds_order', 'date_dsc_mix') == 'title_dsc') {
						usort($feed_items, array(__CLASS__, 'sortTitleDsc'));
					} else if ($params->get('newsfeeds_order', 'date_dsc_mix') == 'random_mix' || $params->get('newsfeeds_order', 'date_dsc_mix') == 'random') {
						shuffle($feed_items);
					}

					$items_feeds[] = $feed_items; // Deprecated: Automatic conversion of false to array is deprecated
				}
			}

			$cache->store($items_feeds, $cacheid);
		}

		if (empty($items_feeds)) {
			return array();
		}

		// clear image cache, if requested

		$config_params = LNEHelper::getConfig();

		$clear_cache = LNEHelper::IsClearPictureCache($params);

		$subdirectory = 'thumbnails/lne';

		$thumb_path = ($params->get('thumb_path', '') == '') ? $config_params->get('thumb_path_mod', 'cache') : $params->get('thumb_path', '');

		if ($thumb_path == 'cache') {
			$subdirectory = 'mod_latestnewsenhanced';
		}
		$tmp_path = SYWCache::getTmpPath($thumb_path, $subdirectory);

		if ($clear_cache) {
		    LNEHelper::clearThumbnails($module->id, $tmp_path);

		    SYWVersion::refreshMediaVersion('mod_latestnewsenhanced_' . $module->id);
		}

		// merge feeds

		$count = trim($params->get('count', ''));
		$count_is_for_feed = $params->get('newsfeeds_count_for_feed', 0);

		$merged_items = array();

		foreach ($items_feeds as $items_feed) {

			if (!empty($count) && $count_is_for_feed) {

				switch ($params->get('newsfeeds_order', 'date_dsc_mix'))
				{
					case 'date_dsc_mix': case 'date_dsc': case 'title_dsc_mix': case 'title_dsc': case 'random_mix': case 'random':
						$items_feed = array_slice($items_feed, 0, intval($count)); break;
					case 'date_asc_mix': case 'date_asc': case 'title_asc_mix': case 'title_asc':
						$items_feed = array_slice($items_feed, -intval($count), intval($count)); break;
				}
			}

			$merged_items = array_merge($merged_items, $items_feed);
		}

		// order by date or title when feeds are mixed

		switch ($params->get('newsfeeds_order', 'date_dsc_mix'))
		{
			case 'date_dsc_mix': usort($merged_items, array(__CLASS__, 'sortDatesDsc')); break;
			case 'date_asc_mix': usort($merged_items, array(__CLASS__, 'sortDatesAsc')); break;
			case 'title_dsc_mix': usort($merged_items, array(__CLASS__, 'sortTitleDsc')); break;
			case 'title_asc_mix': usort($merged_items, array(__CLASS__, 'sortTitleAsc')); break;
			case 'random_mix': shuffle($merged_items);
		}

		// need to create images?

		$show_image = false;
		$image_types = array('image', 'imageintro', 'imagefull', 'allimagesasc', 'allimagesdesc');
		if (in_array($params->get('head_type', 'none'), $image_types)) {
			$show_image = true;
		}

		$count = trim($params->get('count', ''));

		$startat = $params->get('startat', 1);
		if ($startat < 1) {
			$startat = 1;
		}

		$items = array();

		$i = 0; // needed because keys can be like 0 1 2 10 11 12

		foreach ($merged_items as $item) {

			if ($i < $startat - 1) {
				continue;
			}

			if (!empty($count) && $i >= intval($count) && !$count_is_for_feed) {
				break;
			}

			// create images
			if ($show_image) {
				self::getFeedItemImage($item, $params, $module->id, $tmp_path, $clear_cache);
			}

			// alter text
			self::getFeedItemText($item, $params);

			$items[] = $item;

			$i++;
		}

		return $items;
	}

	/*
	 * Return the detail options available
	 */
	function onLatestNewsEnhancedPrepareDetailSelection()
	{
		return array('type' => $this->type, 'options' => array('newsfeeds_feedtitle', 'newsfeeds_entrycategories', 'newsfeeds_entrysource', 'newsfeeds_fields'));
	}

	/*
	 * Data detail retrieval
	 *
	 * $detail array of detailed info
	 * [0] detail name
	 * [1] prepend text
	 * [2] show icon true|false
	 * [3] the icon selected or '' (default)
	 * [4] extra classes to apply to the info
	 *
	 * $params the module parameters
	 * $item object containg all the data for an item
	 * $item_params the items specific parameters
	 */
	function onLatestNewsEnhancedGetDetailData($detail, $params, $item, $item_params)
	{
		$layout_suffix = $params->get('layout_suffix', '');

		if ($detail['info'] == 'newsfeeds_feedtitle' && isset($item->feedtitle)) {

			$layout = new FileLayout('details.lnep_detail_newsfeeds_feedtitle', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component

			if ($layout_suffix) {
				$layout->setSuffixes(array($layout_suffix));
			}

			$data = array('item' => $item, 'params' => $params, 'label' => $detail['prepend'], 'show_icon' => $detail['show_icons'], 'icon' => $detail['icon'], 'extraclasses' => $detail['extra_classes']);

			return $layout->render($data);

		} else if ($detail['info'] == 'newsfeeds_entrycategories' && isset($item->entrycategories)) {

			$listed_categories = array();
			foreach ($item->entrycategories as $category => $v) { // categories are built like 'category' => ''
				$listed_categories[] = ucwords($category);
			}

			if (!empty($listed_categories)) {

				$layout = new FileLayout('details.lnep_detail_newsfeeds_entrycategories', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component

				if ($layout_suffix) {
					$layout->setSuffixes(array($layout_suffix));
				}

				$data = array('item' => $item, 'params' => $params, 'label' => $detail['prepend'], 'show_icon' => $detail['show_icons'], 'icon' => $detail['icon'], 'extraclasses' => $detail['extra_classes']);
				$data['categories'] = $listed_categories;

				return $layout->render($data);
			}
		} else if ($detail['info'] == 'newsfeeds_entrysource' && isset($item->entrysource)) {

			$layout = new FileLayout('details.lnep_detail_newsfeeds_entrysource', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component

			if ($layout_suffix) {
				$layout->setSuffixes(array($layout_suffix));
			}

			$data = array('item' => $item, 'params' => $params, 'label' => $detail['prepend'], 'show_icon' => $detail['show_icons'], 'icon' => $detail['icon'], 'extraclasses' => $detail['extra_classes']);

			return $layout->render($data);

		} else if ($detail['info'] == 'newsfeeds_fields' && isset($item->feedfields)) {

			if (!empty($item->feedfields)) {
				$layout = new FileLayout('details.lnep_detail_newsfeeds_fields', null, array('component' => 'com_latestnewsenhancedpro')); // gets override from component

				if ($layout_suffix) {
					$layout->setSuffixes(array($layout_suffix));
				}

				$data = array('item' => $item, 'params' => $params, 'label' => $detail['prepend'], 'show_icon' => $detail['show_icons'], 'icon' => $detail['icon'], 'extraclasses' => $detail['extra_classes']);

				return $layout->render($data);
			}
		}

		return null;
	}

	protected function getFeedItems($feed, $params, $count = 1000)
	{
		$items = array();

		$feed_title = $feed->__get('title') !== null ? $feed->__get('title') : '';

		$feed_image = $feed->__get('image') !== null ? $feed->__get('image')->uri : ''; // used as default image for each item

		$feed_url = null;
		if ($feed->__get('link') !== null) {
			$feed_url = ($feed->__get('link')->uri !== null) ? $feed->__get('link')->uri : ''; // JFeeLink
		}

		$title_letter_count = trim($params->get('letter_count_title', ''));

		switch ($params->get('link_target', 'default')) {
			case 'same': $link_target = ''; break;
			case 'inline': $link_target = 4; break;
			case 'new': $link_target = 1; break;
			case 'modal': $link_target = 3; break;
			case 'popup': $link_target = 2; break;
			default: $link_target = 'default';
		}

		$i = 0;
		while ($i < $count) {
			//for ($i = 0; $i < $feed->count(); $i++) {

			if (!$feed->offsetExists($i)) {
				break;
			}

			$entry = $feed->offsetGet($i);
			
			$date_temp = $entry->__get('updatedDate');
			
			if ($date_temp === null) {
			    $date_temp = $entry->__get('publishedDate');
			}
			
			if ($date_temp === null) {
			    continue;
			}

			$item = new stdClass();

			//$item->id = md5(JHTML::_('date', $feed[$i]->publishedDate, 'U')); // Unix time in seconds
			$item->id = md5($feed_title . $entry->__get('title') . strtotime($date_temp));

			$item->feeditem_links = $entry->__get('links');

			// title

			$item->title = html_entity_decode($entry->__get('title'), ENT_QUOTES, 'UTF-8'); // replaces entities like &quot;
			//$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8');

			if (!$params->get('force_one_line', false) && strlen($title_letter_count) > 0) {
				$item->title = SYWText::getText($item->title, 'txt', intval($title_letter_count), true, '', false, $params->get('trunc_l_w_title', 0));
			} else {
				$item->title = strip_tags($item->title);
			}

			// link
			$item->link = ($entry->__get('guid') != null && substr($entry->__get('guid'), 0, 4) === 'http') ? $entry->__get('guid') : (($entry->__get('uri') != null && substr($entry->__get('uri'), 0, 4) === 'http') ? $entry->__get('uri') : '');
			$item->link = htmlspecialchars($item->link, ENT_COMPAT, 'UTF-8');

			$item->authorized = true;
			$item->isinternal = false;

			$item->linktitle = $item->title;

			$item->linktarget = ($link_target == 4 || $link_target === 'default') ? 1 : $link_target;

// 			switch ($params->get('link_to', 'article')) {
// 				case 'article': $item->linktarget = 0; break; // open in same window
// 				case 'modal': $item->linktarget = 2; break; // open in popup window, not modal to avoid iframe issues
// 				default: $item->linktarget = 1; // open in new window
// 			}

			// author

			if (is_object($entry->__get('author'))) {
				$entry_author = $entry->__get('author'); // JFeedPerson
				if (isset($entry_author->name)) {
					$item->author = $entry_author->name;
				}
				if (isset($entry_author->email)) {
					$item->author_email = $entry_author->email;
				}
			}

			// date
			// the parsers only handle one date
			// Atom: only updatedDate
			// RSS 2.0: updatedDate and publishedDate contain the published date

			$item->date = $date_temp; // JDate object

			$item->calendar_date = $item->date;

			//$timezone = new DateTimeZone(Factory::getConfig()->get('offset'));
			//$timezone_utc = new DateTimeZone('UTC');
			$timezone_feed = $item->date->getTimezone(); // for instance PST of type 2

			// get offset between tz from feed and UTC

			// $utc_dt = new JDate('now'); // uses UTC
			// $feed_dt = new JDate('now', $timezone_feed);
			// $offset = ($timezone_utc->getOffset($utc_dt) - $timezone_feed->getOffset($feed_dt)) / 3600;
			// for Boing Boing : 8

			// timezones maybe of the wrong type, they should be of type 3
			// if types 1 or 2, impossible to compare dates

			$zone = explode('/', $timezone_feed->getName());
			if ($zone[0] != 'Africa' && $zone[0] != 'America' && $zone[0] != 'Antarctica' && $zone[0] != 'Arctic' && $zone[0] != 'Asia' && $zone[0] != 'Atlantic'
					&& $zone[0] != 'Australia' && $zone[0] != 'Europe' && $zone[0] != 'Indian' && $zone[0] != 'Pacific') { // avoid doing it for tz of type 3

						$temp = new Date('now'); // uses UTC
						$temp->setTimestamp($item->date->getTimestamp());

						// $interval = new DateInterval('PT8H');

						$item->date = $temp; //->sub($interval);
			}

			//$timeoffset = $timezone->getOffset($item->date) / 3600;
			//print_r(' offset '.$timeoffset.' '); // -5h

			//$item->date->setTimezone($timezone);

			// now substract $timeOffset to the date

			//$item->date->setTimestamp($item->date->getTimestamp() + $timeOffset);
			//$item->date->setTimezone($timezone);

			$item->timestamp = strtotime($item->date); // easier for sorting and apply date range

			$details = LNEHelper::date_to_counter($item->date);

			$item->nbr_seconds  = intval($details['secs']);
			$item->nbr_minutes  = intval($details['mins']);
			$item->nbr_hours = intval($details['hours']);
			$item->nbr_days = intval($details['days']);
			$item->nbr_months = intval($details['months']);
			$item->nbr_years = intval($details['years']);

			// text before treatment

			$item->text = (!is_null($entry->__get('content'))) ? $entry->__get('content') : ''; // $feed[$i]->description; // content contains description by design

			if ($entry->__get('content_text')) {
				$item->content_text = $entry->__get('content_text');
			}

			$item->cropped = true;

			// image

			$item->imagetag = '';
			$item->error = array();

			if ($feed_image) {
				$item->defaultimage = $feed_image;
			}

			$item->media = array();
			if ($entry->__get('media_image_url')) {
				$item->media['image'] = $entry->__get('media_image_url');
			}

			// categories

			if (is_array($entry->__get('categories')) && count($entry->__get('categories')) > 0) {
				$item->entrycategories = $entry->__get('categories');
			}

			// source

			if ($entry->__get('source') !== null) {
				$source = $entry->__get('source'); // JFeed
				$item->entrysource = array('title' => htmlspecialchars(strip_tags($source->title), ENT_COMPAT, 'UTF-8'), 'url' => $source->uri);
			}

			// other accessible data

			$item->feedtitle = htmlspecialchars(strip_tags($feed_title), ENT_COMPAT, 'UTF-8');
			$item->feedurl = empty($feed_url) ? '' : $feed_url;

			$items[] = $item;

			$i++;
		}

		return $items;
	}

	protected function getFeedItemText(&$item, $params)
	{
		$letter_count = trim($params->get('l_count', ''));
		$keep_tags = trim($params->get('keep_tags', ''));
		$strip_tags = $params->get('strip_tags', 1);
		$trigger_events = $params->get('trigger_events', 0);

		$number_of_letters = -1;
		if ($letter_count != '') {
			$number_of_letters = (int)($letter_count);
		}

		// parse fields, if any in the content

		if ($trigger_events) {

			// text to match
			$regex = "#{field\s*(.*?)}(.*?){/field}#s";

			// find all instances of the regex and put in $matches
			preg_match_all($regex, $item->text, $matches, PREG_SET_ORDER);

			// process the syntax found
			if ($matches) {

				$fields = array();

				foreach ($matches as $match) {
					// $match[0] is the whole field syntax
					// $match[1] is name=[the_name]
					// $match[2] is the content ie the values of the field, comma separated

					$field = array('values' => $match[2]);

					$key = '';

					preg_match_all('#(.*?)=\[(.*?)\]#s', $match[1], $submatches, PREG_SET_ORDER);

					if ($submatches) {
						foreach ($submatches as $submatch) {
							$field[trim($submatch[1])] = $submatch[2];
							if (trim($submatch[1]) == 'alias') {
								$key = $submatch[2];
							}
						}
					}

					if ($key) {
						$fields[$key] = $field;
					} else {
						$fields[] = $field;
					}
				}

				$item->feedfields = $fields;

				// remove the fields from the content
				$item->text = preg_replace($regex, '', $item->text);
			}
		}

		$item->text = SYWText::getText($item->text, 'html', $number_of_letters, $strip_tags, $keep_tags, false, $params->get('trunc_l_w', 0));

		// Strip the images out of the text content
		//$item->text = JFilterOutput::stripImages($item->text);
		//$item->text = JHtml::_('string.truncate', $item->text, 0); // 0 shows everything (it is a word count, not a letter count)
		//$item->text = str_replace('&apos;', "'", $item->text);
	}

	protected function getFeedItemImage(&$item, $params, $module_id, $tmp_path, $clear_cache = true)
	{
	    $crop_picture = ($params->get('crop_pic', 0) && $params->get('create_thumb', 1));

		$create_highres_images = $params->get('create_highres', false);
		$lazyload = $params->get('lazyload', false);

		$thumbnail_mime_type = $params->get('thumb_mime_type', '');

		$maintain_height = $params->get('maintain_height', 0);
		$head_width = $params->get('head_w', 64);
		$head_height = $params->get('head_h', 64);
		$border_width = $params->get('border_w', 0);

		$head_width = $head_width - $border_width * 2;
		$head_height = $head_height - $border_width * 2;

		$quality_jpg = $params->get('quality_jpg', 75);
		$quality_png = $params->get('quality_png', 3);
		$quality_webp = $params->get('quality_webp', 80);
		$quality_avif = $params->get('quality_avif', 80);

		if ($quality_jpg > 100) {
			$quality_jpg = 100;
		}
		if ($quality_jpg < 0) {
			$quality_jpg = 0;
		}

		if ($quality_png > 9) {
			$quality_png = 9;
		}
		if ($quality_png < 0) {
			$quality_png = 0;
		}

		if ($quality_webp > 100) {
			$quality_webp = 100;
		}
		if ($quality_webp < 0) {
			$quality_webp = 0;
		}

		if ($quality_avif > 100) {
		    $quality_avif = 100;
		}
		if ($quality_avif < 0) {
		    $quality_avif = 0;
		}

		$image_qualities = array('jpg' => $quality_jpg, 'png' => $quality_png, 'webp' => $quality_webp, 'avif' => $quality_avif);

		$filter = $params->get('filter', 'none');

		$default_picture = trim($params->get('default_pic', ''));

		$filename = '';

		if (!$clear_cache && $params->get('create_thumb', 1)) {
		    $thumbnail_src = LNEHelper::thumbnailExists($module_id, $item->id, $tmp_path, $create_highres_images);
		    if ($thumbnail_src !== false) {
		        $filename = $thumbnail_src;
			}
		}

		if (empty($filename)) {

			$feed_item_image_source = '';

			// check if media content

			if (isset($item->media['image'])) {
				$feed_item_image_source = $item->media['image'];
			}

			// check entry links

			foreach ($item->feeditem_links as $link) {
			    if ($link->type == 'image/jpeg' || $link->type == 'image/jpg' || $link->type == 'image/png' || $link->type == 'image/gif' || $link->type == 'image/webp' || $link->type == 'image/avif') { // sometimes it is 'logo', but then, it is the same image for all, so ignore (Boing Boing feed)
					$feed_item_image_source = $link->uri;
				}
			}

			// check entry content

			if (empty($feed_item_image_source)) {
				$feed_item_image_source = LNEHelper::getImageSrcFromContent(htmlspecialchars_decode($item->text));
			}

			// check extended content (from namespace)
			if (empty($feed_item_image_source) && isset($item->content_text)) {
				$feed_item_image_source = LNEHelper::getImageSrcFromContent(htmlspecialchars_decode($item->content_text));
			}

			// use general feed image as default, if any
			if (empty($feed_item_image_source) && isset($item->defaultimage)) {
				$feed_item_image_source = $item->defaultimage;
			}

			// last resort, use default image
			if (empty($feed_item_image_source)) {
				if (!empty($default_picture)) {
					$feed_item_image_source = $default_picture;
				}
			}

			$result_array = array(null, null);
			if (!empty($feed_item_image_source)) {
			    $result_array = LNEHelper::getImageFromSrc($module_id, $item->id, $feed_item_image_source, $tmp_path, $head_width, $head_height, $crop_picture, $image_qualities, $filter, $create_highres_images, true, $thumbnail_mime_type);
			}

			if (isset($result_array['url']) && $result_array['url']) {
			    $filename = $result_array['url'];
			}

			if (isset($result_array['error']) && $result_array['error']) {
			    $item->error[] = $result_array['error'];
			}
		}

		if ($filename) {

			$img_attributes = array('width' => $head_width, 'height' => $head_height);

			$extra_attributes = trim($params->get('image_attributes', ''));
			if ($extra_attributes) {
				$xml = new \SimpleXMLElement('<element ' . $extra_attributes . ' />');
				foreach ($xml->attributes() as $attribute_name => $attribute_value) {
					$img_attributes[$attribute_name] = $attribute_value;
				}
			}

			$item->imagetag = SYWUtilities::getImageElement($filename, $item->title, $img_attributes, $lazyload, $create_highres_images, null, true, SYWVersion::getMediaVersion('mod_latestnewsenhanced_' . $module_id));
		}
	}

	/* sort by date DSC */
	protected static function sortDatesDsc($a, $b) {

		if ($a->timestamp == $b->timestamp) {
			return 0;
		}

		return ($a->timestamp > $b->timestamp) ? -1 : 1;
	}

	/* sort by date ASC */
	protected static function sortDatesAsc($a, $b) {

		if ($a->timestamp == $b->timestamp) {
			return 0;
		}

		return ($a->timestamp > $b->timestamp) ? 1 : -1;
	}

	/* sort by title DSC */
	protected static function sortTitleDsc($a, $b) {

		if (strtolower($a->title) == strtolower($b->title)) {
			return 0;
		}

		// strcmp() — Made a binary comparaison of strings
		// return < 0 if $a->title is inferior alphabeticaly to $b->title
		return -strcmp(strtolower($a->title), strtolower($b->title));
	}

	/* sort by title ASC */
	protected static function sortTitleAsc($a, $b) {

		if (strtolower($a->title) == strtolower($b->title)) {
			return 0;
		}

		// strcmp() — Made a binary comparaison of strings
		// return < 0 if $a->title is inferior alphabeticaly to $b->title
		return strcmp(strtolower($a->title), strtolower($b->title));
	}

}
?>