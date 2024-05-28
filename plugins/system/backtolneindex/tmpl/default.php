<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$icon_class = 'fas fa-angle-';

$label = trim($this->params->get('backlabel', ''));

$lang = Factory::getLanguage();
$direction = $lang->isRtl() ? 'right' : 'left';
?>
<div class="back_to_view">
	<nav class="pagenavigation">
		<ul class="pagination ms-0">
			<li class="back page-item">
				<a class="page-link" href="<?php echo $referer_url; ?>" rel="back">
					<span class="visually-hidden"><?php echo $label ? $label : Text::_('PLG_SYSTEM_BACKTOLNEINDEX_BACK'); ?></span>
					<i class="<?php echo $icon_class; ?><?php echo $direction; ?>" aria-hidden="true">&nbsp;</i><span><?php echo $label ? $label : Text::_('PLG_SYSTEM_BACKTOLNEINDEX_BACK'); ?></span>
				</a>
			</li>
		</ul>
	</nav>
</div>