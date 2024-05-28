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
        <g clip-path="url(#clip0_27_26)">
            <mask id="mask0_27_26" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="89" height="88">
                <rect x="0.5" width="88" height="88" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_27_26)">
                <path d="M7.79172 38.5416L16.4856 25L7.79172 11.4584H31.3493C32.0705 11.4584 32.7482 11.6213 33.3826 11.9472C34.0169 12.2731 34.5384 12.7271 34.9471 13.3094L43.2083 25L34.867 36.7307C34.4717 37.2996 33.9635 37.7437 33.3425 38.0628C32.7215 38.382 32.0571 38.5416 31.3493 38.5416H7.79172ZM13.4727 35.4167H31.3493C31.5497 35.4167 31.7367 35.3699 31.9103 35.2764C32.0839 35.1829 32.2308 35.0561 32.351 34.8958L39.3863 25L32.351 15.1042C32.2308 14.9439 32.0906 14.8171 31.9303 14.7236C31.77 14.6301 31.5764 14.5833 31.3493 14.5833H13.4727L20.2435 25L13.4727 35.4167Z" fill="#1e3148"/>
            </g>
        </g>
        <defs>
            <clipPath id="clip0_27_26">
             <rect width="50" height="50" fill="white" transform="translate(0.5)"/>
            </clipPath>
        </defs>
    </svg>
    <h2 class="coming-soon-page--title"><?php echo Text::_('COM_SMILEPACK_WIDGETS'); ?></h2>
    <div class="coming-soon-page--description"><?php echo Text::_('COM_SMILEPACK_WIDGETS_PAGE_DESC'); ?></div>
    <a href="https://www.tassos.gr/newsletter" target="_blank" class="coming-soon-page--btn"><?php echo Text::_('COM_SMILEPACK_GET_NOTIFIED'); ?></a>
</div>