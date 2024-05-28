<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use SmilePack\Helpers\SmartTags;
use NRFramework\Functions;

require_once JPATH_ADMINISTRATOR . '/components/com_smilepack/autoload.php';

class plgSystemSmilePack extends CMSPlugin
{
    /**
     *  Auto loads the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  Joomla Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     * Helper initialization flag
     * 
     * @var  boolean
     */
    protected $init;

    /**
     * Replace Smart Tags in the component's buffer without the need of the "Content Prepare" option. 
     * 
     * Note: This does not parse modules output. The "Custom Module" still requires the Content Prepare option to be enabled.
     *
     * @return void
     */
    public function onBeforeRender()
    {
        if (!$this->getHelper())
        {
            return;
        }

        if (!$this->canReplaceBeforeRender())
        {
            return;
        }

        /**
         * For Module SP PayPal Button.
         */
        if ($instance = $this->getModuleInstance('mod_sppaypal'))
        {
            if (method_exists($instance, 'onBeforeRender'))
            {
                $instance->onBeforeRender($this->app);
            }
        }

        $doc = $this->app->getDocument();

        $buffer = $doc->getBuffer('component');

        SmartTags::doSmartTagReplacements($buffer);

        $doc->setBuffer($buffer, 'component');
    }

    /**
     * Prevent the replacement when editing specific components
     * on the front-end of the website.
     */
    private function canReplaceBeforeRender()
    {
        $pass = true;

        switch ($this->app->input->get('option'))
        {
            case 'com_content':
                $pass = $this->app->input->get('view') !== 'form' && $this->app->input->get('layout') !== 'edit';
                break;
            case 'com_jevents':
                $pass = $this->app->input->get('task') !== 'icalevent.edit';
                break;
        }

        return $pass;
    }
    
    /**
     *  Handles the content preparation event fired by Joomla!
     *
     *  @param   mixed     $context     Unused in this plugin.
     *  @param   stdClass  $article     An object containing the article being processed.
     *  @param   mixed     $params      Unused in this plugin.
     *  @param   int       $limitstart  Unused in this plugin.
     *
     *  @return  bool
     */
    public function onContentPrepare($context, &$article)
    {
        if (!isset($article->text))
        {
            return true;
        }
        
        // Get Helper
        if (!$this->getHelper())
        {
            return true;
        }

        SmartTags::doSmartTagReplacements($article->text);
    }

    /**
     * Parse any module output without the need of the Prepare Content option.
     *
     * @param   string  $buffer    A reference to the output html of the modules being rendered
     * @param   object  $params    An array of parameters for the module renderer
     * 
     * @return  void
     */
    public function onAfterRenderModules(&$buffer, &$params)
    {
        if (!$buffer)
        {
            return;
        }
        
        // Get Helper
        if (!$this->getHelper())
        {
            return true;
        }

        SmartTags::doSmartTagReplacements($buffer);
    }

    /**
     *  Listens to AJAX requests on ?option=com_ajax&format=raw&plugin=smilepack
     *
     *  @return void
     */
    public function onAjaxSmilepack()
    {
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

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
	 * Handles the Widgets AJAX requests.
	 * 
	 * @return  void
	 */
	public function ajaxTaskWidgets()
	{
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

		$widget = $this->app->input->get('widget', null);

		$class = '\SmilePack\Widgets\\' . $widget;

		if (!class_exists($class))
		{
			return;
		}

		$action = $this->app->input->get('action');

		(new $class)->onAjax($action);
	}

    private function ajaxTaskToggleModuleStatus()
    {
		if (!Session::checkToken('request'))
		{
			echo json_encode([
				'error' => true,
				'message' => Text::_('JINVALID_TOKEN')
			]);
			die();
		}
        
		// Only in backend
        if (!$this->app->isClient('administrator'))
        {
			echo json_encode([
				'error' => true,
				'message' => 'Cannot perform this action.'
			]);
			die();
        }

        if (!$module = $this->app->input->get('mod', '', 'string'))
        {
			echo json_encode([
				'error' => true,
				'message' => Text::_('COM_SMILEPACK_NO_MODULE_NAME')
			]);
			die();
        }

        $status = $this->app->input->get('status', 0, 'int');
        if (!in_array($status, [0, 1]))
        {
			echo json_encode([
				'error' => true,
				'message' => Text::_('COM_SMILEPACK_NO_STATUS')
			]);
			die();
        }

        // Validate module
        if (!$moduleInstance = ModuleHelper::getModule($module))
        {
			echo json_encode([
				'error' => true,
				'message' => Text::_('COM_SMILEPACK_MODULE_NOT_FOUND')
			]);
			die();
        }

        // Update module status
        \SmilePack\Helpers\Modules::updateModuleStatus($module, $status);

        echo json_encode([
            'error' => false,
            'message' => Text::_('COM_SMILEPACK_MODULE_UPDATED')
        ]);
    }

    /**
     * Runs after an item has been saved.
     * 
     * @param   string  $context
     * @param   object  $table
     * @param   bool    $isNew
     * 
     * @return  void
     */
	public function onExtensionAfterSave($context, $table, $isNew)
	{
        if ($context !== 'com_modules.module')
        {
            return;
        }

        if (!isset($table->module))
        {
            return;
        }

        if (strpos($table->module, 'mod_sp') !== 0)
        {
            return;
        }

        if (!$instance = $this->getModuleInstance($table->module))
        {
            return;
        }

        // Ensure method exists
        if (!method_exists($instance, 'onExtensionAfterSave'))
        {
            return;
        }

        // Call method
        $instance->onExtensionAfterSave($context, $table, $isNew);
    }
    
    /**
     * Runs before an item is saved.
     * 
     * @param   string  $context
     * @param   object  $table
     * @param   bool    $isNew
     * 
     * @return  bool
     */
	public function onExtensionBeforeSave($context, $table, $isNew)
	{
        if ($context !== 'com_modules.module')
        {
            return;
        }
        
        if (!isset($table->module))
        {
            return true;
        }

        if (strpos($table->module, 'mod_sp') !== 0)
        {
            return true;
        }

        if ($instance = $this->getModuleInstance($table->module))
        {
            if (method_exists($instance, 'onExtensionBeforeSave'))
            {
                return $instance->onExtensionBeforeSave($context, $table, $isNew);
            }
        }

        return true;
    }

    /**
     * Runs after an item has been deleted.
     * 
     * @param   string  $context
     * @param   object  $item
     * 
     * @return  void
     */
	public function onContentAfterDelete($context, $item)
	{
        /**
         * For Module SP Gallery:
         * 
         * After a tag has been deleted, then delete it from all Smile Pack - Gallery modules.
         */
        if ($instance = $this->getModuleInstance('mod_spgallery'))
        {
            if (method_exists($instance, 'onContentAfterDelete'))
            {
                $instance->onContentAfterDelete($context, $item);
            }
        }
	}

    /**
     * Get module instance.
     * 
     * @param   string  $module  The module name.
     * 
     * @return  object
     */
    private function getModuleInstance($module = '')
    {
        if (!$module)
        {
            return;
        }

        // Get module file name
        $file_name = implode(DIRECTORY_SEPARATOR, [JPATH_SITE, 'modules', $module, 'fields', str_replace('_', '', $module) . '.php']);

        
        // Ensure file exists
        if (!file_exists($file_name))
        {
            return;
        }

        // Require file
        require_once $file_name;

        // Get class name
        $class_name = str_replace('_', '', $module) . 'Instance';

        // Ensure class exists
        if (!class_exists($class_name))
        {
            return;
        }
        
        // Get instance
        $instance = new $class_name();

        return $instance;
    }

    /**
     *  Load assets.
     *
     *  @param   Form   $form  The form to be altered.
     *  @param   mixed  $data  The associated data for the form.
     *
     *  @return  boolean
     */
	public function onContentPrepareForm(Form $form, $data)
    {
        // Run only on backend
        if (!$this->app->isClient('administrator') || !$form instanceof Form)
        {
            return;
        }

        $context = $form->getName();

        // Dynamically inject configuration settings per module instance
        if ($context === 'com_config.component' && $this->app->input->get('component') === 'com_smilepack' && $this->_name === 'smilepack')
        {
            $configuration = new \SmilePack\Configuration($form, $data);
            $configuration->injectSettings();
        }

        if (!in_array($context, ['com_modules.module']))
        {
            return;
        }

        $data = (object) $data;

        if (!isset($data->module))
        {
            return;
        }

        if (strpos($data->module, 'mod_sp') !== 0)
        {
            return;
        }

        $this->loadEditorAssets($form, $data);
    }

    /**
     * Load editor assets.
     * 
     * @param   Form   $form  The form to be altered.
     * @param   mixed  $data  The associated data for the form.
     * 
     * @return  void
     */
    private function loadEditorAssets(&$form, $data)
    {
        // Display extension notices
        \NRFramework\Notices\Notices::getInstance([
            'ext_element' => 'com_smilepack',
            'ext_xml' => 'com_smilepack',
            'exclude' => [
                'Geolocation'
            ]
        ])->show();
        
        Functions::loadLanguage('com_smilepack');

        if (!defined('nrJ4'))
        {
            HTMLHelper::stylesheet('plg_system_nrframework/joomla3.css', ['relative' => true, 'version' => 'auto']);
        }

        HTMLHelper::stylesheet('com_smilepack/editor.css', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::stylesheet('com_smilepack/editor-j3.css', ['relative' => true, 'version' => 'auto']);
        HTMLHelper::script('com_smilepack/editor.js', ['relative' => true, 'version' => 'auto']);

        if ($instance = $this->getModuleInstance($data->module))
        {
            if (method_exists($instance, 'loadEditorAssets'))
            {
                $instance->loadEditorAssets();
            }

            if (method_exists($instance, 'onContentPrepareForm'))
            {
                $instance->onContentPrepareForm($form, $data);
            }
        }
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

        // Return if document type is Feed
        if (Functions::isFeed())
        {
            return false;
        }

        return ($this->init = true);
    }
}
