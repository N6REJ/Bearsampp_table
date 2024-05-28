<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

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
        $warning_message = 'MOD_LATESTNEWSENHANCEDEXTENDED_WARNING_SETITEMWIDTHINPIXELS';
    }
}

$item_class_attribute = '';
$link_class_attribute = '';
if ($pagination_style && $bootstrap_version >= 4) {
    $item_class_attribute = ' class="page-item"';
    $link_class_attribute = ' class="page-link"';
}

// DO NOT ADD COMMENTS TO THE CODE -  IT WILL PREVENT COMPRESSION
?>

(function($) {
	$(window).load(function() {

	<?php if ($warning_message && $show_errors) : ?>
		$("<?php echo $css_prefix ?>").empty().show();
		$("<?php echo $css_prefix ?>_loader").remove();
    	$("<?php echo $css_prefix ?>").prepend('<div class="<?php echo SYWUtilities::getBootstrapProperty('alert alert-warning', $bootstrap_version) ?>"><?php echo Text::_($warning_message); ?></div>');
    <?php else : ?>

		var lne_<?php echo $suffix ?>_carousel = $("<?php echo $css_prefix ?> ul.latestnews-items").carouFredSel({
        	<?php if ($horizontal) : ?>
        		responsive: true,
        	<?php endif; ?>
        	direction: "<?php echo $direction ?>",
        	<?php if ($horizontal) : ?>
				height: "variable",
				width: "100%",
			<?php else : ?>
				height: "variable",
				width: "100%",
			<?php endif; ?>

        	<?php if ($arrows) : ?>
				prev: "#prev_<?php echo $suffix ?>",
				next: "#next_<?php echo $suffix ?>",
			<?php endif; ?>

			<?php if ($pages || $symbols) : ?>
				pagination: {
					container: "#pager_<?php echo $suffix ?>",
					anchorBuilder: function(nr, item) {
					    return '<li<?php echo $item_class_attribute; ?>><a href="#'+nr+'"<?php echo $link_class_attribute; ?>><span>' + nr + '</span></a></li>';
					}
				},
			<?php endif; ?>

        	items: {
        		<?php if ($horizontal) : ?>
        			width: <?php echo $item_width ?>,
        			height: "variable",
	        		visible: {
						min: 1,
						max: <?php echo $visibleatonce ?>
					}
				<?php else : ?>
					height: "variable",
		        	visible: <?php echo $visibleatonce ?>,
        			minimum: 1
				<?php endif; ?>
    		},

    		scroll: {
    			<?php if (intval($moveatonce) > 1) : ?>
	    			items: {
	    				visible: {
	    					min: 1,
	    					max: <?php echo $moveatonce ?>
	    				}
	    			},
    			<?php else : ?>
					items: <?php echo $moveatonce ?>,
				<?php endif; ?>
				fx: "scroll",
            	duration: <?php echo $speed ?>,
            	pauseOnHover: true
        	},

    		auto: {
    			<?php if (!$auto) : ?>
    				play: false,
    			<?php endif; ?>
    			timeoutDuration: <?php echo $interval ?>
    		},

    		<?php if (!$restart_on_refresh) : ?>
    			cookie: "<?php echo $css_prefix ?>",
    		<?php endif; ?>

    		swipe: {
    			onTouch: true,
    			onMouse: true
    		},

  			onCreate: function (data) {
          		$("<?php echo $css_prefix ?>").show();
    			setTimeout(function() {
    				$("<?php echo $css_prefix ?>_loader").remove();
    				$("<?php echo $css_prefix ?> ul.latestnews-items").trigger('updateSizes').fadeTo('slow', 1);
    				$("<?php echo $css_prefix ?> .items_pagination").fadeTo('slow', 1);
    			}, 300);
  			}

		}, {
			wrapper: {
				classname: "lnee_carousel_wrapper"
			},

    		classnames: {
    			selected: "active"
    		}
    	});

    	<?php if ($auto) : ?>
            $("body").on("modalopen", function () { lne_<?php echo $suffix ?>_carousel.trigger("stop"); });
            $("body").on("modalclose", function () { lne_<?php echo $suffix ?>_carousel.trigger("play", true); });
        <?php endif; ?>

	<?php endif; ?>

	});
})(jQuery);
