<?php
/**
* J51_ImageHover
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* Licensed under the GPL v2&
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

$document = JFactory::getDocument();

// Load CSS/JS
HTMLHelper::_('stylesheet', 'mod_j51imagehover/imagehover.min.css', array('relative' => 'auto'));
HTMLHelper::_('stylesheet', 'mod_j51imagehover/style.css', array('relative' => 'auto'));
HTMLHelper::_('stylesheet', 'mod_j51imagehover/baguetteBox.min.css', array('relative' => 'auto'));

HTMLHelper::_('script', 'mod_j51imagehover/script.js', array('relative' => 'auto', 'version' => 'auto'));

if (file_exists('media/j51_assets/js/imagesloaded.pkgd.min.js')) { 
    JHtml::_('script', 'j51_assets/imagesloaded.pkgd.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
} else { 
    JHtml::_('script', 'mod_j51imagehover/imagesloaded.pkgd.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
}
if (file_exists('media/j51_assets/js/masonry.pkgd.min.js')) { 
    JHtml::_('script', 'j51_assets/masonry.pkgd.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
} else { 
    JHtml::_('script', 'mod_j51imagehover/masonry.pkgd.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
}
$document->addScriptDeclaration('
    document.addEventListener("DOMContentLoaded", function() {
        var elem = document.querySelector(".j51imagehover'.$j51_moduleid.'");
        imagesLoaded( elem, function() {
            var msnry = new Masonry( elem, {
                itemSelector: ".j51imghvr-item"
            });
        });
    });
');

// Lightbox
HTMLHelper::_('script', 'mod_j51imagehover/baguetteBox.min.js', array('relative' => 'auto', 'version' => 'auto'));

// Styling from module parameters
$document->addStyleDeclaration('
	.j51imagehover' . $j51_moduleid . ' {
		--j51-title-color: ' . $j51_title_color . ';
		--j51-caption-color: ' . $j51_text_color . ';
		--j51-overlay-color: ' . $j51_overlay_color . ';
	}
	.j51imagehover' . $j51_moduleid . ' figcaption {
		display: flex;
	    flex-direction: column;
	    justify-content: ' . $j51_text_vert_align . ';
		padding:' . $j51_image_padding_y . 'px ' . $j51_image_padding_x . 'px;
		text-align: ' . $j51_text_align . ';
	}
	.j51imagehover' . $j51_moduleid . ',
	.j51imagehover' . $j51_moduleid . ' .tns-inner {
		margin: -' . ($j51_image_margin_y / 2) . 'px -' . ($j51_image_margin_x / 2) . 'px;
	}
	.j51imagehover' . $j51_moduleid . ' .j51imghvr-item {
		display: inline-block;
		width:' . $j51_image_width . ';
		padding:' . ($j51_image_margin_y / 2) . 'px ' . ($j51_image_margin_x / 2) . 'px;
		box-sizing: border-box;
	}
	@media only screen and (min-width: 960px) and (max-width: 1280px) {
		.j51imagehover' . $j51_moduleid . ' .j51imghvr-item {width:' . $j51_image_width_tabl . ';}
	}
	@media only screen and (min-width: 768px) and (max-width: 959px) {
		.j51imagehover' . $j51_moduleid . ' .j51imghvr-item {width:' . $j51_image_width_tabp . ';}
	}
	@media only screen and ( max-width: 767px ) {
		.j51imagehover' . $j51_moduleid . ' .j51imghvr-item {width:' . $j51_image_width_mobl . ';}
	}
	@media only screen and (max-width: 440px) {
		.j51imagehover' . $j51_moduleid . ' .j51imghvr-item {width:' . $j51_image_width_mobp . ';}
	}
');

?>
<div class="j51imagehover j51imagehover<?php echo $j51_moduleid; ?>">
	<?php foreach ($imagehover_images as $item) : ?>
	<div class="j51imghvr-item" style="
	--j51-overlay-color:<?php 
	if ($item->j51_override_style && !empty($item->j51_overlay_color)) { 
		echo $item->j51_overlay_color; 
	} elseif (!empty($j51_overlay_color)) { 
		echo $j51_overlay_color;
	} else {
		echo 'var(--primary)';
	} ?>;
	--j51-title-color:<?php 
	if ($item->j51_override_style && !empty($item->j51_title_color)) { 
		echo $item->j51_title_color; 
	} elseif (!empty($j51_title_color)) { 
		echo $j51_title_color; ?>;
	<?php } ?>;
	--j51-text-color:<?php 
	if ($item->j51_override_style && !empty($item->j51_text_color)) { 
		echo $item->j51_text_color; 
	} elseif (!empty($j51_text_color)) { 
		echo $j51_text_color; ?>;
	<?php } ?>;
	">
	<?php 
	if ($item->j51_override_style) {
		$hover_effect = $item->j51_imghvr;
	} else { 
		$hover_effect = $j51_imghvr; 
	}
	?>
		<figure class="<?php echo htmlspecialchars($hover_effect, ENT_COMPAT, 'UTF-8'); ?>">
			<img src="<?php echo htmlspecialchars($item->j51_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->j51_image, ENT_QUOTES, 'UTF-8'); ?>" 
				<?php if (empty($item->j51_title) && empty($item->j51_text) && empty($item->j51_target_url)) : ?>
					style="transform: none; opacity: 1;"
				<?php endif; ?>
				>
			<?php if (!empty($item->j51_title) || !empty($item->j51_text)) : ?>
				<figcaption>
					<?php if ($item->j51_title != '') : ?>
					<h3 class="j51imagehover_title">
						<?php echo $item->j51_title; ?>
					</h3>
					<?php endif; ?>
					<?php if ($item->j51_text != '') : ?>
					<p class="j51imagehover_caption">
						<?php echo $item->j51_text; ?>
					</p>
					<?php endif; ?>
				</figcaption>
			<?php else : ?>
				<?php if (!empty($item->j51_target_url) || $item->j51_link_type === 'lightbox') : ?>
				<figcaption style="align-items: center;">
					<?php if (!empty($svg_code)) {
						echo $svg_code;
					} else { ?>
						<svg aria-hidden="true" focusable="false" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
					<?php } ?>
					
				</figcaption>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($item->j51_link_type === 'url') { ?>
				<?php if (!empty($item->j51_target_url)) : ?>
					<a href="<?php echo htmlspecialchars($item->j51_target_url, ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo $j51_target; ?>"></a>
				<?php endif; ?>
			<?php } elseif ($item->j51_link_type === 'menuitem') { ?>
				<?php if (!empty($item->j51_target_menuitem)) : ?>
					<a href="<?php echo JRoute::_("index.php?Itemid={$item->j51_target_menuitem}"); ?>" target="<?php echo $j51_target; ?>"></a>
				<?php endif; ?>
			<?php } elseif ($item->j51_link_type === 'lightbox') { ?>
				<a href="<?php echo htmlspecialchars($item->j51_image, ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo htmlspecialchars($j51_target, ENT_QUOTES, 'UTF-8'); ?>" data-caption="<?php echo htmlspecialchars($item->j51_title, ENT_QUOTES, 'UTF-8'); ?><?php if (!empty($item->j51_title) && !empty($item->j51_text)) : ?> - <?php endif; ?><?php echo htmlspecialchars($item->j51_text, ENT_QUOTES, 'UTF-8'); ?>">
					<img src="<?php echo htmlspecialchars($item->j51_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->j51_title, ENT_QUOTES, 'UTF-8'); ?>" style="display: none;">
				</a>
			<?php } ?>
		</figure>
	</div>
	<?php endforeach; ?>
</div>
