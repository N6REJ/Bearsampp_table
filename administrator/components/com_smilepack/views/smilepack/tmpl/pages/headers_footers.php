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
<div class="coming-soon-page">
    <div class="coming-soon-page--badge"><?php echo Text::_('NR_COMING_SOON'); ?></div>
    <svg width="100" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0_27_88)">
            <mask id="mask0_27_88" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="89" height="88">
                <rect x="0.5" width="88" height="88" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_27_88)">
                <path d="M7.79172 40.3845V37.2596H43.2083V40.3845H7.79172ZM7.79172 12.7404V9.61546H43.2083V12.7404H7.79172ZM11.5578 32.2916C10.5054 32.2916 9.61463 31.927 8.88547 31.1979C8.1563 30.4687 7.79172 29.5779 7.79172 28.5256V21.4744C7.79172 20.4221 8.1563 19.5313 8.88547 18.8021C9.61463 18.073 10.5054 17.7084 11.5578 17.7084H39.4422C40.4946 17.7084 41.3854 18.073 42.1145 18.8021C42.8437 19.5313 43.2083 20.4221 43.2083 21.4744V28.5256C43.2083 29.5779 42.8437 30.4687 42.1145 31.1979C41.3854 31.927 40.4946 32.2916 39.4422 32.2916H11.5578ZM11.5578 29.1667H39.4422C39.6025 29.1667 39.7494 29.0999 39.883 28.9663C40.0166 28.8328 40.0833 28.6858 40.0833 28.5256V21.4744C40.0833 21.3141 40.0166 21.1672 39.883 21.0336C39.7494 20.9001 39.6025 20.8333 39.4422 20.8333H11.5578C11.3975 20.8333 11.2506 20.9001 11.117 21.0336C10.9834 21.1672 10.9167 21.3141 10.9167 21.4744V28.5256C10.9167 28.6858 10.9834 28.8328 11.117 28.9663C11.2506 29.0999 11.3975 29.1667 11.5578 29.1667Z" fill="#1e3148"/>
            </g>
        </g>
        <defs>
            <clipPath id="clip0_27_88">
                <rect width="50" height="50" fill="white" transform="translate(0.5)"/>
            </clipPath>
        </defs>
    </svg>
    <h2 class="coming-soon-page--title"><?php echo Text::_('COM_SMILEPACK_HEADERS_FOOTERS'); ?></h2>
    <div class="coming-soon-page--description"><?php echo Text::_('COM_SMILEPACK_HEADERS_FOOTERS_PAGE_DESC'); ?></div>
    <a href="https://www.tassos.gr/newsletter" target="_blank" class="coming-soon-page--btn"><?php echo Text::_('COM_SMILEPACK_GET_NOTIFIED'); ?></a>
</div>