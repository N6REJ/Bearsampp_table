<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace SYW\Plugin\Content\ArticleDetailsProfiles\Field;

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

/*
 * Preview for setting up icons
 */
class PreviewiconField extends FormField
{
	public $type = 'Previewicon';

	protected $head_element;

	protected function getInput()
	{
		$wam = Factory::getApplication()->getDocument()->getWebAssetManager();

		$wam->addInlineScript('
			document.addEventListener("readystatechange", function(event) {
				if (event.target.readyState == "complete") {

					function resize_font() {
						let head_width = parseInt(document.getElementById("jform_params_head_w").value);
						let head_height = parseInt(document.getElementById("jform_params_head_h").value);
						if (head_width >= head_height) {
							document.querySelector(".preview_icon i").style.fontSize = (head_height - document.getElementById("jform_params_border_w_icon").value * 2 - document.getElementById("jform_params_padding_icon").value * 2) + "px";
						} else {
							document.querySelector(".preview_icon i").style.fontSize = (head_width - document.getElementById("jform_params_border_w_icon").value * 2 - document.getElementById("jform_params_padding_icon").value * 2) + "px";
						}
					}

					let preview = document.querySelector(".preview_icon");
					let icon_preview = preview.querySelector("i");

					let preview_width = parseInt(document.getElementById("jform_params_head_w").value);
					let preview_height = parseInt(document.getElementById("jform_params_head_h").value);

					if (preview_width > 0) {
						document.querySelector(".preview_icontype").style.width = preview_width + "px";
						document.querySelector(".preview").style.width = preview_width + "px";
					}

					document.getElementById("jform_params_head_w").addEventListener("change", function(event) {
						document.querySelector(".preview_icontype").style.width = document.getElementById("jform_params_head_w").value + "px";
						document.querySelector(".preview").style.width = document.getElementById("jform_params_head_w").value + "px";
						resize_font();
					});

					if (preview_height > 0) {
						document.querySelector(".preview_icontype").style.height = preview_height + "px";
						document.querySelector(".preview").style.height = preview_height + "px";
					}

					document.getElementById("jform_params_head_h").addEventListener("change", function(event) {
						document.querySelector(".preview_icontype").style.height = document.getElementById("jform_params_head_h").value + "px";
						document.querySelector(".preview").style.height = document.getElementById("jform_params_head_h").value + "px";
						resize_font();
					});

					resize_font();

					preview.style.backgroundColor = document.getElementById("visible_jform_params_icon_bgcolor").value;
					icon_preview.style.color = document.getElementById("jform_params_icon_color").value;

					document.getElementById("a_jform_params_icon_bgcolor").addEventListener("click", function(event) {
						document.querySelector(".preview_icon").style.backgroundColor = "";
					});

					document.getElementById("select_jform_params_icon_bgcolor").querySelectorAll("li[data-keyword]").forEach (function (option) {
						option.addEventListener("click", function(event) {
							document.querySelector(".preview_icon").style.backgroundColor = event.target.getAttribute("data-keyword");
						});
					});

					document.getElementById("visible_jform_params_icon_bgcolor").addEventListener("input", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.backgroundColor = event.target.value;
						}
					});

					document.getElementById("jform_params_icon_color").addEventListener("input", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon i").style.color = event.target.value;
						}
					});

					preview.style.border = document.getElementById("jform_params_border_w_icon").value + "px solid " + document.getElementById("jform_params_border_c_icon").value;
					preview.style.borderRadius = document.getElementById("jform_params_border_r_icon").value + "px";

					document.getElementById("jform_params_border_w_icon").addEventListener("change", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.border = event.target.value + "px solid " + document.getElementById("jform_params_border_c_icon").value;
							document.querySelector(".preview_icon").style.borderRadius = document.getElementById("jform_params_border_r_icon").value + "px";
							resize_font();
						}
					});

					document.getElementById("jform_params_border_c_icon").addEventListener("input", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.border = document.getElementById("jform_params_border_w_icon").value + "px solid " + event.target.value;
							document.querySelector(".preview_icon").style.borderRadius = document.getElementById("jform_params_border_r_icon").value + "px";
						}
					});

					document.getElementById("jform_params_border_r_icon").addEventListener("change", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.borderRadius = event.target.value + "px";
						}
					});

					preview.style.boxShadow = "0 0 " + document.getElementById("jform_params_sh_w_icon").value + "px rgba(0, 0, 0, 0.8)";
					document.getElementById("jform_params_sh_w_icon").addEventListener("change", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.boxShadow = "0 0 " + event.target.value + "px rgba(0, 0, 0, 0.8)";
						}
					});

					icon_preview.style.textShadow = "0 0 " + document.getElementById("jform_params_sh_w_text_icon").value + "px rgba(0, 0, 0, 0.8)";
					document.getElementById("jform_params_sh_w_text_icon").addEventListener("change", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon i").style.textShadow = "0 0 " + event.target.value + "px rgba(0, 0, 0, 0.8)";
						}
					});

					preview.style.padding = document.getElementById("jform_params_padding_icon").value + "px";
					document.getElementById("jform_params_padding_icon").addEventListener("change", function(event) {
						if (event.target.value != "") {
							document.querySelector(".preview_icon").style.padding = event.target.value + "px";
							resize_font();
						}
					});

					let icon_value = document.getElementById("jform_params_default_icon").value;
					if (icon_value != "") {
						icon_preview.className = icon_value;
					} else {
						icon_preview.className = "SYWicon-leaf";
					}

					document.getElementById("jform_params_default_icon_default").addEventListener("click", function(event) {
						let icon_preview = document.querySelector(".preview_icon i");
						icon_preview.className = "SYWicon-leaf";
					});
			
					let icon_ul = document.getElementById("jform_params_default_icon_select");
					icon_ul.querySelectorAll("li").forEach(function (el) {
						el.addEventListener("click", function(event) {
							let icon_preview = document.querySelector(".preview_icon i");
							icon_preview.className = "";
							icon_preview.className = event.target.closest("li").getAttribute("data-icon");
							if (icon_preview.className == "") {
								icon_preview.className = "SYWicon-leaf";
							}
						});
					});
			
					document.getElementById("jform_params_default_icon").addEventListener("change", function(event) {
						let icon_preview = document.querySelector(".preview_icon i");
						icon_preview.className = "";
						icon_preview.className = event.target.value;
						if (icon_preview.className == "") {
							icon_preview.className = "SYWicon-leaf";
						}
					});

				}
			});
		');

		$html = '';

		$html .= '<div class="preview" style="padding: 20px; background-color: #fbfbfb; border: 2px dashed #ccc; -webkit-border-radius: 10px; border-radius: 10px; box-sizing: initial">';
			$html .= '<div class="preview_icontype" style="margin: 0 auto;">';
				$html .= '<div class="preview_icon" style="display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex; display: flex; justify-content: center; align-items: center; width: 100%; height: 100%; box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box;">';
					$html .= '<i></i>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->head_element = isset($this->element['head_element']) ? $this->element['head_element'] : null;
		}

		return $return;
	}

}
?>