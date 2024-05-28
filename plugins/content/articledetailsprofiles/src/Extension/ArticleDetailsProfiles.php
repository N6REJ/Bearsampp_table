<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Extension;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Contact\Site\Helper\RouteHelper as ContactRouteHelper;
use Joomla\Component\Content\Site\Helper\AssociationHelper as ContentAssociationHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;
use Joomla\Registry\Registry;
use SYW\Component\TrombinoscopeExtended\Site\Helper\RouteHelper as TrombinoscopeExtendedRouteHelper;
use SYW\Library\Cache as SYWCache;
use SYW\Library\Fonts as SYWFonts;
use SYW\Library\Utilities as SYWUtilities;
use SYW\Library\Version as SYWVersion;
use SYW\Plugin\Content\ArticleDetailsProfiles\Cache\CSSFileCache;
use SYW\Plugin\Content\ArticleDetailsProfiles\Cache\CSSPrintFileCache;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\CalendarHelper;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\Helper;
use SYW\Plugin\Content\ArticleDetailsProfiles\Helper\ImageHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

final class ArticleDetailsProfiles extends CMSPlugin
{
    /**
     * Application object.
     * Needed for compatibility with Joomla 4 < 4.2
     * Ultimately, we should use $this->getApplication() in Joomla 6
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     */
    protected $app;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;
    
    /**
     * The supported form contexts
     *
     * @var    array
     */
    protected $supportedContext = [
        'com_content.article',
        'com_content.category',
        'com_content.featured',
    ];
    
    protected $_library_loaded = true;
    
    protected $_syntax_exists = false;
    
    protected $_position;
    
    protected $_modules;
    
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        if (!$this->app) {
            $this->app = Factory::getApplication();
        }
        
        if (!PluginHelper::isEnabled('system', 'syw')) {
            $this->app->enqueueMessage(Text::_('PLG_CONTENT_ARTICLEDETAILSPROFILES_WARNING_MISSINGLIBRARY'), 'error');
            $this->_library_loaded = false;
            return;
        }
        
        $config_params = ComponentHelper::getParams('com_articledetailsprofiles');
        
        $this->_position = trim($config_params->get('position', 'adprofile'));
        
        $this->_modules = array();
        $modules = ModuleHelper::getModules($this->_position);
        foreach ($modules as $module) {
            if ($module->module == 'mod_articledetailsprofile') {
                $this->_modules[] = $module;
            }
        }
    }
    
    /**
     * elimination of the extra and unnecessary module parameters
     */
    public function onContentPrepareForm($form, $data)
    {
        if (!$this->app->isClient('administrator')) {
            return;
        }
        
        if (!($form instanceof Form)) {
            return;
        }
        
        $formnames = [
            'com_modules.module' => true,
            'com_advancedmodules.module' => true
        ];
        
        if (!in_array($form->getName(), $formnames)) {
            return;
        }
        
        if (isset($data) && isset($data->module) && $data->module == 'mod_articledetailsprofile') {
            $form->removeField('module_tag', 'params');
            $form->removeField('bootstrap_size', 'params');
            $form->removeField('header_tag', 'params');
            $form->removeField('header_class', 'params');
            $form->removeField('style', 'params');
        }
    }
    
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if (!$this->_library_loaded) {
            return;
        }
            
        // add missing info in case 'force showing' is enabled and some info is missing
        if (in_array($context, $this->supportedContext)) {
            
            foreach ($this->_modules as $module) {
                
                // Get module parameters
                $module_params = new Registry();
                $module_params->loadString($module->params);
                
                if ($module_params->get('force_show', 0)) {
                    $this->_addMissingInfo($row, $params);
                }
                
                break;
            }
        }
        
        if (!isset($row->text)) {
            return;
        }
        
        $there_is_a_match = false;
        
        $regex_header = '/{articledetails-header}/i';
        $regex_footer = '/{articledetails-footer}/i';
        
        // find all instances of plugin and put in $matches for articledetails-header
        preg_match_all($regex_header, $row->text, $matches, PREG_SET_ORDER);
        
        if ($matches) {
            
            //if (isset($row->publish_up) && $view != 'category' && $view != 'featured') { // some components do not get the full fledge article (like tags or search)
            if ($context == 'com_content.article') {
                
                $there_is_a_match = true;
                
                // auto-hide elements
                $this->_autoHide($params, $this->params);
                
                // add missing info
                $this->_addMissingInfo($row, $params);
                
                $done_once = false;
                foreach ($matches as $match) {
                    if (!$done_once) {
                        $row->text = preg_replace($regex_header, $this->_createOutputBefore($context, $row, $params, $page, 'article', $this->params), $row->text, 1); // do only once, in place
                        $done_once = true;
                    } else {
                        $row->text = preg_replace($regex_header, '', $row->text, 1);
                    }
                }
            } else {
                // find all instances of articledetails-header and remove them
                preg_match_all($regex_header, $row->text, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $row->text = preg_replace($regex_header, '', $row->text, 1);
                }
            }
        }
        
        // find all instances of plugin and put in $matches for articledetails-footer
        preg_match_all($regex_footer, $row->text, $matches, PREG_SET_ORDER);
        
        if ($matches) {
            
            if ($context == 'com_content.article') { // footer is only applied to full articles
                
                $there_is_a_match = true;
                
                // auto-hide elements
                $this->_autoHide($params, $this->params);
                
                // add missing info
                $this->_addMissingInfo($row, $params);
                
                // find all instances of plugin and put in $matches for articledetails-footer
                preg_match_all($regex_footer, $row->text, $matches, PREG_SET_ORDER);
                
                $done_once = false;
                foreach ($matches as $match) {
                    $row->text = preg_replace($regex_footer, '', $row->text, 1); // remove all occurences
                    if (!$done_once) {
                        $row->text .= $this->_createOutputAfter($context, $row, $params, $page, 'article', $this->params); // do only once
                        $done_once = true;
                    }
                }
            } else {
                // find all instances of articledetails-footer and remove them
                preg_match_all($regex_footer, $row->text, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $row->text = preg_replace($regex_footer, '', $row->text, 1);
                }
            }
        }
        
        if ($there_is_a_match) {
            
            $this->_syntax_exists = true;
            
            $wam = $this->app->getDocument()->getWebAssetManager();
            
            $config_params = ComponentHelper::getParams('com_articledetailsprofiles');
            
            // add styles
            
            $load_icon_font = ($this->params->get('load_icon_font', '') == '') ? $config_params->get('load_icon_font', 1) : $this->params->get('load_icon_font', '');
            if ($load_icon_font) {
                SYWFonts::loadIconFont();
            }
            
            $load_fontawesome = ($this->params->get('load_fontawesome', '') == '') ? $config_params->get('load_fontawesome', 1) : $this->params->get('load_fontawesome', '');
            if ($load_fontawesome) {
                SYWFonts::loadIconFont('fontawesome');
            }
            
            $breakpoint = ($this->params->get('breakpoint', '') == '') ? $config_params->get('breakpoint', 640) : $this->params->get('breakpoint', '');
            $this->params->set('breakpoint', $breakpoint);
            
            $additional_inline_styles = Helper::getInlineStyles($this->params);
            $additional_inline_styles .= CalendarHelper::getCalendarInlineStyles($this->params);
            
            $clear_header_files_cache = Helper::IsClearHeaderCache($this->params);
            
            $cache_css = new CSSFileCache('plg_content_articledetailsprofiles', $this->params);
            $cache_css->addDeclaration($additional_inline_styles);
            $result = $cache_css->cache('style_article.css', $clear_header_files_cache);
            
            if ($result) {
                $wam->registerAndUseStyle('adp.article_style', $cache_css->getCachePath() . '/style_article.css');
            }
            
            $cache_css_print = new CSSPrintFileCache('plg_content_articledetailsprofiles', $this->params);
            $result = $cache_css_print->cache('print_article.css', $clear_header_files_cache);
            
            if ($result) {
                $wam->registerAndUseStyle('adp.article_print_style', $cache_css_print->getCachePath() . '/print_article.css', [], ['media' => 'print']);
            }
        }
    }
    
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        if (!$this->_library_loaded) {
            return '';
        }
        
        $html = '';

        if (!in_array($context, $this->supportedContext)) {
            return $html;
        }
        
        if ($this->_syntax_exists) {
            return $html;
        }
        
        $view = $this->app->getInput()->getCmd('view', '');
        
        if ($view == 'article' || $view == 'category' || $view == 'featured') {
            
            // 			if (count($this->_modules) > 1) {
            // 				$this->app->enqueueMessage(Text::_('PLG_CONTENT_ARTICLEDETAILS_WARNING_MULTIPLEPROFILESONPAGE'), 'error');
            // 			}
            
            $config_params = Helper::getConfig();
            
            $wam = $this->app->getDocument()->getWebAssetManager();
            
            foreach ($this->_modules as $module) {
                
                // Get module parameters
                $module_params = new Registry();
                $module_params->loadString($module->params);
                
                // heads
                
                if ($view == 'article') {
                    $head_type = $module_params->get('head_type', 'none');
                } else {
                    
                    if ($module_params->get('disable_in_list_views', false)) {
                        return $html;
                    }
                    
                    $head_type = $module_params->get('lists_head_type', 'none');
                }
                
                // auto-hide elements
                $this->_autoHide($params, $module_params);
                
                $show_image = false;
                $show_calendar = false;
                $show_icon = false;
                
                if ($head_type == 'contact' || $head_type == 'gravatar' || substr($head_type, 0, strlen('jfield:media')) === 'jfield:media'|| substr($head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {
                    $show_image = true;
                } else if ($head_type == 'calendar' || substr($head_type, 0, strlen('jfield:calendar')) === 'jfield:calendar') {
                    $show_calendar = true;
                } else if (substr($head_type, 0, strlen('jfield:sywicon')) === 'jfield:sywicon') {
                    $show_icon = true;
                }
                
                // add styles
                
                $load_icon_font = ($module_params->get('load_icon_font', '') == '') ? $config_params->get('load_icon_font', 1) : $module_params->get('load_icon_font', '');
                if ($load_icon_font) {
                    SYWFonts::loadIconFont();
                }
                
                $load_fontawesome = ($module_params->get('load_fontawesome', '') == '') ? $config_params->get('load_fontawesome', 1) : $module_params->get('load_fontawesome', '');
                if ($load_fontawesome) {
                    SYWFonts::loadIconFont('fontawesome');
                }
                
                $module_params->set('view', $view);
                
                $breakpoint = ($module_params->get('breakpoint', '') == '') ? $config_params->get('breakpoint', 640) : $module_params->get('breakpoint', '');
                $module_params->set('breakpoint', $breakpoint);
                
                $additional_inline_styles = Helper::getInlineStyles($module_params);
                if ($show_calendar) { // ok because calendars only in header, not in footer
                    $additional_inline_styles .= CalendarHelper::getCalendarInlineStyles($module_params);
                }
                
                $clear_header_files_cache = Helper::IsClearHeaderCache($module_params);
                
                $cache_css = new CSSFileCache('plg_content_articledetailsprofiles', $module_params);
                $cache_css->addDeclaration($additional_inline_styles);
                $result = $cache_css->cache('style_'.$module->id.'_'.$view.'.css', $clear_header_files_cache);
                
                if ($result) {
                    $wam->registerAndUseStyle('adp.' . $view . '_style_' . $module->id , $cache_css->getCachePath() . '/style_' . $module->id . '_' . $view . '.css');
                }
                
                $cache_css_print = new CSSPrintFileCache('plg_content_articledetailsprofiles', $module_params);
                $result = $cache_css_print->cache('print_'.$module->id.'_'.$view.'.css', $clear_header_files_cache);
                
                if ($result) {
                    $wam->registerAndUseStyle('adp.' . $view . '_print_style_' . $module->id, $cache_css_print->getCachePath() . '/print_' . $module->id . '_' . $view . '.css', [], ['media' => 'print']);
                }
                
                return $this->_createOutputBefore($context, $row, $params, $page, $view, $module_params);
            }
        }
        
        return $html;
    }

    public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
    {
        if (!$this->_library_loaded) {
            return '';
        }
        
        // for content after the article (like author)
        $html = '';
        
        $canProceed = ($context == 'com_content.article');
        if (!$canProceed) {
            return $html;
        }
        
        $view = $this->app->getInput()->getCmd('view', '');
        
        if ($view == 'article') {
            
            if ($this->_syntax_exists) {
                return $html;
            }
            
            foreach ($this->_modules as $module) {
                
                // Get module parameters
                $module_params = new Registry();
                $module_params->loadString($module->params);
                
                $row->text .= $this->_createOutputAfter($context, $row, $params, $page, $view, $module_params);
                // $html is not used in order for the footer of the article to be before the navigation or any plugin that would call onContentAfterDisplay
                
                return $html;
            }
        }
        
        return $html;
    }

    protected function _createOutputBefore($context, &$row, &$params, &$page = 0, $view = 'article', $extension_params = null)
    {
        $output = '';
        $head_output = '';
        
        $db = Factory::getDbo();
        
        $config_params = Helper::getConfig();
        
        $bootstrap_version = ($extension_params->get('bootstrap_version', '') == '') ? $config_params->get('bootstrap_version', 'joomla') : $extension_params->get('bootstrap_version', '');
        $load_bootstrap = false;
        if ($bootstrap_version === 'joomla') {
            $bootstrap_version = 5;
            $load_bootstrap = true;
        } else {
            $bootstrap_version = intval($bootstrap_version);
        }
        
        // set article link
        
        $row->link = '';
        if ($view == 'article' && !empty($row->readmore_link)) {
            $row->link = $row->readmore_link;
        } else if ($params->get('access-view')) {
            if (isset($row->language)) {
                $row->link = Route::_(ContentRouteHelper::getArticleRoute($row->slug, $row->catid, $row->language));
            } else {
                $row->link = Route::_(ContentRouteHelper::getArticleRoute($row->slug, $row->catid));
            }
        }
        
        // title
        
        $title_html_tag = $extension_params->get('t_tag', '2');
        
        // create head block
        
        if ($view == 'article') {
            $head_type = $extension_params->get('head_type', 'none');
            $head_position = $extension_params->get('head_position', 'default');
            $area = 'article_header';
        } else {
            $head_type = $extension_params->get('lists_head_type', 'none');
            $head_position = $extension_params->get('lists_head_position', 'default');
            $area = 'list';
        }
        
        if ($head_type != 'none') {
            
            $show_image = false;
            $show_calendar = false;
            $show_icon = false;
            
            if ($head_type == "contact" || $head_type == "gravatar" || substr($head_type, 0, strlen('jfield:media')) === 'jfield:media'|| substr($head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {
                $show_image = true;
            } else if ($head_type == "calendar" || substr($head_type, 0, strlen('jfield:calendar')) === 'jfield:calendar') {
                $show_calendar = true;
            } else if (substr($head_type, 0, strlen('jfield:sywicon')) === 'jfield:sywicon') {
                $show_icon = true;
            }
            
            if ($show_image) {
                
                $create_highres_images = $extension_params->get('create_highres', false);
                $allow_remote = $extension_params->get('allow_remote', true);
                $thumbnail_mime_type = $extension_params->get('thumb_mime_type', '');
                
                if ($view == 'article') {
                    $head_width = $extension_params->get('head_w', 64);
                    $head_height = $extension_params->get('head_h', 80);
                } else {
                    $head_width = $extension_params->get('lists_head_w', 64);
                    $head_height = $extension_params->get('lists_head_h', 80);
                }
                
                $border_width = $extension_params->get('border_w', 0);
                
                $head_width = $head_width - $border_width * 2;
                $head_height = $head_height - $border_width * 2;
                
                $image_qualities = $this->_getImageQuality($extension_params);
                
                $filter = $extension_params->get('filter', 'none');
                
                $clear_cache = Helper::IsClearPictureCache($extension_params);
                
                $subdirectory = 'thumbnails/adp';
                
                $thumb_path = ($extension_params->get('thumb_path', '') == '') ? $config_params->get('thumb_path_mod', 'images') : $extension_params->get('thumb_path', '');
                
                if ($thumb_path == 'cache') {
                    $subdirectory = 'plg_content_articledetailsprofiles';
                }
                $tmp_path = SYWCache::getTmpPath($thumb_path, $subdirectory);
                
                $default_picture = trim($extension_params->get('default_pic', ''));
                if ($default_picture) {
                    $default_picture = HTMLHelper::cleanImageURL($default_picture)->url;
                }
                
                $author = $row->created_by_alias ? $row->created_by_alias : $row->author;
                
                $filename = '';
                $errors = array();
                
                if (!$clear_cache) {
                    $thumbnail_src = ImageHelper::thumbnailExists($area, $row->created_by, $tmp_path, $create_highres_images);
                    if ($thumbnail_src !== false) {
                        $filename = $thumbnail_src;
                    }
                } else {
                    //ImageHelper::clearThumbnails($module->id, $tmp_path);
                    SYWVersion::refreshMediaVersion('com_articledetailsprofiles');
                }
                
                if (empty($filename)) {
                    // thumbnail(s) do not exist
                    
                    $imagesrc = '';
                    
                    if ($head_type == 'gravatar') {
                        $author_email = Helper::getAuthorEmail($row->created_by);
                        if (!empty($author_email)) {
                            $imagesrc = ImageHelper::getGravatar($author_email, $head_width);
                        }
                        
                    } else if ($head_type == 'contact') {
                        $imagesrc = Helper::getContactPicture($row->created_by);
                        if (!empty($imagesrc)) {
                            $imagesrc = HTMLHelper::cleanImageURL($imagesrc)->url;
                        }
                        
                    } else if (substr($head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {
                        $media_value = Helper::getCustomField($head_type, $row->created_by);
                        
                        if (!empty($media_value)) {
                            $image_custom_field_value = json_decode($media_value, true);
                            if ($image_custom_field_value !== null) {
                                $imagesrc = HTMLHelper::cleanImageURL($image_custom_field_value['imagefile'])->url;
                            } else {
                                $imagesrc = HTMLHelper::cleanImageURL($media_value)->url;
                            }
                        }
                        
                    } else if (substr($head_type, 0, strlen('jfield:media')) === 'jfield:media') {
                        $media_value = Helper::getCustomField($head_type, $row->id);
                        
                        if (!empty($media_value)) {
                            $image_custom_field_value = json_decode($media_value, true);
                            if ($image_custom_field_value !== null) {
                                $imagesrc = HTMLHelper::cleanImageURL($image_custom_field_value['imagefile'])->url;
                            } else {
                                $imagesrc = HTMLHelper::cleanImageURL($media_value)->url;
                            }
                        }
                    }
                    
                    // last resort, use default image if it exists
                    $used_default_image = false;
                    if (empty($imagesrc)) {
                        if ($default_picture) {
                            $imagesrc = $default_picture;
                            $used_default_image = true;
                        }
                    }
                    
                    if ($imagesrc) { // found an image, gravatar images can't be manipulated
                        
                        if ($head_type === 'gravatar') {
                            $filename = $imagesrc;
                        } else {
                            
                            $result_array = ImageHelper::getImageFromSrc($area, $row->created_by, $imagesrc, $tmp_path, $head_width, $head_height, true, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);
                            
                            if (!empty($result_array[0])) {
                                $filename = $result_array[0];
                            }
                            
                            if (!empty($result_array[1])) {
                                
                                $errors[] = $result_array[1];
                                
                                // if error for the file found, try and use the default image instead
                                if (!$used_default_image && $default_picture) { // if the default image was the one chosen, no use to retry
                                    
                                    $result_array = ImageHelper::getImageFromSrc($area, $row->created_by, $default_picture, $tmp_path, $head_width, $head_height, true, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);
                                    
                                    if (!empty($result_array[0])) {
                                        $filename = $result_array[0];
                                    }
                                    
                                    if (!empty($result_array[1])) {
                                        $errors[] = $result_array[1];
                                    }
                                }
                            }
                        }
                    }
                }
                
                $show_errors = Helper::isShowErrors($extension_params);
                
                if ($show_errors) {
                    foreach ($errors as $error) {
                        $this->app->enqueueMessage($error, 'warning');
                    }
                }
                
                $head_output .= '<div class="head imagetype">';
                if ($filename) {
                    $head_output .= '<div class="picture">';
                    $head_output .= SYWUtilities::getImageElement($filename, $author, array('width' => $head_width, 'height' => $head_height), true, $create_highres_images, null, true, $head_type !== 'gravatar' ? SYWVersion::getMediaVersion('com_articledetailsprofiles') : '');
                } else {
                    $head_output .= '<div class="nopicture">';
                    $head_output .= '<span></span>';
                }
                $head_output .= '</div>';
                $head_output .= '</div>';
                
            } else if ($show_calendar) {
                
                if (substr($head_type, 0, strlen('jfield:calendar')) === 'jfield:calendar') {
                    $calendar_date = Helper::getCustomField($head_type, $row->id);
                } else {
                    $calendar_date = $row->publish_up;
                    switch ($extension_params->get('post_d', 'published')) {
                        case 'created': $calendar_date = $row->created; break;
                        case 'modified': $calendar_date = $row->modified; break;
                        case 'finished': $calendar_date = $row->publish_down; break;
                    }
                }
                
                if ($calendar_date != $db->getNullDate() && !empty($calendar_date)) {
                    
                    $date_params = CalendarHelper::getCalendarBlockData($extension_params, $calendar_date);
                    
                    $head_output .= '<div class="head calendartype">';
                    $head_output.= '<div class="calendar noimage">';
                    foreach ($date_params as $counter => $date_array) {
                        if (!empty($date_array)) {
                            $head_output .= '<span class="position'.($counter + 1).' '.key($date_array).'">'.$date_array[key($date_array)].'</span>';
                        }
                    }
                    $head_output .= '</div>';
                    $head_output .= '</div>';
                }
                
            } else if ($show_icon) {
                
                $icon = SYWUtilities::getIconFullName(Helper::getCustomField($head_type, $row->id));
                
                // last resort, use default icon if it exists
                if (empty($icon)) {
                    if ($extension_params->get('default_icon', '') != '') {
                        $icon = SYWUtilities::getIconFullName($extension_params->get('default_icon', ''));
                    }
                }
                
                if (!empty($icon)) {
                    $head_output .= '<div class="head icontype">';
                    $head_output .= '<div class="icon">';
                    $head_output .= '<i class="' . $icon . '"></i>';
                    $head_output .= '</div>';
                    $head_output .= '</div>';
                }
            }
        }
        
        // create output
        
        $additional_class = SYWUtilities::isMobile() ? ' mobile' : '';
        $additional_class .= ' id-' . $row->id;
        if (isset($row->catid)) {
            $additional_class .= ' catid-' . $row->catid;
        }
        
        $output .= '<div class="articledetails articledetails-header' . $additional_class . '">';
        
        // head
        
        if ($head_position == 'default') {
            $output .= $head_output;
        }
        
        // create info
        
        $output .= '<div class="info">';
        
        // publication status
        
        if ($extension_params->get('show_pub_status', 1)) {
            
            $publishing_status_output = '';
            if ($row->state == 0) {
                $publishing_status_output .= '<span class="article_unpublished label label-warning">'.Text::_('JUNPUBLISHED').'</span>';
            }
            if (strtotime($row->publish_up) > strtotime(Factory::getDate())) {
                $publishing_status_output .= '<span class="article_notpublishedyet label label-warning">'.Text::_('JNOTPUBLISHEDYET').'</span>';
            }
            if ($row->publish_down !== Factory::getDbo()->getNullDate() && !is_null($row->publish_down) && strtotime($row->publish_down) < strtotime(Factory::getDate())) {
                $publishing_status_output .= '<span class="article_expired label label-warning">'.Text::_('JEXPIRED').'</span>';
            }
            
            if ($publishing_status_output) {
                $output .= '<div class="publishing_status">'.$publishing_status_output.'</div>';
            }
        }
        
        // details with/without head
        
        $info_block = Helper::getInfoBlock($extension_params, $row, $params, $view, 'before_title');
        
        if ($head_position == 'before') {
            $output .= '<div class="head_details before_title">';
            $output .= $head_output;
            if (!empty($info_block)) {
                $output .= '<dl class="item_details before_title">'.$info_block.'</dl>';
            }
            $output .= '</div>';
        } else {
            if (!empty($info_block)) {
                $output .= '<dl class="item_details before_title">'.$info_block.'</dl>';
            }
        }
        
        // title
        
        $edit_addition = '';
        if ($params->get('access-edit') && !$this->app->getInput()->getBool('print', false) /*&& !$params->get('popup')*/) {
            
            if ($load_bootstrap) {
                HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
            }
            
            if ($row->checked_out > 0 && $row->checked_out != Factory::getUser()->get('id')) {
                $checkoutUser = Factory::getUser($row->checked_out);
                $edit_addition = '<span class="article_checked_out hasTooltip" title="'.Text::sprintf('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name).'"><i class="SYWicon-lock"></i></span>';
            } else {
                $edit_url = 'index.php?option=com_content&task=article.edit&a_id=' . $row->id . '&return=' . base64_encode(Uri::getInstance());
                //$edit_addition = '&nbsp;<span class="article_edit"><i class="SYWicon-create"></i>&nbsp;<a href="'.$edit_url.'">'.Text::_('JGLOBAL_EDIT').'</a></span>';
                $edit_addition = '<a href="'.$edit_url.'" class="article_edit hasTooltip" title="'.Text::_('JGLOBAL_EDIT').'"><i class="SYWicon-create"></i></a>';
            }
        }
        
        if ($params->get('ad_show_title') && !empty($row->title)) {
            if ( $view == 'category' || $view == 'featured') {
                if ($params->get('link_titles') && $params->get('access-view') && !$this->app->getInput()->getBool('print', false)) {
                    $output .= '<h'.$title_html_tag.' class="article_title"><a href="'.$row->link.'">'.$row->title.'</a>'.$edit_addition.'</h'.$title_html_tag.'>';
                } else {
                    $output .= '<h'.$title_html_tag.' class="article_title">'.$row->title.$edit_addition.'</h'.$title_html_tag.'>';
                }
            } else {
                $output .= '<h'.$title_html_tag.' class="article_title">'.$row->title.$edit_addition.'</h'.$title_html_tag.'>';
            }
        } else {
            $output .= '<h'.$title_html_tag.' class="article_title">'.$edit_addition.'</h'.$title_html_tag.'>';
        }
        
        // details with/without head
        
        $info_block = Helper::getInfoBlock($extension_params, $row, $params, $view, 'after_title');
        
        if ($head_position == 'after') {
            $output .= '<div class="head_details after_title">';
            $output .= $head_output;
            if (!empty($info_block)) {
                $output .= '<dl class="item_details after_title">'.$info_block.'</dl>';
            }
            $output .= '</div>';
        } else {
            if (!empty($info_block)) {
                $output .= '<dl class="item_details after_title">'.$info_block.'</dl>';
            }
        }
        
        $output .= '</div>'; // end info
        
        $output .= '</div>'; // end articledetails
        
        return $output;
    }

    protected function _createOutputAfter($context, &$row, &$params, &$page = 0, $view = 'article', $extension_params = null)
    {
        $output = '';
        $head_output = '';
        
        $config_params = Helper::getConfig();
        
        // set article link (needed here before getting $something_to_show)
        
        $row->link = '';
        if (!empty($row->readmore_link)) {
            $row->link = $row->readmore_link;
        } else if ($params->get('access-view')) {
            if (isset($row->language)) {
                $row->link = Route::_(ContentRouteHelper::getArticleRoute($row->slug, $row->catid, $row->language));
            } else {
                $row->link = Route::_(ContentRouteHelper::getArticleRoute($row->slug, $row->catid));
            }
        }
        
        // create head block
        
        $footer_head_type = $extension_params->get('footer_head_type', 'none');
        
        if ($footer_head_type != 'none') {
            
            $show_image = false;
            
            if ($footer_head_type == "contact" || $footer_head_type == "gravatar" || substr($footer_head_type, 0, strlen('jfield:media')) === 'jfield:media'|| substr($footer_head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {
                $show_image = true;
            }
            
            if ($show_image) {
                
                $create_highres_images = $extension_params->get('footer_create_highres', false);
                $allow_remote = $extension_params->get('footer_allow_remote', true);
                $thumbnail_mime_type = $extension_params->get('footer_thumb_mime_type', '');
                
                $head_width = $extension_params->get('footer_head_w', 64);
                $head_height = $extension_params->get('footer_head_h', 80);
                
                $border_width = $extension_params->get('footer_border_w', 0);
                
                $head_width = $head_width - $border_width * 2;
                $head_height = $head_height - $border_width * 2;
                
                $image_qualities = $this->_getImageQuality($extension_params, 'footer');
                
                $filter = $extension_params->get('footer_filter', 'none');
                
                $clear_cache = Helper::IsClearPictureCache($extension_params);
                
                $subdirectory = 'thumbnails/adp';
                
                $thumb_path = ($extension_params->get('thumb_path', '') == '') ? $config_params->get('thumb_path_mod', 'images') : $extension_params->get('thumb_path', '');
                
                if ($thumb_path == 'cache') {
                    $subdirectory = 'plg_content_articledetailsprofiles';
                }
                $tmp_path = SYWCache::getTmpPath($thumb_path, $subdirectory);
                
                $default_picture = trim($extension_params->get('footer_default_pic', ''));
                if ($default_picture) {
                    $default_picture = HTMLHelper::cleanImageURL($default_picture)->url;
                }
                
                $author = $row->created_by_alias ? $row->created_by_alias : $row->author;
                
                $filename = '';
                $errors = array();
                
                if (!$clear_cache) {
                    $thumbnail_src = ImageHelper::thumbnailExists('article_footer', $row->created_by, $tmp_path, $create_highres_images);
                    if ($thumbnail_src !== false) {
                        $filename = $thumbnail_src;
                    }
                } else {
                    //ImageHelper::clearThumbnails($module->id, $tmp_path);
                    SYWVersion::refreshMediaVersion('com_articledetailsprofiles');
                }
                
                if (empty($filename)) {
                    // thumbnail(s) do not exist
                    
                    $imagesrc = '';
                    
                    if ($footer_head_type == 'gravatar') {
                        $author_email = Helper::getAuthorEmail($row->created_by);
                        if (!empty($author_email)) {
                            $imagesrc = ImageHelper::getGravatar($author_email, $head_width);
                        }
                        
                    } else if ($footer_head_type == 'contact') {
                        $imagesrc = Helper::getContactPicture($row->created_by);
                        if (!empty($imagesrc)) {
                            $imagesrc = HTMLHelper::cleanImageURL($imagesrc)->url;
                        }
                        
                    } else if (substr($footer_head_type, 0, strlen('jfieldusers:media')) === 'jfieldusers:media') {
                        $media_value = Helper::getCustomField($footer_head_type, $row->created_by);
                        
                        if (!empty($media_value)) {
                            $image_custom_field_value = json_decode($media_value, true);
                            if ($image_custom_field_value !== null) {
                                $imagesrc = HTMLHelper::cleanImageURL($image_custom_field_value['imagefile'])->url;
                            } else {
                                $imagesrc = HTMLHelper::cleanImageURL($media_value)->url;
                            }
                        }
                        
                    } else if (substr($footer_head_type, 0, strlen('jfield:media')) === 'jfield:media') {
                        $media_value = Helper::getCustomField($footer_head_type, $row->id);
                        
                        if (!empty($media_value)) {
                            $image_custom_field_value = json_decode($media_value, true);
                            if ($image_custom_field_value !== null) {
                                $imagesrc = HTMLHelper::cleanImageURL($image_custom_field_value['imagefile'])->url;
                            } else {
                                $imagesrc = HTMLHelper::cleanImageURL($media_value)->url;
                            }
                        }
                    }
                    
                    // last resort, use default image if it exists
                    $used_default_image = false;
                    if (empty($imagesrc)) {
                        if ($default_picture) {
                            $imagesrc = $default_picture;
                            $used_default_image = true;
                        }
                    }
                    
                    if ($imagesrc) { // found an image
                        
                        if ($footer_head_type === 'gravatar') {
                            $filename = $imagesrc;
                        } else {
                            
                            $result_array = ImageHelper::getImageFromSrc('article_footer', $row->created_by, $imagesrc, $tmp_path, $head_width, $head_height, true, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);
                            
                            if (!empty($result_array[0])) {
                                $filename = $result_array[0];
                            }
                            
                            if (!empty($result_array[1])) {
                                
                                $errors[] = $result_array[1];
                                
                                // if error for the file found, try and use the default image instead
                                if (!$used_default_image && $default_picture) { // if the default image was the one chosen, no use to retry
                                    
                                    $result_array = ImageHelper::getImageFromSrc('article_footer', $row->created_by, $default_picture, $tmp_path, $head_width, $head_height, true, $image_qualities, $filter, $create_highres_images, $allow_remote, $thumbnail_mime_type);
                                    
                                    if (!empty($result_array[0])) {
                                        $filename = $result_array[0];
                                    }
                                    
                                    if (!empty($result_array[1])) {
                                        $errors[] = $result_array[1];
                                    }
                                }
                            }
                        }
                    }
                }
                
                $show_errors = Helper::isShowErrors($extension_params);
                
                if ($show_errors) {
                    foreach ($errors as $error) {
                        $this->app->enqueueMessage($error, 'warning');
                    }
                }
                
                $head_output .= '<div class="head imagetype">';
                if ($filename) {
                    $head_output .= '<div class="picture">';
                    $head_output .= SYWUtilities::getImageElement($filename, $author, array('width' => $head_width, 'height' => $head_height), true, $create_highres_images, null, true, $footer_head_type !== 'gravatar' ? SYWVersion::getMediaVersion('com_articledetailsprofiles') : '');
                } else {
                    $head_output .= '<div class="nopicture">';
                    $head_output .= '<span></span>';
                }
                $head_output .= '</div>';
                $head_output .= '</div>';
            }
        }
        
        $info_block_footer = Helper::getInfoBlock($extension_params, $row, $params, $view, 'footer');
        
        if ($info_block_footer || $head_output) {
            
            // create output
            
            $additional_class = SYWUtilities::isMobile() ? ' mobile' : '';
            $additional_class .= ' id-' . $row->id;
            if (isset($row->catid)) {
                $additional_class .= ' catid-' . $row->catid;
            }
            
            $output .= '<div class="articledetails articledetails-footer' . $additional_class . '">';
            $output .= $head_output;
            if ($info_block_footer) {
                $output .= '<div class="info">';
                $output .= '<dl class="item_details">'.$info_block_footer.'</dl>';
                $output .= '</div>';
            }
            $output .= '</div>';
        }
        
        return $output;
    }

    /**
     * get array of quality for image types
     *
     * @param object $params
     * @param string $prefix
     * @return number[]
     */
    protected function _getImageQuality($params, $prefix = '')
    {
        if ($prefix) {
            $prefix .= '_';
        }
        
        $quality_jpg = $params->get($prefix . 'quality_jpg', 75);
        $quality_png = $params->get($prefix . 'quality_png', 3);
        $quality_webp = $params->get($prefix . 'quality_webp', 80);
        $quality_avif = $params->get($prefix . 'quality_avif', 80);
        
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
        
        return array('jpg' => $quality_jpg, 'png' => $quality_png, 'webp' => $quality_webp, 'avif' => $quality_avif);
    }

    protected function _autoHide(&$params, $extension_params)
    {
        // title
        $params->set('ad_show_title', $params->get('show_title'));
        if ($extension_params->get('autohide_title', 0)) {
            $params->set('show_title', 0);
        }
        
        // info will show if (warning: tags are included if those are set also)
        // 			$params->get('show_modify_date')
        // 			|| $params->get('show_publish_date')
        // 			|| $params->get('show_create_date')
        // 			|| $params->get('show_hits')
        // 			|| $params->get('show_category')
        // 			|| $params->get('show_parent_category')
        // 			|| $params->get('show_author')
        // 			|| (JLanguageAssociations::isEnabled() && $params->get('show_associations'))
        
        // tags
        $params->set('ad_show_tags', $params->get('show_tags'));
        if ($extension_params->get('autohide_tags', 0)) {
            $params->set('show_tags', 0);
        }
        
        // vote
        $params->set('ad_show_vote', $params->get('show_vote'));
        if ($extension_params->get('autohide_vote', 0)) {
            $params->set('show_vote', 0);
        }
    }

    protected function _addMissingInfo(&$row, &$params)
    {
        // missing contact_link
        if (!isset($row->contact_link) && $params->get('link_author')) {
            
            $row->contactid = '';
            $row->contact_link = '';
            
            $contact = Helper::getContact($row->created_by);
            
            if (!empty($contact)) {
                $row->contactid = $contact->contactid;
                if (Folder::exists(JPATH_ADMINISTRATOR . '/components/com_trombinoscopeextended') && ComponentHelper::isEnabled('com_trombinoscopeextended' && PluginHelper::isEnabled('content', 'tcpcontact'))) {
                    
                    $plugin = PluginHelper::getPlugin('content', 'tcpcontact');
                    $params_plugin = new Registry($plugin->params);
                    
                    $url_addition = '';
                    $default_view = $params_plugin->get('default_view', 0);
                    
                    if ($default_view > 0) {
                        
                        if (Multilanguage::isEnabled()) {
                            $currentLanguage = Factory::getLanguage()->getTag();
                            $langAssociations = Associations::getAssociations('com_menus', '#__menu', 'com_menus.item', $default_view, 'id', '', '');
                            foreach ($langAssociations as $langAssociation) {
                                if ($langAssociation->language == $currentLanguage) {
                                    $default_view = $langAssociation->id;
                                    break;
                                }
                            }
                        }
                        
                        $url_addition = '&Itemid=' . $default_view;
                    }
                    
                    $row->contact_link = Route::_(TrombinoscopeExtendedRouteHelper::getContactRoute('trombinoscopeextended', $contact->contactid . ':' . $contact->alias, $contact->catid) . $url_addition);
                } else if (PluginHelper::isEnabled('content', 'contact')) {
                    
                    $plugin = PluginHelper::getPlugin('content', 'contact');
                    $params_plugin = new Registry($plugin->params);
                    
                    if ($contact->contactid && $params_plugin->get('url', 'url') === 'url') {
                        $row->contact_link = Route::_(ContactRouteHelper::getContactRoute($contact->contactid . ':' . $contact->alias, $contact->catid));
                    } else if ($contact->webpage && $params_plugin->get('url', 'url') === 'webpage') {
                        $row->contact_link = $contact->webpage;
                    } else if ($contact->email && $params_plugin->get('url', 'url') === 'email') {
                        $row->contact_link = 'mailto:' . $contact->email;
                    }
                }
            }
        }
        
        if (!isset($row->slug)) {
            $row->slug  = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
            $row->catslug = $row->category_alias ? ($row->catid . ':' . $row->category_alias) : $row->catid;
            $row->parent_slug = $row->parent_alias ? ($row->parent_id . ':' . $row->parent_alias) : $row->parent_id;
            
            // No link for ROOT category
            if ($row->parent_alias == 'root') {
                $row->parent_slug = null;
            }
        }
        
        if (!isset($row->tags)) {
            $row->tags = new TagsHelper();
            $row->tags->getItemTags('com_content.article', $row->id);
        }
        
        if (!isset($row->associations) && $params->get('show_associations')) {
            $row->associations = ContentAssociationHelper::displayAssociations($row->id);
        }
    }
}
