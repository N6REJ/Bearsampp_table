<?php
/**
* J51_Testimonials
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

// Load CSS/JS
if (file_exists('media/j51_assets/css/tiny-slider.min.css')) { 
    HTMLHelper::_('stylesheet', 'j51_assets/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto')); 
} else { 
    HTMLHelper::_('stylesheet', 'mod_j51testimonials/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto'));
}

HTMLHelper::_('stylesheet', 'mod_j51testimonials/style.css', array('relative' => 'auto'));

if (file_exists('media/j51_assets/js/tiny-slider.min.js')) { 
    HTMLHelper::_('script', 'j51_assets/tiny-slider.min.js', array('relative' => 'auto', 'version' => 'auto')); 
} else { 
    HTMLHelper::_('script', 'mod_j51testimonials/tiny-slider.min.js', array('relative' => 'auto', 'version' => 'auto'));
}

$doc->addScriptDeclaration('
	document.addEventListener("DOMContentLoaded", () => {
	    var slider = tns({
			container: "#j51-testimonials'.$j51_moduleid.'",
			items: 1,
			slideBy: "page",
			controls: false,
			autoplay: '.$j51_autoplay.',
			autoplayTimeout: '.$j51_transition_interval.',
			speed: '.$j51_transition_duration.',
			navContainer: "#j51-testimonials'.$j51_moduleid.'-nav",
			gutter: '.$j51_horiz_padding.',
			autoplayButton: "#j51-testimonials'.$j51_moduleid.'-play",
			mouseDrag: true,
			responsive:{
		        0:{
		            items: '.$j51_image_width_mobp.',
		        },
		        440:{
		            items: '.$j51_image_width_mobl.',
		        },
		        767:{
		            items: '.$j51_image_width_tabp.',
		        },
		        959:{
		            items: '.$j51_image_width_tabl.',
		        },
		        1024:{
		            items: '.$j51_blocks_per_slide.',
		        }
		    },
	    });
	});
'); 

$doc->addStyleDeclaration('
	.j51-testimonials'.$j51_moduleid.' .j51-text-box {
		background-color: '.$j51_quote_bg.';
	}
');
if (!empty($j51_quote_color)) {
	$doc->addStyleDeclaration('
		.j51-testimonials'.$j51_moduleid.' .j51-text-box {
			color: '.$j51_quote_color.' !important;
		}
	');
}

?>

<div class="j51-testimonials<?php echo $j51_moduleid; ?> j51-testimonials" style="margin-top: <?php echo $j51_vert_padding ?>px; margin-bottom: <?php echo $j51_vert_padding ?>px;">
	<div id="j51-testimonials<?php echo $j51_moduleid; ?>" class="j51-testimonials-wrapper">
		<?php
		if(!empty($j51_items)) {
	    	foreach ($j51_items as $item) {
		?>
		<div class="item">
			<?php if (!empty($item->j51_caption)) { ?>
			<div class="j51-text-box">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="j51-quote-icon"><path d="M512 80v128c0 137.018-63.772 236.324-193.827 271.172-15.225 4.08-30.173-7.437-30.173-23.199v-33.895c0-10.057 6.228-19.133 15.687-22.55C369.684 375.688 408 330.054 408 256h-72c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h128c26.51 0 48 21.49 48 48zM176 32H48C21.49 32 0 53.49 0 80v128c0 26.51 21.49 48 48 48h72c0 74.054-38.316 119.688-104.313 143.528C6.228 402.945 0 412.021 0 422.078v33.895c0 15.762 14.948 27.279 30.173 23.199C160.228 444.324 224 345.018 224 208V80c0-26.51-21.49-48-48-48z"/></svg>
				<p><?php echo htmlspecialchars($item->j51_caption, ENT_COMPAT, 'UTF-8'); ?></p>
			</div>
			<?php } ?>
			<div class="j51-profile">
				<?php if (!empty($item->j51_image)) { ?>
				<div class="j51-profile-image">
					<img src="<?php echo htmlspecialchars($item->j51_image, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->j51_name, ENT_COMPAT, 'UTF-8'); ?>">
				</div>
				<?php } ?>
				<div class="j51-profile-details">
					<?php if (!empty($item->j51_name)) { ?>
					<h4 class="j51-profile-name"><?php echo htmlspecialchars($item->j51_name, ENT_COMPAT, 'UTF-8'); ?></h4>
					<?php } ?>
					<?php if (!empty($item->j51_title)) { ?>
					<h6 class="j51-profile-title"><?php echo htmlspecialchars($item->j51_title, ENT_COMPAT, 'UTF-8'); ?></h6>
					<?php } ?>
					<?php if (!empty($item->j51_rating)) { ?>
					<div class="star-rating">
						<span style="width:<?php echo htmlspecialchars($item->j51_rating, ENT_COMPAT, 'UTF-8'); ?>%"></span>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php 
			}
	  	} 
		?>
	</div>
	<div id="j51-testimonials<?php echo $j51_moduleid; ?>-play" style="display:none;"></div>
	<div class="j51-nav-dots" id="j51-testimonials<?php echo $j51_moduleid; ?>-nav">
		<?php
		if(!empty($j51_items)) {
	    	foreach ($j51_items as $item) {
		?>
		<span role="button" class="j51-nav-dot"><span></span></span>
		<?php 
			}
	  	} 
		?>
	</div>
</div>



