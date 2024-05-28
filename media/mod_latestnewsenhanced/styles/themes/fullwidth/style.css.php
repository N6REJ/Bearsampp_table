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

		<?php echo $suffix; ?> .innernews {
			position: relative;
		}

		<?php echo $suffix; ?> .text_top .innernews,
		<?php echo $suffix; ?> .text_bottom .innernews {
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: flex;

			-webkit-box-align: start;
			-webkit-align-items: flex-start;
			-moz-box-align: start;
			-ms-flex-align: start;
			align-items: flex-start;
		}

		<?php echo $suffix; ?> .text_top .innernews {
			-webkit-flex-direction: column-reverse;
			-ms-flex-direction: column-reverse;
			flex-direction: column-reverse;
		}

		<?php echo $suffix; ?> .text_bottom .innernews {
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-webkit-flex-direction: column;
			-ms-flex-direction: column;
			flex-direction: column;
		}

		<?php echo $suffix; ?> .innernews > .catlink {
			margin-left: auto;
			margin-right: auto;
			<?php if ($image || $video) : ?>
	    		max-width: <?php echo $head_width ?>px;
			<?php endif; ?>
		}

			<?php echo $suffix; ?> .head_left .newshead,
			<?php echo $suffix; ?> .head_right .newshead {
				position: relative;
			}

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
					<?php else : ?>
						margin: 0 auto;
					<?php endif; ?>
				}

			<?php echo $suffix; ?> .newsinfo,
			<?php echo $suffix; ?> .newsinfooverhead {
				margin: 0 auto;
			    position: relative;
			    width: 100%;
			    <?php if ($image || $video) : ?>
			    	max-width: <?php echo $head_width ?>px;
			    <?php endif; ?>
			}

			<?php echo $suffix; ?> .newsinfo {
				overflow: hidden;
			}

<?php if ($image) : ?>

	<?php echo $suffix; ?> .newshead.picturetype {
		margin-bottom: 5px;
	    position: relative;
		width: 100%;
	}

	<?php if ($maintain_height) : ?>
		<?php echo $suffix; ?> .newshead .picture {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;

			-webkit-box-pack: center;
			-ms-flex-pack: center;
			justify-content: center;

			-webkit-box-align: center;
			-ms-flex-align: center;
			align-items: center;
		}
	<?php endif; ?>

	<?php echo $suffix; ?> .newshead .picture,
	<?php echo $suffix; ?> .newshead .nopicture {
		width: 100%;
		height: 100%;

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

	<?php if ($maintain_height) : ?>

		<?php echo $suffix; ?> .newshead .nopicture > span,
		<?php echo $suffix; ?> .newshead .nopicture > a span {
			<?php if ($shadow_body == 's') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 6 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 6 * 2) ?>px;
				<?php endif; ?>
			<?php elseif ($shadow_body == 'm') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2) ?>px;
				<?php endif; ?>
			<?php elseif ($shadow_body == 'l') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 27 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 27 * 2) ?>px;
				<?php endif; ?>
			<?php elseif ($shadow_body == 'ss') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 5 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 5 * 2) ?>px;
				<?php endif; ?>
			<?php elseif ($shadow_body == 'sm') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2) ?>px;
				<?php endif; ?>
			<?php elseif ($shadow_body == 'sl') : ?>
				<?php if (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
					max-width: <?php echo ((int)$head_width - 16 * 2 - (intval($pic_shadow_width) + 2) * 2) ?>px;
				<?php else : ?>
					max-width: <?php echo ((int)$head_width - 16 * 2) ?>px;
				<?php endif; ?>
			<?php elseif (intval($pic_shadow_width) > 0 && $pic_shadow_type == 's') : ?>
				max-width: <?php echo ((int)$head_width - (intval($pic_shadow_width) + 2) * 2) ?>px;
			<?php else : ?>
				max-width: <?php echo $head_width ?>px;
			<?php endif; ?>
		}

		<?php echo $suffix; ?> .newshead .picture .innerpicture {
			-ms-flex-negative: 0;
			flex-shrink: 0;
		}
	<?php endif; ?>

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

<?php if ($calendar) : ?>

	<?php echo $suffix; ?> .newshead.calendartype {
		margin-bottom: 5px;
		position: relative;
		width: 100%;
	}

		<?php echo $suffix; ?> .newshead > div {
			width: auto !important;
		}

<?php endif; ?>

<?php if ($icon) : ?>

	<?php echo $suffix; ?> .newshead.icontype {
		margin-bottom: 5px;
		position: relative;
		width: 100%;
	}

<?php endif; ?>

<?php if ($video) : ?>

	<?php echo $suffix; ?> .newshead.videotype {
		margin-bottom: 5px;
		position: relative;
		width: 100%;
	}

<?php endif; ?>
