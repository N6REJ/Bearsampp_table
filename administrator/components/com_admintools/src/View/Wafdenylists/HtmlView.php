<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Wafdenylists;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewLoadAnyTemplateTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\WafdenylistsModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

class HtmlView extends BaseHtmlView
{
	use ViewLoadAnyTemplateTrait;
	use ViewTaskBasedEventsTrait;

	/**
	 * The search tools form
	 *
	 * @var    Form
	 * @since  7.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  7.0.0
	 */
	public $activeFilters = [];

	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  7.0.0
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  7.0.0
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  7.0.0
	 */
	protected $state;

	/**
	 * Is this view an Empty State
	 *
	 * @var   boolean
	 * @since 7.0.0
	 */
	private $isEmptyState = false;

	public function display($tpl = null)
	{
		/** @var WafdenylistsModel $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->isEmptyState  = $this->get('IsEmptyState');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		if (!\count($this->items) && $this->isEmptyState)
		{
			$this->setLayout('emptystate');
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar()
	{
		$user = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(sprintf(Text::_('COM_ADMINTOOLS_TITLE_WAFDENYLISTS')), 'icon-admintools');

		$canCreate    = $user->authorise('core.create', 'com_admintools');
		$canDelete    = $user->authorise('core.delete', 'com_admintools');
		$canEditState = $user->authorise('core.edit.state', 'com_admintools');

		if ($canCreate)
		{
			$toolbar
				->addNew('wafdenylist.add');
		}

		if ($canDelete || $canEditState || $canCreate)
		{
			/** @var DropdownButton $dropdown */
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canCreate)
			{
				$childBar->standardButton('copy', 'COM_ADMINTOOLS_COMMON_LBL_COPY_LABEL')
					->icon('fa fa-copy')
					->task('wafdenylists.copy')
					->listCheck(true);
			}

			if ($canDelete)
			{
				$childBar->delete('wafdenylists.delete')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		ToolbarHelper::back('JTOOLBAR_BACK', Route::_('index.php?option=com_admintools&view=Webapplicationfirewall', false));

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/wafblacklist.html');
	}
}