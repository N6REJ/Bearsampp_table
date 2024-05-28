<?php
/**
* @title	    J51 Thumbs Gallery
* @version		1.1
* @website		http://www.joomla51.com
* @copyright	Copyright (C) 2012 Joomla51.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$document 			= JFactory::getDocument();

$baseurl    		= JURI::base();

$document->addCustomTag("<script src='".$baseurl."modules/mod_j51thumbsgallery/js/script.js'></script>");
$document->addCustomTag("<script src='".$baseurl."modules/mod_j51thumbsgallery/js/baguetteBox.js'></script>");

$document->addStyleSheet('modules/mod_j51thumbsgallery/css/thumbs_style.css');
$document->addStyleSheet('modules/mod_j51thumbsgallery/css/baguetteBox.css');

$list = GalleryHelper::getimgList($params);
$j51_moduleid       = $module->id;

JHtml::_('jquery.framework');

$document->addScriptDeclaration('
    jQuery(document).ready(function() {
		jQuery(".j51thumbs'.$j51_moduleid.' i").animate({
				 opacity: 0
			  }, {
				 duration: 300,
				 queue: false
			  });      
	   jQuery(".j51thumbs'.$j51_moduleid.'").parent().hover(
		   function () {},
		   function () {
			  jQuery(".j51thumbs'.$j51_moduleid.' i").animate({
				 opacity: 0
			  }, {
				 duration: 300,
				 queue: false
			  });
	   });
	   jQuery(".j51thumbs'.$j51_moduleid.' i").hover(
	      function () {
			  jQuery(this).animate({
				 opacity: 0
			  }, {
				 duration: 300,
				 queue: false
			  });      
			  jQuery(".j51thumbs'.$j51_moduleid.' i").not( jQuery(this) ).animate({
				 opacity: ' . $fade_opacity . '
			  }, {
				 duration: 300,
				 queue: false
			  });
	      }, function () {
	      }
	   );
	});
');
?>
<div class="j51thumbs<?php echo $j51_moduleid; ?>" style="margin-left:-<?php echo $margin;?>px;">
	<div class="j51thumbs" style="text-align: <?php echo $alignment;?>">
		<div class="gallery">
			<?php foreach($list as $item) { ?><a class="j51Box fancybox" style="margin:<?php echo ($margin/2) ?>px <?php echo ($margin/2);?>px;" data-fancybox-group="gallery" href="<?php echo $item['image'] ?>">
				<img src="<?php echo $item['thumb'] ?>" 
				style="padding:<?php echo $bordersize;?>px;
				background-color:<?php echo $bordercolor;?>;
				<?php if ($bordersize != 0) { ?>border:1px solid <?php echo $outlinecolor;?>;<?php } ?>"
				alt="<?php echo $item['image'] ?>" />
				<i style="
					top: <?php if ($bordersize != 0) { echo ($bordersize + 1); } else { echo $bordersize;} ?>px;
					left: <?php if ($bordersize != 0) { echo ($bordersize + 1); } else { echo $bordersize;} ?>px;
					bottom: <?php if ($bordersize != 0) { echo ($bordersize + 1); } else { echo $bordersize;} ?>px;
					right: <?php if ($bordersize != 0) { echo ($bordersize + 1); } else { echo $bordersize;} ?>px;
					">
				</i>
			</a><?php } ?>
		</div>
	</div>
</div> 

<div class="clear"></div> 
