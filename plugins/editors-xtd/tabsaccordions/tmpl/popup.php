<?php
/**
 * @package         Tabs & Accordions
 * @version         2.1.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Editor\Editor as JEditor;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use RegularLabs\Library\Input as RL_Input;

$xmlfile = dirname(__FILE__, 2) . '/forms/popup.xml';

$form = new JForm('tabsaccordions');
$form->loadFile($xmlfile, 1, '//config');

$editor_plugin = JPluginHelper::getPlugin('editors', 'codemirror');

if (empty($editor_plugin))
{
    JFactory::getApplication()->enqueueMessage(JText::sprintf('RL_ERROR_CODEMIRROR_DISABLED', JText::_('TABSACCORDIONS'), '<a href="index.php?option=com_plugins&filter[folder]=editors&filter[search]=codemirror" target="_blank">', '</a>'), 'error');

    return '';
}

$user   = JFactory::getApplication()->getIdentity() ?: JFactory::getUser();
$editor = JEditor::getInstance('codemirror');
?>

<div class="container-fluid container-main">
    <div class="row">
        <div class="fixed-top">
            <button type="button" class="btn btn-success w-100"
                    onclick="parent.RegularLabs.TabsAccordionsButton.insertText('<?php echo RL_Input::getCmd('editor'); ?>');window.parent.Joomla.Modal.getCurrent().close();">
                <span class="icon-file-import" aria-hidden="true"></span>
                <?php echo JText::_('RL_INSERT'); ?>
            </button>
        </div>

        <div class="pt-5"></div>
    </div>

    <form action="index.php" id="tabsAccordionsForm" method="post" style="width:99%">
        <?php echo JHtml::_('uitab.startTabSet', 'main', ['active' => 'items']); ?>

        <?php
        $tabs = [
            'items'     => 'RLTA_TABS',
            'styling'   => 'RL_STYLING',
            'slideshow' => 'RLTA_SLIDESHOW',
            'settings'  => 'RL_OTHER_SETTINGS',
        ];

        foreach ($tabs as $id => $title)
        {
            echo JHtml::_('uitab.addTab', 'main', $id, JText::_($title));
            echo $form->renderFieldset($id);
            echo JHtml::_('uitab.endTab');
        }
        ?>

        <?php echo JHtml::_('uitab.endTabSet'); ?>
    </form>
</div>
