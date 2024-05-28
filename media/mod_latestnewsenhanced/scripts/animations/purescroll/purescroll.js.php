<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use SYW\Library\Utilities as SYWUtilities;

// Explicitly declare the type of content
//header("Content-type: text/javascript; charset=UTF-8");

$warning_message = '';

if ($item_width_unit == '%') { // to avoid erratic behavior
	if ($horizontal) {
		if (intval($visibleatonce) > 1) {
			if ($min_width) {
				$item_width = $min_width;
			} else {
				$warning_message = 'MOD_LATESTNEWSENHANCEDEXTENDED_WARNING_SETITEMWIDTHINPIXELS';
			}
		}
	} else {
		//$warning_message = 'MOD_LATESTNEWSENHANCEDEXTENDED_WARNING_SETITEMWIDTHINPIXELS';
	}
}

// DO NOT ADD COMMENTS TO THE CODE -  IT WILL PREVENT COMPRESSION
?>
document.addEventListener("readystatechange", function(event) {
	if (event.target.readyState === "complete") {

		<?php if ($warning_message && $show_errors) : ?>
			<?php jimport('syw.utilities', JPATH_LIBRARIES); ?>
			document.querySelector("<?php echo $css_prefix ?>_loader").style.display = 'none';
			var module = document.querySelector("<?php echo $css_prefix ?>");
			module.insertAdjacentHTML('beforebegin', '<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-warning', $bootstrap_version) ?>"><?php echo Text::_($warning_message); ?></div>');
		<?php else : ?>

			var lne_<?php echo $suffix ?>_carousel = tns({
				container: "<?php echo $css_prefix ?> ul.latestnews-items",
				
				<?php if (Factory::getDocument()->getDirection() == 'rtl') : ?>
					textDirection: "rtl",
				<?php endif; ?>

				<?php if (!$horizontal) : ?>
					axis: "vertical",
					<?php if (intval($visibleatonce) == 1) : ?>
						autoHeight: true,
					<?php endif; ?>
					items: <?php echo $visibleatonce ?>,
					<?php if (intval($moveatonce) > 1) : ?>
						slideBy: <?php echo $moveatonce ?>,
					<?php endif; ?>
				<?php else : ?>
					<?php if (intval($visibleatonce) > 1) : ?>
						responsive: {
							<?php for ($x = 0; $x < intval($visibleatonce); $x++) : ?>
								<?php if ($x == 0) : ?>
									0: {
								<?php else : ?>
									<?php echo $x * $item_width + $item_width ?>: {
								<?php endif; ?>
								items: <?php echo $x + 1 ?>
								<?php if (intval($moveatonce) > 1) : ?>
									, slideBy: <?php echo $x + 1 ?>
								<?php endif; ?>
								}
								<?php if ($x < intval($visibleatonce) - 1) : ?>
									,
								<?php endif; ?>
							<?php endfor; ?>
						},
					<?php else : ?>
						items: <?php echo $visibleatonce ?>,
					<?php endif; ?>
				<?php endif; ?>

				swipeAngle: false,
				gutter: <?php echo $space_between_items ?>,
				controls: false,

				<?php if (!$symbols) : ?>
					nav: false,
				<?php else : ?>
					navPosition: "bottom",
				<?php endif; ?>

				<?php if ($auto) : ?>
					autoplay: true,
					autoplayTimeout: <?php echo $interval ?>,
					<?php if (!$restart_on_refresh) : ?>
						autoplayResetOnVisibility: false,
					<?php endif; ?>
					autoplayHoverPause: true,
					autoplayButtonOutput: false,
				<?php endif; ?>

				mouseDrag: true,
				arrowKeys: true,
				speed: <?php echo $speed ?>,

				onInit: function (data) {
					var lne = document.querySelector("<?php echo $css_prefix ?>");
					if (lne.classList) { lne.classList.add("show"); } else { lne.className += " show" }

					<?php if ($arrows) : ?>
						if (data.items < <?php echo $visibleatonce ?> || data.slideCount > <?php echo $visibleatonce ?>) {
							var elems = document.querySelectorAll("<?php echo $css_prefix ?> .items_pagination");
							var nav_length = elems.length;
							for (var i = 0; i < nav_length; i++) {
								elems[i].style.opacity = 1;
							}
						}

						document.querySelector("#next_<?php echo $suffix ?>").addEventListener("click", function (e) {
							e.preventDefault();
							lne_<?php echo $suffix ?>_carousel.goTo("next");
						});

						document.querySelector("#prev_<?php echo $suffix ?>").addEventListener("click", function (e) {
							e.preventDefault();
							lne_<?php echo $suffix ?>_carousel.goTo("prev");
						});
					<?php endif; ?>
				}
			});

			<?php if ($arrows) : ?>
				var lne_resizeId_<?php echo $suffix ?>;
				window.addEventListener("resize", function() {
					clearTimeout(lne_resizeId_<?php echo $suffix ?>);
					lne_resizeId_<?php echo $suffix ?> = setTimeout(lne_doneResizing_<?php echo $suffix ?>, 100);
				});

				function lne_doneResizing_<?php echo $suffix ?>() {
					var info = lne_<?php echo $suffix ?>_carousel.getInfo();
					var elems = document.querySelectorAll("<?php echo $css_prefix ?> .items_pagination");
					var nav_length = elems.length;
					for (var i = 0; i < nav_length; i++) {
						if (info.items < <?php echo $visibleatonce ?> || info.slideCount > <?php echo $visibleatonce ?>) {
							elems[i].style.opacity = 1;
						} else {
							elems[i].style.opacity = 0;
						}
					}
				}
			<?php endif; ?>

			<?php if ($auto) : ?>
				document.addEventListener("modalopen", function() {
					lne_<?php echo $suffix ?>_carousel.pause();
				}, false);

				document.addEventListener("modalclose", function() {
					lne_<?php echo $suffix ?>_carousel.play();
				}, false);
			<?php endif; ?>
		<?php endif; ?>
	}
});
