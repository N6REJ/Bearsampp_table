<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         9.0.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2023 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;

extract($displayData);

/**
 * @var   object $item
 */

$extension = $item->name;

$text        = JText::_('RLEM_TITLE_UPDATE');
$title       = $text . ': ' . $extension;
$hidden_text = $extension;
$icon        = 'upload';
$class       = 'btn btn-sm btn-primary';
$onclick     = 'RegularLabs.Manager.update(\'' . $item->alias . '\');';
?>

<?php echo JLayoutHelper::render('button', compact('text', 'hidden_text', 'title', 'icon', 'class', 'onclick')); ?>
