<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

// Initialize ACF Namespace
require_once __DIR__ . '/autoload.php';

use NRFramework\HTML;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use NRFramework\Helpers\Widgets\GalleryManager as GalleryManagerHelper;

/**
 *  Advanced Custom Fields System Plugin
 */
class PlgSystemACF extends CMSPlugin
{
    /**
     *  Auto load plugin's language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;
    
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  The loaded indicator of helper
     *
     *  @var  boolean
     */
    private $init;

    /**
     * The field preview data.
     * 
     * This is an array that holds
     * both the HTML of the field as well
     * as the assets that it requires.
     * 
     * @var  array
     */
    private $field_preview_data = [];
    
    public function onAfterInitialise()
	{
        // YooTheme Pro Integration
        \ACF\Helpers\Yoo::initFieldParser();
    }

    
    /**
     * After a tag has been deleted, then delete it from
     * all ACF - Gallery instances.
     * 
     * @param   string  $context
     * @param   object  $item
     * 
     * @return  void
     */
	public function onContentAfterDelete($context, $item)
	{
        if ($context !== 'com_tags.tag')
        {
            return;
        }

        GalleryManagerHelper::deleteTagFromFieldsValues($item->id);
	}

    /**
     *  onCustomFieldsBeforePrepareField Event
     */
    public function onCustomFieldsBeforePrepareField($context, $item, &$field)
    {
        // Validate supported component/views
        if (!in_array($context, [
            'com_content.article', 
            'com_dpcalendar.event'
        ]))
        {
            return;
        }

        // Get Helper
        if (!$this->getHelper())
        {
            return;
        }

        // Only if assignments option is enabled in the plugin settings
        if (!$this->params->get('assignments', true))
        {
            return;
        }

        if (ACFHelper::checkConditions($field) === false)
        {
            // According to the components/com_fields/layouts/fields/render.php file, if the field's value is empty it won't show up in the front-end.
            $field->value = '';

            // Unset rawvalue too, as it may be used in template overrides.
            $field->rawvalue = '';
        }
    }
    

    /**
     *  Append publishing assignments XML to the
     *
     *  @param   Form   $form  The form to be altered.
     *  @param   mixed  $data  The associated data for the form.
     *
     *  @return  boolean
     */
	public function onContentPrepareForm(Form $form, $data)
    {
        
        // Run Display Conditions checks for the User Profile page. 
        // NOTE: The System Fields plugin must be placed before this plugin in order for the $form to include the custom fields information.
        if ($this->params->get('assignments', true) && $form->getName() == 'com_users.profile' && $this->app->isClient('site') && $form instanceof Form)
        {
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            
            if ($fields = FieldsHelper::getFields('com_users.user', Factory::getUser(), true))
            {
                foreach ($fields as $field)
                {
                    if (ACFHelper::checkConditions($field) === false)
                    {
                        $form->removeField($field->name, 'com_fields');
                    }
                }
            }
        }
        

        // Run only on backend
        if (!$this->app->isClient('administrator') || !$form instanceof Form)
        {
            return;
        }

        $context = $form->getName();

        
        $this->loadPreviewer($context);
        

        if (!in_array($context, [
            'com_fields.field.com_users.user',
            'com_fields.field.com_content.article',
            'com_fields.field.com_contact.contact',
            'com_fields.field.com_dpcalendar.event'
        ]))
        {
            return;
        }

        
        // Load Publishing Rules tab if assignments option is enabled in the plugin settings
        if ($this->params->get('assignments', true))
        {
            $form->loadFile(__DIR__ . '/form/conditions.xml', false);

            if ($context == 'com_fields.field.com_users.user')
            {
                // If plg_system_fields is not positioned before plg_system_acf, display a warning.
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
                    ->select('name, ordering')
					->from($db->quoteName('#__extensions'))
					->where($db->quoteName('name') . ' = ' . $db->q('plg_system_acf'))
					->orWhere($db->quoteName('name') . ' = ' . $db->q('plg_system_fields'))
                    ->order($db->quoteName('ordering'));

                $db->setQuery($query);
    
                $plugins = $db->loadObjectList();

                if (count($plugins) == 2 && $plugins[0]->name !== 'plg_system_fields')
                {
                    $form->setField(new SimpleXMLElement('
                        <fieldset name="conditions">
                            <field name="wrongPluginOrder" type="note" 
                                class="alert alert-warning"
                                description="ACF_USER_DISPLAY_CONDITIONS_WRONG_PLUGIN_ORDER"
                            />
                        </fieldset>
                    '));
                }
            }
        }
        

        // Load "ACF Options" tab if the option is enabled in the plugin settings
        if ($this->params->get('acf_options', true))
        {
            $form->loadFile(__DIR__ . '/form/options.xml', false);
        }

        
        // Always load our stylesheet even if it's a non-ACF field. The Publishing Rules tab shows up on all fields.
        HTMLHelper::stylesheet('plg_system_acf/acf-backend.css', ['relative' => true, 'version' => 'auto']);

        if (defined('nrJ4'))
        {
			HTMLHelper::stylesheet('plg_system_acf/joomla4.css', ['relative' => true, 'version' => 'auto']);
            HTMLHelper::stylesheet('plg_system_nrframework/joomla4.css', ['relative' => true, 'version' => 'auto']);
            HTML::fixFieldTooltips();
        } else 
        {
            HTMLHelper::stylesheet('plg_system_acf/joomla3.css', ['relative' => true, 'version' => 'auto']);
        }
        

        return true;
    }

    
    /**
     * Load previewer assets.
     * 
     * @param   string  $context
     * 
     * @return  void
     */
    private function loadPreviewer($context)
    {
        $allowed_context = [
            'com_fields.field.com_content.article',
            'com_fields.field.com_content.categories',
            'com_fields.field.com_contact.contact',
            'com_fields.field.com_contact.mail',
            'com_fields.field.com_contact.categories'
        ];

        if (!in_array($context, $allowed_context))
        {
            return;
        }

        // Data passed as JS object
        $doc = Factory::getDocument();
		$options = $doc->getScriptOptions('acf_js_object');
		$options = is_array($options) ? $options : [];
		$options = [
			'root_url' => Uri::base()
		];
		$doc->addScriptOptions('acf_js_object', $options);

        // Framework helper to load assets
        HTMLHelper::script('plg_system_nrframework/helper.js', ['relative' => true, 'version' => 'auto']);

        // Load Field Previewer
        HTMLHelper::script('plg_system_acf/field_previewer.js', ['relative' => true, 'version' => 'auto']);
    }
    

    /**
     *  Listens to AJAX requests on ?option=com_ajax&format=raw&plugin=acf
     *
     *  @return void
     */
    public function onAjaxAcf()
    {
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

		// Only in backend
        if (!$this->app->isClient('administrator'))
        {
            return;
        }

        // Check if we have a valid task
		$task = $this->app->input->get('task', null);

		// Check if we have a valid method task
		$taskMethod = 'ajaxTask' . $task;

		if (!method_exists($this, $taskMethod))
		{
			die('Task not found');
		}

		$this->$taskMethod();
	}

    /**
     * Fields Previewer.
     * 
     * @return  string
     */
    private function ajaxTaskFieldsPreviewer()
    {
        $field = $this->app->input->get('field', null);
        if (!$field)
        {
			echo json_encode([
				'error' => true,
				'message' => 'Missing field name.'
			]);
            die();
        }

		if (!$data = json_decode(file_get_contents('php://input')))
        {
			echo json_encode([
				'error' => true,
				'message' => 'Missing field data to generate preview.'
			]);
            die();
        }

        // Prepare data
        $registry = new Registry();
        foreach ($data as $key => $value)
        {
            $key = str_replace(['jform[', ']', '['], ['', '', '.'], $key);
            $registry->set($key, $value);
        }
        $data = $registry->toArray();

        // We require the type of the field to save the fields data to the JSON file and be able to generate the preview
        if (!isset($data['type']))
        {
			echo json_encode([
				'error' => true,
				'message' => 'Missing field type to generate preview.'
			]);
            die();
        }
        
        // ACF Field Previewer Class
        $class = '\ACF\Previewer\\' . $field;

        // Ensure class exists
        if (!class_exists($class))
        {
			echo json_encode([
				'error' => true,
				'message' => 'Cannot preview field: ' . $field
			]);
            die();
        }

        // Get class
        $class = new $class($data);

        // Setup previewer
        $class->setup();

        echo json_encode([
            'error' => false
        ]);
    }

    /**
     * Fields Previewer HTML.
     * 
     * @return  string
     */
    private function ajaxTaskFieldsPreviewerHTML()
    {
        $field = $this->app->input->get('field', null);
        if (!$field)
        {
			echo json_encode([
				'error' => true,
				'message' => 'Missing field name.'
			]);
            die();
        }

        if (!$html = \ACF\Helpers\Previewer::getFieldPreviewData($field))
        {
            return;
        }

        echo $html;
    }

    /**
     *  Loads the helper classes of plugin
     *
     *  @return  bool
     */
    private function getHelper()
    {
        // Return if is helper is already loaded
        if ($this->init)
        {
            return true;
        }

        // Return if we are not in frontend
        if (!$this->app->isClient('site'))
        {
            return false;
        }

        // Load Novarain Framework
        if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
        {
            return;
        }

        // Load Plugin Helper
        JLoader::register('ACFHelper', __DIR__ . '/helper/helper.php');

        return ($this->init = true);
    }

    /**
     * Let each condition check the value before it's savced into the database
     *
     * @param   string  $context
     * @param   object  $article
     * @param   bool    $isNew
     * 
     * @return  void
     */
    public function onContentBeforeSave($context, $article, $isNew)
    {
        if (!in_array($context, ['com_fields.field']))
        {
            return;
        }
        
        if (!isset($article->params))
        {
            return;
        }

        $params = json_decode($article->params, true);
        if (!isset($params['rules']))
        {
            return;
        }       
        
        NRFramework\Conditions\ConditionsHelper::getInstance()->onBeforeSave($params['rules']);

        $article->params = json_encode($params);
    }
}
