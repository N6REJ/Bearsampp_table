<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );

$wa->useStyle('fontawesome');
$document->addStyleDeclaration('


.layerslideshow > .tns-item {
	transition-delay: 0.5s;
	animation-delay: 0.5s;
}

body {
	--text-main: '.$this->params->get('body_font_color').';
	--primary: '.$this->params->get('primary_color').';
	--secondary: '.$this->params->get('secondary_color').';
	--primary-color: '.$this->params->get('primary_color').';
	--secondary-color: '.$this->params->get('secondary_color').';
	--base-color: '.$this->params->get('body_font_color').';
	--content-link-color: '.$this->params->get('content_link_color').';
	--button-color: '.$this->params->get('button_color').';
	--button-hover-color: '.$this->params->get('button_hover_color').';content_link_color
	--hornav_font_color: '.$this->params->get('hornav_font_color').';
	--header_bg: '.$this->params->get('header_bg').';
	--mobile-menu-bg: '.$this->params->get('mobile_menu_bg').';
	--mobile-menu-toggle: '.$this->params->get('mobile_menu_color').';
	--h1-color: '.$this->params->get('h1head_font_color').';
	--h2-color: '.$this->params->get('articletitle_font_color').';
	--h3-color: '.$this->params->get('modulehead_font_color').';
	--h4-color: '.$this->params->get('h4head_font_color').'; 
}
.blog-alternative .item,
.blog-alternative .item-content {
    width: 100%;
}
body, .hornav ul ul, .hornav ul ul a {
	font-family:'.str_replace("+"," ",$body_fontstyle).', Arial, Verdana, sans-serif;
	font-size: '.$this->params->get('body_fontsize').'px;
}
a {
	color: '.$this->params->get('content_link_color').';
}
h1 {
	font-family:'.str_replace("+"," ",$h1head_fontstyle).', Arial, Verdana, sans-serif; 
}
h2, 
h2 a:link, 
h2 a:visited {
	font-family:'.str_replace("+"," ",$articlehead_fontstyle).', Arial, Verdana, sans-serif;
}
h3 {
	font-family:'.str_replace("+"," ",$modulehead_fontstyle).', Arial, Verdana, sans-serif;
}
h4 {
	font-family:'.str_replace("+"," ",$h4head_fontstyle).', Arial, Verdana, sans-serif;
}
.hornav, .btn, .button, button {
	font-family:'.str_replace("+"," ",$hornav_fontstyle).' 
}
.wrapper960 {
	width: '.$this->params->get('wrapper_width').'px;
}
.logo {
	top: '.$this->params->get('logo_y').'px;
	left: '.$this->params->get('logo_x').'px;
}
.logo-text {
	color: '.$this->params->get('logo_font_color').';
	font-family:'.str_replace("+"," ",$logo_fontstyle).';
	font-size: '.$this->params->get('logo_font_size').'px;
}
.scrolled .logo-text {
	color: '.$this->params->get('logo_font_color-scrolled').';
}
.logo-slogan {
	color: '.$this->params->get('slogan_font_color').';
	font-size: '.$this->params->get('slogan_font_size').'px;
}

.hornav ul.menu li a,
.hornav ul.menu li span, 
.hornav > ul > .parent::after {
	color: '.$this->params->get('hornav_font_color').';
}
.hornav ul.menu ul li a,
.hornav ul.menu ul li span {
	color: '.$this->params->get('hornav_dd_color').';
}
.hornav ul ul {
	background-color: '.$this->params->get('hornav_ddbackground_color').';
}
.hornav ul ul:before {
	border-color: transparent transparent '.$this->params->get('hornav_ddbackground_color').' transparent;
}
.sidecol_a {
	width: '.$this->params->get('sidecola_width').'%;
}
.sidecol_b {
	width: '.$this->params->get('sidecolb_width').'%;
}
ul.dot li::before,
.text-primary {
 	color: '.$this->params->get('primary_color').';
 }
.j51news .hover-overlay,
.background-primary {
	background-color: '.$this->params->get('primary_color').';
}
.btn, button, .pager.pagenav a, .btn:hover, .slidesjs-next.slidesjs-navigation, .slidesjs-previous.slidesjs-navigation {
	background-color: '.$this->params->get('button_color').';
	color: #fff;
}
.btn, .button, button {
	background-color: '.$this->params->get('button_color').';
}
.btn:hover, button:hover, .btn:focus, .btn:active, .btn.active, .readmore .btn:hover, .dropdown-toggle:hover {
	background-color: '.$this->params->get('button_hover_color').';
	color: #ffffff;
}
.nav-tabs > .active > a, 
.nav-tabs > .active > a:hover, 
.nav-tabs > .active > a:focus {
	border-bottom-color: '.$this->params->get('button_color').';
}
blockquote {
	border-color: '.$this->params->get('button_color').';
}
.btn:hover, .button:hover, button:hover {
	border-color: '.$this->params->get('button_hover_color').';
}
body {
	background-color: '.$this->params->get('bgcolor','#fff').';
}
.showcase_seperator svg {
	fill: '.$this->params->get('bgcolor').';
}
#container_main {
	background-color: '.$this->params->get('elementcolor3').';
	position: relative;
}
.container_footer {
	background-color: '.$this->params->get('footer_color').';
}
[id] {
	scroll-margin-top: '.$this->params->get('scrollto_offset').'px;
}
#container_header {
	background-color: '.$this->params->get('header_bg').';
}
');

if($this->params->get('sticky_sw'))  {
	$document->addStyleDeclaration('
	.is-sticky #container_header {
		background-color: '.$this->params->get('header_bg').' !important;
	}
	');
}

// Header
$document->addStyleDeclaration('
	.body_bg {
		background-color: '.$this->params->get('body_bg', '#fff').';
	}
	.header_top {
		background-color: '.$this->params->get('header_top_bg').';
	}
');

// Base Background Image
if($this->params->get('base_bg') != '')  {
	$document->addStyleDeclaration('#container_base {background-image: url('.$this->baseurl.'/'.$this->params->get('base_bg').');}');
}

// Responsive Logo
if($this->params->get('mobilelogoimagefile') != '')  {
	$document->addStyleDeclaration('@media only screen and (max-width: '.$this->params->get('mobilelogo_breakpoint').'px) {
		.primary-logo-image, .logo-image-scrolled{display:none !important;}
		.mobile-logo-image{display:inline-block !important;}
	}');
}

// Responsive Style
$document->addCustomTag('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"/>');
$document->addStyleDeclaration('
	@media only screen and (max-width: '.$this->params->get('wrapper_width').'px) {
		.module_block, .wrapper960  {
			width: 100% !important;
		}	
	}
	@media only screen and (max-width: '.$this->params->get('hornav_breakpoint').'px) {
		.hornav:not(.header-3) {display:none !important;}
		.menu-toggle {display: flex;}
	}
');

// Responsive Switches
if($this->params->get('res_sidecola_sw') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.sidecol_a {display:none;}}');
}
if($this->params->get('res_sidecolb_sw') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.sidecol_b {display:none;}}');
}
if($this->params->get('res_header1_sw') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.header-1 {display:none;}}');
}
if($this->params->get('res_header2_sw') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.header-2 {display:none;}}');
}
if($this->params->get('mobile_showcase_sw') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.showcase {display:none;} .mobile_showcase {display:inline;}}');
}



