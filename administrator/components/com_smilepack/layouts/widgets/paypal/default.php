<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>
<div
	class="sp-paypal-button<?php echo $css_class; ?>"
	data-config="<?php echo htmlspecialchars(json_encode($displayData)); ?>"
	>
	<div class="sp-paypal-button--message"></div>
	<div class="sp-paypal-button-inner"></div>
</div>