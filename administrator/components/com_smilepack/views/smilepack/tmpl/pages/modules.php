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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

HTMLHelper::stylesheet('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
HTMLHelper::script('com_smilepack/dashboard.js', ['version' => 'auto', 'relative' => true]);

$modules = $this->get('modules');


\NRFramework\HTML::renderProOnlyModal('com_smilepack');


require_once JPATH_PLUGINS . '/system/nrframework/fields/nrtoggle.php';
$field = new \JFormFieldNRToggle();

?>
<div
    class="smilepack-grid"
    data-token="<?php echo Session::getFormToken(); ?>"
    data-root-url="<?php echo Uri::base(); ?>">
    <?php
    foreach ($modules as $key => $module)
    {
        ?>
        <div class="smilepack-grid--item" data-module="mod_sp<?php echo $key; ?>">
            <div class="smilepack-grid--item--top">
                <div class="smilepack-grid--item--top--name">
                    <div class="smilepack-grid--item--top--name--icon">
                        <?php echo $module['icon']; ?>
                    </div>
                    <?php echo $module['name']; ?>
                </div>
                <div class="smilepack-grid--item--top--toggle">
                    <?php
                    if (in_array($module['status'], ['published', 'unpublished']))
                    {
                        $element = new \SimpleXMLElement('<field name="smilePackModuleStatusToggle_' . $key . '" type="NRToggle" class="small smilePackModuleStatusToggle" checked="' . ($module['status'] === 'published') . '" />');
                        $field->setup($element, null);
                        echo $field->__get('input');
                    }
                    ?>
                </div>
            </div>
            <div class="smilepack-grid--item--description">
                <?php echo $module['description']; ?>
            </div>
            <div class="smilepack-grid--item--actions">
                <span>
                    <?php
                    if (in_array($module['status'], ['published', 'unpublished']) && $module['total_modules'] > 0)
                    {
                        echo '<a href="' . Uri::base() . 'index.php?option=com_modules&filter[module]=mod_sp' . $key . '" target="_blank">' . $module['total_modules'] . ' module' . ($module['total_modules'] > 1 ? 's' : '') . '</a>';
                    }
                    ?>
                </span>
                <span>
                    <?php
                    if ($module['status'] === 'coming_soon')
                    {
                        echo Text::_('NR_COMING_SOON');
                    }
                    else if ($module['status'] === 'not_installed')
                    {
                        ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_201_280" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                        <rect width="16" height="16" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_201_280)">
                        <path d="M7.99998 14.6666C7.07776 14.6666 6.21109 14.4916 5.39998 14.1416C4.58887 13.7916 3.88331 13.3166 3.28331 12.7166C2.68331 12.1166 2.20831 11.4111 1.85831 10.6C1.50831 9.78887 1.33331 8.9222 1.33331 7.99998C1.33331 7.07776 1.50831 6.21109 1.85831 5.39998C2.20831 4.58887 2.68331 3.88331 3.28331 3.28331C3.88331 2.68331 4.58887 2.20831 5.39998 1.85831C6.21109 1.50831 7.07776 1.33331 7.99998 1.33331C8.9222 1.33331 9.78887 1.50831 10.6 1.85831C11.4111 2.20831 12.1166 2.68331 12.7166 3.28331C13.3166 3.88331 13.7916 4.58887 14.1416 5.39998C14.4916 6.21109 14.6666 7.07776 14.6666 7.99998C14.6666 8.9222 14.4916 9.78887 14.1416 10.6C13.7916 11.4111 13.3166 12.1166 12.7166 12.7166C12.1166 13.3166 11.4111 13.7916 10.6 14.1416C9.78887 14.4916 8.9222 14.6666 7.99998 14.6666ZM7.99998 13.3333C8.59998 13.3333 9.17776 13.2361 9.73331 13.0416C10.2889 12.8472 10.8 12.5666 11.2666 12.2L3.79998 4.73331C3.43331 5.19998 3.15276 5.71109 2.95831 6.26665C2.76387 6.8222 2.66665 7.39998 2.66665 7.99998C2.66665 9.48887 3.18331 10.75 4.21665 11.7833C5.24998 12.8166 6.51109 13.3333 7.99998 13.3333ZM12.2 11.2666C12.5666 10.8 12.8472 10.2889 13.0416 9.73331C13.2361 9.17776 13.3333 8.59998 13.3333 7.99998C13.3333 6.51109 12.8166 5.24998 11.7833 4.21665C10.75 3.18331 9.48887 2.66665 7.99998 2.66665C7.39998 2.66665 6.8222 2.76387 6.26665 2.95831C5.71109 3.15276 5.19998 3.43331 4.73331 3.79998L12.2 11.2666Z" fill="#687686"/>
                        </g>
                        </svg>
                        <span><?php echo Text::_('COM_SMILEPACK_NOT_INSTALLED'); ?></span>
                        <?php
                    }
                    else if (in_array($module['status'], ['published', 'unpublished']))
                    {
                        ?>
                        <a href="<?php echo $module['help_url']; ?>" target="_blank">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_181_106" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_181_106)"><path d="M7.9668 12.0001C8.20013 12.0001 8.39735 11.9196 8.55847 11.7585C8.71958 11.5974 8.80013 11.4001 8.80013 11.1668C8.80013 10.9335 8.71958 10.7362 8.55847 10.5751C8.39735 10.414 8.20013 10.3335 7.9668 10.3335C7.73347 10.3335 7.53624 10.414 7.37513 10.5751C7.21402 10.7362 7.13347 10.9335 7.13347 11.1668C7.13347 11.4001 7.21402 11.5974 7.37513 11.7585C7.53624 11.9196 7.73347 12.0001 7.9668 12.0001ZM7.3668 9.43347H8.60013C8.60013 9.0668 8.6418 8.77791 8.72513 8.5668C8.80847 8.35569 9.04458 8.0668 9.43347 7.70013C9.72235 7.41124 9.95013 7.13624 10.1168 6.87513C10.2835 6.61402 10.3668 6.30013 10.3668 5.93347C10.3668 5.31124 10.139 4.83347 9.68347 4.50013C9.22791 4.1668 8.68902 4.00013 8.0668 4.00013C7.43347 4.00013 6.91958 4.1668 6.52513 4.50013C6.13069 4.83347 5.85569 5.23347 5.70013 5.70013L6.80013 6.13347C6.85569 5.93347 6.98069 5.7168 7.17513 5.48347C7.36958 5.25013 7.6668 5.13347 8.0668 5.13347C8.42235 5.13347 8.68902 5.23069 8.8668 5.42513C9.04458 5.61958 9.13346 5.83347 9.13346 6.0668C9.13346 6.28902 9.0668 6.49735 8.93347 6.6918C8.80013 6.88624 8.63347 7.0668 8.43347 7.23347C7.94458 7.6668 7.64458 7.99458 7.53347 8.2168C7.42235 8.43902 7.3668 8.84458 7.3668 9.43347ZM8.00013 14.6668C7.07791 14.6668 6.21124 14.4918 5.40013 14.1418C4.58902 13.7918 3.88347 13.3168 3.28347 12.7168C2.68347 12.1168 2.20847 11.4112 1.85847 10.6001C1.50847 9.78902 1.33347 8.92235 1.33347 8.00013C1.33347 7.07791 1.50847 6.21124 1.85847 5.40013C2.20847 4.58902 2.68347 3.88347 3.28347 3.28347C3.88347 2.68347 4.58902 2.20847 5.40013 1.85847C6.21124 1.50847 7.07791 1.33347 8.00013 1.33347C8.92235 1.33347 9.78902 1.50847 10.6001 1.85847C11.4112 2.20847 12.1168 2.68347 12.7168 3.28347C13.3168 3.88347 13.7918 4.58902 14.1418 5.40013C14.4918 6.21124 14.6668 7.07791 14.6668 8.00013C14.6668 8.92235 14.4918 9.78902 14.1418 10.6001C13.7918 11.4112 13.3168 12.1168 12.7168 12.7168C12.1168 13.3168 11.4112 13.7918 10.6001 14.1418C9.78902 14.4918 8.92235 14.6668 8.00013 14.6668ZM8.00013 13.3335C9.48902 13.3335 10.7501 12.8168 11.7835 11.7835C12.8168 10.7501 13.3335 9.48902 13.3335 8.00013C13.3335 6.51124 12.8168 5.25013 11.7835 4.2168C10.7501 3.18347 9.48902 2.6668 8.00013 2.6668C6.51124 2.6668 5.25013 3.18347 4.2168 4.2168C3.18347 5.25013 2.6668 6.51124 2.6668 8.00013C2.6668 9.48902 3.18347 10.7501 4.2168 11.7835C5.25013 12.8168 6.51124 13.3335 8.00013 13.3335Z" fill="currentColor"/></g></svg>
                            <span><?php echo Text::_('COM_SMILEPACK_HELP'); ?></span>
                        </a>
                        <?php
                    }
                    else if ($module['status'] === 'pro')
                    {
                        ?>
                        <a href="#" data-pro-only="<?php echo $module['name']; ?>">
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_201_124" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="17" height="18"><rect y="0.5" width="17" height="17" fill="#D9D9D9"/></mask><g mask="url(#mask0_201_124)"><path d="M4.25006 6.16655H10.6251V4.74989C10.6251 4.15961 10.4185 3.65787 10.0053 3.24468C9.59207 2.83149 9.09033 2.62489 8.50006 2.62489C7.90978 2.62489 7.40804 2.83149 6.99485 3.24468C6.58165 3.65787 6.37506 4.15961 6.37506 4.74989H4.95839C4.95839 3.77003 5.3037 2.93478 5.99433 2.24416C6.68495 1.55353 7.5202 1.20822 8.50006 1.20822C9.47992 1.20822 10.3152 1.55353 11.0058 2.24416C11.6964 2.93478 12.0417 3.77003 12.0417 4.74989V6.16655H12.7501C13.1396 6.16655 13.4731 6.30527 13.7506 6.5827C14.028 6.86013 14.1667 7.19364 14.1667 7.58322V14.6666C14.1667 15.0561 14.028 15.3896 13.7506 15.6671C13.4731 15.9445 13.1396 16.0832 12.7501 16.0832H4.25006C3.86047 16.0832 3.52697 15.9445 3.24954 15.6671C2.97211 15.3896 2.83339 15.0561 2.83339 14.6666V7.58322C2.83339 7.19364 2.97211 6.86013 3.24954 6.5827C3.52697 6.30527 3.86047 6.16655 4.25006 6.16655ZM4.25006 14.6666H12.7501V7.58322H4.25006V14.6666ZM8.50006 12.5416C8.88964 12.5416 9.22315 12.4028 9.50058 12.1254C9.77801 11.848 9.91672 11.5145 9.91672 11.1249C9.91672 10.7353 9.77801 10.4018 9.50058 10.1244C9.22315 9.84694 8.88964 9.70822 8.50006 9.70822C8.11047 9.70822 7.77697 9.84694 7.49954 10.1244C7.22211 10.4018 7.08339 10.7353 7.08339 11.1249C7.08339 11.5145 7.22211 11.848 7.49954 12.1254C7.77697 12.4028 8.11047 12.5416 8.50006 12.5416Z" fill="currentColor"/></g></svg>
                            <span><?php echo Text::_('COM_SMILEPACK_GO_PRO_TO_UNLOCK'); ?></span>
                        </a>
                        <?php
                    }
                    ?>
                </span>
            </div>
        </div>
        <?php
    }
?>
</div>