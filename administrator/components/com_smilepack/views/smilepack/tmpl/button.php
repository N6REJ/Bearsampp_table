<?php
/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

HTMLHelper::script('com_smilepack/editorbutton.js', ['relative' => true, 'version' => 'auto']);
HTMLHelper::stylesheet('com_smilepack/editorbutton.css', ['relative' => true, 'version' => 'auto']);


\NRFramework\HTML::renderProOnlyModal();

?>
<div class="smilepack-smart-tags-table-wrapper">
    <table>
        <thead>
            <tr>
                <th><?php echo Text::_('COM_SMILEPACK_SMART_TAG'); ?></th>
                <th><?php echo Text::_('NR_DESCRIPTION'); ?></th>
                <th><?php echo Text::_('NR_DOCUMENTATION'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($this->get('smartTags') as $category_label => $list)
            {
                ?>
                <tr>
                    <td colspan="3" class="smilepack-smart-tags-table-category"><?php echo $category_label; ?></td>
                </tr>
                <?php
                foreach ($list as $key => $item)
                {
					$smartTag = '&lcub;sp ' . $key . '&rcub;';
                    $doc_id = isset($item['doc_id']) ? $item['doc_id'] : str_replace('.', '', $key);
                    $isPro = isset($item['pro']) && $item['pro'];
                    
                    ?>
                    <tr>
                        <td>
                            <?php if ($isPro): ?>
                                <div class="smilepack-pro-tag" data-pro-only="<?php echo sprintf(Text::_('COM_SMILEPACK_SMART_TAG_X'), $smartTag); ?>">
                                    <span class="smilepack-pro-tag--badge"><?php echo Text::_('NR_PRO'); ?></span>
                                    <span class="smilepack-pro-tag--content"><?php echo $smartTag; ?></span>
                                </div>
                            <?php else: ?>
                                <a href="#" onclick="insertSmilePackSmartTag('<?php echo $this->eName; ?>', <?php echo defined('nrJ4') ? 'true' : 'false' ?>, '<?php echo $smartTag; ?>');"><?php echo $smartTag; ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['label']; ?></td>
                        <td>
                            <a href="https://www.tassos.gr/kb/general/smart-tags#<?php echo $doc_id; ?>" target="_blank"><?php echo Text::_('COM_SMILEPACK_READ_MORE'); ?></a>
                        </td>
                    </tr>
                    <?php
                
                }
            }
            ?>
        </tbody>
    </table>
</div>