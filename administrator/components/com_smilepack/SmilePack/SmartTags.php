<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace SmilePack;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class SmartTags
{
    /**
     * Returns the SmartTags instance.
     * 
     * @return  object
     */
    public static function getInstance()
    {
        $opts = [
            'prefix' => 'sp',
            
            'isPro' => false,
            
            
        ];

        return (new \NRFramework\SmartTags($opts));
    }
    
    /**
     * Returns all available Smart Tags.
     * 
     * @return  array
     */
    public static function getSmartTags()
    {
        return [
            Text::_('COM_SMILEPACK_SITE') => [
                'site.email' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_SITE_EMAIL'),
                ],
                'site.name' => [
                    'label' => TEXT::_('COM_SMILEPACK_TAG_DESC_SITE_NAME'),
                ],
                'site.url' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_SITE_URL'),
                ],
            ],
            Text::_('NR_PAGE') => [
                'url' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_URL'),
                ],
                'url.path' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_URL_PATH'),
                ],
                'url.encoded' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_URL_ENCODED'),
                ],
                'page.title' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_PAGE_TITLE'),
                ],
                'page.desc' => [
                    'label' => TEXT::_('COM_SMILEPACK_TAG_DESC_PAGE_DESC'),
                ],
                'page.lang' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_PAGE_LANG'),
                ],
                'page.langurl' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_PAGE_LANGURL'),
                ],
                'page.generator' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_PAGE_GENERATOR'),
                ],
                'page.browsertitle' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_PAGE_BROWSERTITLE'),
                ],
                'querystring.YOUR_KEY' => [
                    'doc_id' => 'querystring',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_QUERY_STRING'),
                ],
                'post.YOUR_KEY' => [
                    'doc_id' => 'post',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_POST'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
            Text::_('NR_USER') => [
                'user.id' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_ID'),
                ],
                'user.name' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_NAME'),
                ],
                'user.firstname' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_FIRSTNAME'),
                ],
                'user.lastname' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_LASTNAME'),
                ],
                'user.login' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_NAME'),
                ],
                'user.email' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_EMAIL'),
                ],
                'user.registerdate' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_USER_REGISTRATION_DATE'),
                ],
                'user.field.FIELD_NAME' => [
                    'doc_id' => 'userfield',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CUSTOM_FIELD'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
            Text::_('COM_SMILEPACK_JOOMLA_CONTENT') => [
                'article.id' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ARTICLE_ID'),
                ],
                'article.title' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ARTICLE_TITLE'),
                ],
                'article.alias' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ARTICLE_ALIAS'),
                ],
                'article.link' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ARTICLE_LINK'),
                ],
                'article.field' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ARTICLE_CUSTOM_FIELD'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
            Text::_('COM_SMILEPACK_VISITOR_TECHNOLOGY') => [
                'client.device' => [
                    'doc_id' => 'device',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CLIENT_DEVICE'),
                ],
                'client.os' => [
                    'doc_id' => 'os',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CLIENT_OS'),
                ],
                'client.browser' => [
                    'doc_id' => 'browser',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CLIENT_BROWSER'),
                ],
                'client.useragent' => [
                    'doc_id' => 'useragent',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CLIENT_USERAGENT'),
                ],
                'client.id' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CLIENT_ID'),
                ],
            ],
            Text::_('NR_GEOLOCATION') => [
                'geo.country' => [
                    'doc_id' => 'country',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_GEO_COUNTRY'),
                    
                    'pro' => true,
                    
                    
                ],
                'geo.countrycode' => [
                    'doc_id' => 'countrycode',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_GEO_COUNTRY_CODE'),
                    
                    'pro' => true,
                    
                    
                ],
                'geo.city' => [
                    'doc_id' => 'city',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_GEO_CITY'),
                    
                    'pro' => true,
                    
                    
                ],
                'geo.region' => [
                    'doc_id' => 'region',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_GEO_REGION'),
                    
                    'pro' => true,
                    
                    
                ],
                'geo.location' => [
                    'doc_id' => 'location',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_GEO_LOCATION'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
            Text::_('COM_SMILEPACK_DATE_TIME') => [
                'date' => [
                    'doc_id' => 'date',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_DATE'),
                ],
                'day' => [
                    'doc_id' => 'day',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_DAY'),
                ],
                'month' => [
                    'doc_id' => 'month',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_MONTH'),
                ],
                'year' => [
                    'doc_id' => 'year',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_YEAR'),
                ],
                'time' => [
                    'doc_id' => 'time',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_TIME'),
                ],
            ],
            Text::_('COM_SMILEPACK_UTILITIES') => [
                'cookie.COOKIE_NAME' => [
                    'doc_id' => 'cookie',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_COOKIE'),
                    
                    'pro' => true,
                    
                    
                ],
                'language.STRING_KEY' => [
                    'doc_id' => 'language',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_LANGUAGE_STRING'),
                ],
                'ip' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_IP'),
                ],
                'referrer' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_REFERRER'),
                ],
                'randomid' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_RANDOMID'),
                ],
                'crawler' => [
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_CRAWLER'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
            Text::_('NR_INTEGRATIONS') => [
                'acymailing.subscriberscount' => [
                    'doc_id' => 'acymailing',
                    'label' => Text::_('COM_SMILEPACK_TAG_DESC_ACYMAILING_SUBSCRIBERS_COUNT'),
                    
                    'pro' => true,
                    
                    
                ],
            ],
        ];
    }
}