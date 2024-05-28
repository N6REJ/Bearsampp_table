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
        <g clip-path="url(#clip0_27_36)">
            <mask id="mask0_27_36" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="89" height="88">
                <rect x="0.5" width="88" height="88" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_27_36)">
                <path d="M11.5578 39.5833H39.4422C39.6292 39.5833 39.7828 39.5232 39.903 39.403C40.0232 39.2828 40.0833 39.1292 40.0833 38.9422V20.2484L30.2516 10.4167H11.5578C11.3708 10.4167 11.2172 10.4768 11.097 10.597C10.9768 10.7172 10.9167 10.8708 10.9167 11.0578V38.9422C10.9167 39.1292 10.9768 39.2828 11.097 39.403C11.2172 39.5232 11.3708 39.5833 11.5578 39.5833ZM11.5578 42.7083C10.5188 42.7083 9.63135 42.3404 8.89552 41.6045C8.15965 40.8687 7.79172 39.9812 7.79172 38.9422V11.0578C7.79172 10.0188 8.15965 9.13135 8.89552 8.39552C9.63135 7.65965 10.5188 7.29172 11.5578 7.29172H31.5296L43.2083 18.9704V38.9422C43.2083 39.9812 42.8404 40.8687 42.1045 41.6045C41.3687 42.3404 40.4812 42.7083 39.4422 42.7083H11.5578ZM15.6042 34.375H35.3958V31.25H15.6042V34.375ZM15.6042 26.5625H35.3958V23.4376H15.6042V26.5625ZM15.6042 18.75H28.8653V15.6251H15.6042V18.75Z" fill="#1e3148"/>
            </g>
        </g>
        <defs>
            <clipPath id="clip0_27_36">
                <rect width="50" height="50" fill="white" transform="translate(0.5)"/>
            </clipPath>
        </defs>
    </svg>
    <h2 class="coming-soon-page--title"><?php echo Text::_('COM_SMILEPACK_REUSABLE_SNIPPETS'); ?></h2>
    <div class="coming-soon-page--description"><?php echo Text::_('COM_SMILEPACK_REUSABLE_SNIPPETS_PAGE_DESC'); ?></div>
    <a href="https://www.tassos.gr/newsletter" target="_blank" class="coming-soon-page--btn"><?php echo Text::_('COM_SMILEPACK_GET_NOTIFIED'); ?></a>
</div>