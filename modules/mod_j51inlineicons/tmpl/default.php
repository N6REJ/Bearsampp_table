<?php
/**
* J51_InlineIcons
* Version		: 1.0
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* @license         GNU General Public License version 2 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$baseurl 		= JURI::base();
$j51_moduleid       = $module->id;

$j51_inlineicons = $params->get('j51_inlineicons');
$j51_iconclass = $params->get('j51_iconclass');
$j51_icontitle = $params->get('j51_icontitle');
$j51_iconurl = $params->get('j51_iconurl');
$j51_icontarget = $params->get('j51_icontarget');
$j51_iconsize = $params->get('j51_iconsize');
$j51_iconcolor = $params->get('j51_iconcolor');
$j51_iconhovercolor = $params->get('j51_iconhovercolor');
$j51_iconalign = $params->get('j51_iconalign');
$j51_icon_margin_y = $params->get('j51_icon_margin_y');
$j51_icon_margin_x = $params->get('j51_icon_margin_x');

// Load CSS/JS
$document = JFactory::getDocument();

$document->addStyleSheet (JURI::base() . 'modules/mod_j51inlineicons/css/balloon.css' );
$document->addStyleSheet (JURI::base() . 'modules/mod_j51inlineicons/css/style.css' );

$document->addStyleDeclaration('
.j51_inlineicons'.$j51_moduleid.' i:before {
    font-size: '.$j51_iconsize.'px;
}
.j51_inlineicons'.$j51_moduleid.' {
	text-align: '.$j51_iconalign.';
}
.j51_inlineicons'.$j51_moduleid.' .j51_inlineicon {
	margin: '.$j51_icon_margin_y.'px '.$j51_icon_margin_x.'px;
}

');

if (!empty($j51_iconhovercolor)) {
	$document->addStyleDeclaration('
		.j51_inlineicons'.$j51_moduleid.' .j51_inlineicon:hover i:before {
		    color: '.$j51_iconhovercolor.' !important;
		}
		.j51_inlineicons'.$j51_moduleid.' [data-balloon]:after {
		    background-color: '.$j51_iconhovercolor.' !important;
		}
		.j51_inlineicons'.$j51_moduleid.' [data-balloon]:before {
		  border-color: '.$j51_iconhovercolor.' transparent transparent transparent;
		}
	');
}

	echo '<div class="j51_inlineicons j51_inlineicons'.$j51_moduleid.'">';
	
	    foreach ($j51_inlineicons as $item) { 
	    	$target = '';
	    	if ($item->j51_icontarget) {
	    		$target = 'target="_blank"';
	    	}
	    echo '<a href="'.$item->j51_iconurl.'" class="j51_inlineicon" data-balloon="'.$item->j51_icontitle.'" data-balloon-pos="up" '.$target.'><i class="fa '.$item->j51_iconclass.'" title="'.$item->j51_icontitle.'" style="color:'.$item->j51_iconcolor.'" ></i></a>';
 		} 

 	echo '</div>';

?>