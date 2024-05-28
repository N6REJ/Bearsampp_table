<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

// Explicitly declare the type of content
//header("Content-type: text/css; charset=UTF-8");
?>

<?php echo $suffix; ?> ul.latestnews-items li.latestnews-item.active {
	opacity: 0.5;
}

<?php if ($item_width_unit == '%' && $item_width > 50) : ?>
    <?php echo $suffix; ?> ul.latestnews-items li.full {
    	margin-right: <?php echo ((100 - $item_width) / 2); ?>%;
    	margin-left: <?php echo ((100 - $item_width) / 2); ?>%;
    }
<?php endif; ?>

<?php echo $suffix; ?>.horizontal ul.latestnews-items li.full {
	float: left;
}

<?php echo $suffix; ?> ul.latestnews-items li.downgraded {
	border-top: 1px solid #f3f3f3;
	padding-top: 5px;
	margin-top: 5px;

	width: <?php echo $downgraded_item_width; ?><?php echo $downgraded_item_width_unit; ?>;
}

		<?php echo $suffix; ?> .head_left .newshead {
			float: left;
			margin: 0 8px 0 0;
		}

		<?php echo $suffix; ?> .head_right .newshead {
			float: right;
			margin: 0 0 0 8px;
		}

			<?php if ($head_align) : ?>
				<?php echo $suffix; ?> .text_top .newshead > div,
				<?php echo $suffix; ?> .text_bottom .newshead > div,
				<?php echo $suffix; ?> .text_top .newshead > a,
				<?php echo $suffix; ?> .text_bottom .newshead > a {
					<?php if ($head_align == 'left') : ?>
						margin: 0 auto 0 0;
					<?php elseif ($head_align == 'right') : ?>
						margin: 0 0 0 auto;
					<?php elseif ($head_align == 'center') : ?>
						margin: 0 auto;
					<?php endif; ?>
				}
			<?php endif; ?>

		<?php echo $suffix; ?> .head_left .newsinfooverhead,
		<?php echo $suffix; ?> .head_right .newsinfooverhead,
		<?php echo $suffix; ?> .text_top .newsinfooverhead {
			display: none;
		}

		<?php if (!$wrap) : ?>
			<?php echo $suffix; ?> .newsinfo {
				overflow: hidden;
			}

			<?php echo $suffix; ?> .head_left .newsinfo.noimagespace {
				margin-left: 0 !important;
			}

			<?php echo $suffix; ?> .head_right .newsinfo.noimagespace {
				margin-right: 0 !important;
			}
		<?php endif; ?>

<?php if ($image) : ?>

	<?php echo $suffix; ?> .newshead.picturetype {
		position: relative;
		max-width: 100%;
	}

	<?php if ($pic_border_width > 0 || $pic_border_radius > 0) : ?>
		<?php echo $suffix; ?> .newshead .picture,
		<?php echo $suffix; ?> .newshead .nopicture {
			<?php if ($pic_border_width > 0) : ?>
    			border: <?php echo $pic_border_width ?>px solid <?php echo $pic_border_color ?>;

    			-webkit-box-sizing: border-box;
    			-moz-box-sizing: border-box;
    			box-sizing: border-box;
			<?php endif; ?>

    		<?php if ($pic_border_radius > 0) : ?>
    			border-radius: <?php echo $pic_border_radius; ?>px;
    			-moz-border-radius: <?php echo $pic_border_radius; ?>px;
    			-webkit-border-radius: <?php echo $pic_border_radius; ?>px;
    		<?php endif; ?>
		}
	<?php endif; ?>

	<?php echo $suffix; ?> .newshead .picture img {
		display: inherit;
	}

	<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type === 's') : ?>
		<?php echo $suffix; ?> .shadow.simple .picturetype {
			padding: <?php echo (intval($pic_shadow_width) + 2) ?>px;

			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}

		<?php echo $suffix; ?> .shadow.simple .picture,
		<?php echo $suffix; ?> .shadow.simple .nopicture {
			-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		}
	<?php endif; ?>

	<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type !== 's') : ?>

		<?php echo $suffix; ?> .shadow.vshapeleft .picturetype,
		<?php echo $suffix; ?> .shadow.vshaperight .picturetype {
			margin-bottom: <?php echo $pic_shadow_width; ?>px;
		}

		<?php echo $suffix; ?> .shadow.vshapeleft .picturetype:before,
		<?php echo $suffix; ?> .shadow.vshaperight .picturetype:after {

		    content: "";

		    position: absolute;
		    top: 80%;
		    left: 20px;
		    bottom: <?php echo $pic_shadow_width; ?>px;
		    width: 50%;

		    -webkit-transform: rotate(-3deg);
			-moz-transform: rotate(-3deg);
			-o-transform: rotate(-3deg);
			-ms-transform: rotate(-3deg);
			transform: rotate(-3deg);

		    background-color: transparent;
			-webkit-box-shadow: 0 <?php echo $pic_shadow_width; ?>px 10px #777;
			-moz-box-shadow: 0 <?php echo $pic_shadow_width; ?>px 10px #777;
			box-shadow: 0 <?php echo $pic_shadow_width; ?>px 10px #777;
		}

		<?php echo $suffix; ?> .shadow.vshaperight .picturetype:after {
			left: auto;
			right: 20px;

		    -webkit-transform: rotate(3deg);
			-moz-transform: rotate(3deg);
			-o-transform: rotate(3deg);
			-ms-transform: rotate(3deg);
			transform: rotate(3deg);
		}

		<?php echo $suffix; ?> .shadow.vshapeleft .picture,
		<?php echo $suffix; ?> .shadow.vshaperight .picture,
		<?php echo $suffix; ?> .shadow.vshapeleft .nopicture,
		<?php echo $suffix; ?> .shadow.vshaperight .nopicture {
			z-index: 1;
			position: relative;
		}
	<?php endif; ?>
<?php endif; ?>
