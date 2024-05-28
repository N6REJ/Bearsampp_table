<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
?>
<div class="smilepack-dashboard--footer">
    <div><?php echo Text::_('COM_SMILEPACK'); ?> <?php echo NRFramework\Functions::getExtensionVersion('com_smilepack', true); ?></div>
    <div><?php echo Text::_('COM_SMILEPACK_COPYRIGHT'); ?> &copy; <?php echo date("Y"); ?> - Tassos. <?php echo Text::_('COM_SMILEPACK_ALL_RIGHTS_RESERVED'); ?>.</div>
</div>