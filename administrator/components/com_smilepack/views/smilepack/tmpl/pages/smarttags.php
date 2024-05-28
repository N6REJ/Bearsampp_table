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
<p><?php echo Text::_('COM_SMILEPACK_SMARTTAGS_INTRO1'); ?></p>
<p><?php echo Text::_('COM_SMILEPACK_SMARTTAGS_INTRO2'); ?></p>


<?php
\NRFramework\HTML::renderProOnlyModal();
?>
<div class="smilepack-feature-pro-section">
    <h2 class="smilepack-feature-pro-section--title">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="m216.769-535.152 88 37.231q16.308-32.616 34.77-63.039 18.462-30.424 40.693-59.885l-58.693-11.385q-3.077-.769-5.962.192-2.884.962-5.192 3.27l-93.616 93.616Zm135.845 74.922 113.616 113q44.692-18.692 92.692-51.692 48-33 91.154-76.154 67.308-67.308 104.115-146.653 36.808-79.346 36.423-163.27-83.924-.384-163.385 36.423-79.461 36.808-146.768 104.115-43.154 43.154-76.154 91.347-33 48.192-51.693 92.884Zm192.617-80.001q-20.308-20.307-20.308-49.384 0-29.076 20.308-49.384 20.307-20.307 49.884-20.307 29.576 0 49.884 20.307 20.307 20.308 20.307 49.384 0 29.077-20.307 49.384-20.308 20.308-49.884 20.308-29.577 0-49.884-20.308Zm-4.464 329.462 93.617-93.616q2.307-2.308 3.269-5.192.962-2.885.192-5.962l-11.384-58.693q-29.462 22.231-59.885 40.385-30.424 18.154-63.04 34.462l37.231 88.616Zm304.538-629.152q12.846 115.615-27.346 219.153T685.691-425.154l-3.462 3.461-3.462 3.462 18.077 90.538q3.615 18.077-1.615 35.154-5.231 17.076-18.077 29.923l-156.846 156.46-75.153-176.614-156.768-156.768-176.614-75.538 155.845-156.46q12.847-12.846 30.231-18.269 17.384-5.424 35.461-1.808l91.307 18.461q1.924-1.923 3.27-3.462 1.346-1.538 3.269-3.461 92.076-92.076 195.306-132.384 103.23-40.307 218.845-27.462ZM180.848-317.154q29.23-29.23 71.268-29.345 42.038-.116 71.268 29.115 29.231 29.23 28.923 71.268-.308 42.038-29.538 71.268-22.692 22.692-74.461 38.961t-132.844 26.038q9.769-81.075 26.23-132.844 16.462-51.768 39.154-74.461Zm42.768 42.538q-10.769 10.769-20.769 37.077-10 26.308-13.616 53.692 27.385-3.615 53.693-13.5 26.307-9.884 37.077-20.654 12-12 12.615-28.807.615-16.808-11.385-28.808t-28.807-11.5q-16.808.5-28.808 12.5Z"/></svg>
        <?php echo Text::_('COM_SMILEPACK_UNLOCK_ALL_FEATURES'); ?>
    </h2>
    <div class="smilepack-feature-pro-section--description">
        <p><?php echo sprintf(Text::_('COM_SMILEPACK_PRO_FEATURE_DESC'), Text::_('COM_SMILEPACK_SMART_TAGS')); ?></p>
    </div>
    <a href="#" data-pro-only="<?php echo Text::_('COM_SMILEPACK_ADVANCED_SMARTTAGS'); ?>">
        <svg width="20" height="20" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_143_146" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="17" height="17"><rect width="17" height="17" fill="currentColor"></rect></mask><g mask="url(#mask0_143_146)"><path d="M4.25006 5.66655H10.6251V4.24989C10.6251 3.65961 10.4185 3.15787 10.0053 2.74468C9.59207 2.33149 9.09033 2.12489 8.50005 2.12489C7.90978 2.12489 7.40804 2.33149 6.99485 2.74468C6.58165 3.15787 6.37506 3.65961 6.37506 4.24989H4.95839C4.95839 3.27003 5.3037 2.43478 5.99433 1.74416C6.68495 1.05353 7.52019 0.708221 8.50005 0.708221C9.47992 0.708221 10.3152 1.05353 11.0058 1.74416C11.6964 2.43478 12.0417 3.27003 12.0417 4.24989V5.66655H12.7501C13.1396 5.66655 13.4731 5.80527 13.7506 6.0827C14.028 6.36013 14.1667 6.69364 14.1667 7.08322V14.1666C14.1667 14.5561 14.028 14.8896 13.7506 15.1671C13.4731 15.4445 13.1396 15.5832 12.7501 15.5832H4.25006C3.86047 15.5832 3.52697 15.4445 3.24954 15.1671C2.9721 14.8896 2.83339 14.5561 2.83339 14.1666V7.08322C2.83339 6.69364 2.9721 6.36013 3.24954 6.0827C3.52697 5.80527 3.86047 5.66655 4.25006 5.66655ZM4.25006 14.1666H12.7501V7.08322H4.25006V14.1666ZM8.50005 12.0416C8.88964 12.0416 9.22315 11.9028 9.50058 11.6254C9.77801 11.348 9.91672 11.0145 9.91672 10.6249C9.91672 10.2353 9.77801 9.9018 9.50058 9.62437C9.22315 9.34694 8.88964 9.20822 8.50005 9.20822C8.11047 9.20822 7.77696 9.34694 7.49953 9.62437C7.2221 9.9018 7.08339 10.2353 7.08339 10.6249C7.08339 11.0145 7.2221 11.348 7.49953 11.6254C7.77696 11.9028 8.11047 12.0416 8.50005 12.0416Z" fill="white"></path></g></svg>
        <?php echo Text::_('NR_UPGRADE_TO_PRO'); ?>
    </a>
</div>


<div class="smilepack-table-wrapper">
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
                    <td colspan="3" class="smilepack-table-category"><?php echo $category_label; ?></td>
                </tr>
                <?php
                foreach ($list as $key => $item)
                {
                    $doc_id = isset($item['doc_id']) ? $item['doc_id'] : str_replace('.', '', $key);
                    $isPro = isset($item['pro']) && $item['pro'];
                    $smartTag = '&lcub;sp ' . $key . '&rcub;';
                    ?>
                    <tr>
                        <td>
                            <?php if ($isPro): ?>
                                <div class="smilepack-pro-tag" data-pro-only="<?php echo sprintf(Text::_('COM_SMILEPACK_SMART_TAG_X'), $smartTag); ?>">
                                    <span class="smilepack-pro-tag--badge"><?php echo Text::_('NR_PRO'); ?></span>
                                    <span class="smilepack-pro-tag--content"><?php echo $smartTag; ?></span>
                                </div>
                            <?php else: ?>
                                <span class="smilepack-copy-to-clipboard-trigger">
                                    <span class="smilepack-copy-to-clipboard-text"><?php echo $smartTag; ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M362.308-260.001q-30.308 0-51.307-21-21-21-21-51.308v-455.382q0-30.308 21-51.308 20.999-21 51.307-21h335.383q30.307 0 51.307 21 21 21 21 51.308v455.382q0 30.308-21 51.308t-51.307 21H362.308Zm0-59.999h335.383q4.615 0 8.462-3.846 3.846-3.847 3.846-8.463v-455.382q0-4.616-3.846-8.463-3.847-3.846-8.462-3.846H362.308q-4.616 0-8.462 3.846-3.847 3.847-3.847 8.463v455.382q0 4.616 3.847 8.463 3.846 3.846 8.462 3.846ZM222.309-120.003q-30.307 0-51.307-21-21-21-21-51.307v-515.381h59.999v515.381q0 4.616 3.846 8.462 3.847 3.847 8.462 3.847h395.382v59.998H222.309ZM349.999-320V-800-320Z" fill="currentColor" /></svg>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['label']; ?></td>
                        <td><a href="https://www.tassos.gr/kb/general/smart-tags#<?php echo $doc_id; ?>" target="_blank"><?php echo Text::_('COM_SMILEPACK_READ_MORE'); ?></a></td>
                    </tr>
                    <?php
                
                }
            }
            ?>
        </tbody>
    </table>
</div>