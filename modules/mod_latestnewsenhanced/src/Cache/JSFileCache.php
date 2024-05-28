<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Module\LatestNewsEnhanced\Site\Cache;

defined('_JEXEC') or die;

use SYW\Library\HeaderFilesCache;

class JSFileCache extends HeaderFilesCache
{
	public function __construct($extension, $params = null)
	{
		parent::__construct($extension, $params);

		$this->extension = $extension;

		$variables = array();

		$suffix = '#lnee_'.$params->get('suffix');
		$variables[] = 'suffix';

// 		$link_to = $params->get('link_to', 'item');
// 		$variables[] = 'link_to';

		$width_unit = $params->get('item_w_u', 'percent');
		$variables[] = 'width_unit';

		$item_width = trim($params->get('item_w', 100)); // %
		if ($item_width <= 0 || $item_width > 100) {
			$item_width = 100;
		}
		$variables[] = 'item_width';

		$min_width = trim($params->get('min_item_w', '')); // px
		$variables[] = 'min_width';

		$margin_min_width = 3; // px
		$variables[] = 'margin_min_width';

		$margin_error = 1; // px
		$variables[] = 'margin_error';

		// set all necessary parameters
		$this->params = compact($variables);
	}

	public function getBuffer($inline = false)
	{
		// get all necessary parameters
		extract($this->params);

// 		if (function_exists('ob_gzhandler')) { // not tested
// 			ob_start('ob_gzhandler');
// 		} else {
 			ob_start();
// 		}

		// set the header
 		if (!$inline) {
 			//$this->sendHttpHeaders('js');
 		}

 		echo 'document.addEventListener("readystatechange", function(event) { ';
 			echo 'if (event.target.readyState === "complete") { ';

	 			echo 'var toggle = function(index) { ';
	 				echo 'document.querySelectorAll("' . $suffix . ' .inline-item").forEach(function (el, index2) { ';
	 					echo 'if (index != index2) { ';
				 			echo 'el.classList.add("hide"); ';
				 			echo 'el.classList.remove("show"); ';
	 					echo '}';
	 				echo '}, this); ';

	 				echo 'var inlineitem = document.querySelector("' . $suffix . ' #inline-item-" + index); ';
	 				echo 'if (typeof(inlineitem) != "undefined" && inlineitem != null) { '; // when animations, the index may be off because of duplicated items
	 					echo 'inlineitem.classList.add("show"); ';
	 					echo 'inlineitem.classList.remove("hide"); ';
	 				echo '}; ';
	 			echo '}; ';

	 			echo 'document.querySelectorAll("' . $suffix . ' .inline-item").forEach(function (el) { ';
	 				echo 'el.classList.add("hide"); ';
	 			echo '}, this); ';

	 			echo 'document.querySelectorAll("' . $suffix . ' .latestnews-item").forEach(function (item, index) { ';
	 				echo 'item.querySelectorAll(".inline-link").forEach(function (link) { ';
	 					echo 'link.addEventListener("click", toggle.bind(event, index)); ';
	 				echo '}, this); ';
	 			echo '}, this); ';

 			echo '} ';
 		echo '}); ';

		return ob_get_clean();
	}

}