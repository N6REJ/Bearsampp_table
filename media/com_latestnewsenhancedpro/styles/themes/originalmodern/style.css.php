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

	<?php echo $suffix; ?> .innernews {
		overflow: hidden;

		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;

		-webkit-flex-direction: row;
		-ms-flex-direction: row;
		flex-direction: row;

		-webkit-align-self: stretch;
		-ms-flex-item-align: stretch;
		align-items: stretch;
	}

	<?php echo $suffix; ?> .head_right .innernews {
		-webkit-box-orient: horizontal;
		-webkit-box-direction: reverse;
		-webkit-flex-direction: row-reverse;
		-ms-flex-direction: row-reverse;
		flex-direction: row-reverse;
	}

	<?php echo $suffix; ?> .head_left .innernews,
	<?php echo $suffix; ?> .head_right .innernews {
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
    	flex-wrap: wrap;
	}

	<?php echo $suffix; ?> .text_bottom .innernews {
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
	}

	<?php echo $suffix; ?> .text_top .innernews {
		-webkit-flex-direction: column-reverse;
		-ms-flex-direction: column-reverse;
		flex-direction: column-reverse;
	}

		<?php echo $suffix; ?> .newshead {
			-webkit-box-flex: none;
			-moz-box-flex: none;
			-webkit-flex: none;
			-ms-flex: none;
			flex: none;
		}

		<?php echo $suffix; ?> .text_top .newshead,
		<?php echo $suffix; ?> .text_bottom .newshead {
			width: 100%;
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
					margin: 0;
				<?php endif; ?>
			}

			<?php echo $suffix; ?> .leading.text_top .newshead > div,
			<?php echo $suffix; ?> .leading.text_bottom .newshead > div,
			<?php echo $suffix; ?> .leading.text_top .newshead > a,
			<?php echo $suffix; ?> .leading.text_bottom .newshead > a {
				<?php if ($leading_head_align == 'left') : ?>
					margin: 0 auto 0 0;
				<?php elseif ($leading_head_align == 'right') : ?>
					margin: 0 0 0 auto;
				<?php elseif ($leading_head_align == 'center') : ?>
					margin: 0 auto;
				<?php else : ?>
					margin: 0;
				<?php endif; ?>
			}

		<?php echo $suffix; ?> .newsinfooverhead {
			display: none;
		}

		<?php echo $suffix; ?> .newsinfo {
			-webkit-box-flex: 1 1 auto;
			-webkit-flex: 1 1 auto;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;

			padding: 10px;
			text-align: initial;

			display: -webkit-box;
			display: -moz-box;
			display: -webkit-flex;
			display: -ms-flexbox;
			display: flex;

			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-webkit-flex-direction: column;
			-ms-flex-direction: column;
			flex-direction: column;

			<?php if ($force_title_one_line) : ?>
				min-width: 0; /* to fix ellipsis */
			<?php endif; ?>
		}

		/* for flexbox to work in IE11 */
		<?php echo $suffix; ?> .head_right .newsinfo,
		<?php echo $suffix; ?> .head_left .newsinfo {
			-webkit-box-flex: 1 1 0px;
			-webkit-flex: 1 1 0px;
			-ms-flex: 1 1 0px;
			flex: 1 1 0px;
		}

			<?php echo $suffix; ?> .newstitle {
				padding: 0 0 10px 0;

				-webkit-box-flex: none;
				-webkit-flex: none;
				-ms-flex: none;
				flex: none;
			}

			<?php echo $suffix; ?> .item_details + .newstitle + .newsintro,
			<?php echo $suffix; ?> .item_details + .newsintro {
				-webkit-box-flex: 1 1 auto;
				-webkit-flex: 1 1 auto;
				-ms-flex: 1 1 auto;
				flex: 1 1 auto;
			}

			<?php echo $suffix; ?> .newsintro {
				padding: 0 0 10px 0;

				-webkit-box-flex: none;
				-webkit-flex: none;
				-ms-flex: none;
				flex: none;
			}

			<?php echo $suffix; ?> .newsinfo .item_details {
				padding: 0 0 10px 0;

				-webkit-box-flex: none;
				-webkit-flex: none;
				-ms-flex: none;
				flex: none;
			}

			<?php echo $suffix; ?> .newsinfo .item_details.after_text {
				-webkit-box-flex: 1 1 auto;
				-webkit-flex: 1 1 auto;
				-ms-flex: 1 1 auto;
				flex: 1 1 auto;
			}

			<?php echo $suffix; ?> p.link,
			<?php echo $suffix; ?> .catlink,
			<?php echo $suffix; ?> .head_right p.link.right,
			<?php echo $suffix; ?> .head_right .catlink.right,
			html[dir="rtl"] <?php echo $suffix; ?> .head_right p.link,
			html[dir="rtl"] <?php echo $suffix; ?> .head_right .catlink,
			html[dir="rtl"] <?php echo $suffix; ?> p.link.left,
			html[dir="rtl"] <?php echo $suffix; ?> .catlink.left {
				-webkit-box-flex: none;
				-moz-box-flex: none;
				-webkit-flex: none;
				-ms-flex: none;
				flex: none;

				-ms-flex-item-align: end;
				align-self: flex-end;

				margin: 0;
			}

			<?php echo $suffix; ?> p.link.left,
			<?php echo $suffix; ?> .catlink.left,
			<?php echo $suffix; ?> .head_right p.link,
			<?php echo $suffix; ?> .head_right .catlink,
			html[dir="rtl"] <?php echo $suffix; ?> p.link.right,
			html[dir="rtl"] <?php echo $suffix; ?> .catlink.right {
				-ms-flex-item-align: start;
				align-self: flex-start;
			}

			<?php echo $suffix; ?> p.link.center,
			<?php echo $suffix; ?> .catlink.center {
				-ms-flex-item-align: center;
				-ms-grid-row-align: center;
				align-self: center;
			}

			<?php echo $suffix; ?> p.link.justify,
			<?php echo $suffix; ?> .catlink.justify {
				-ms-flex-item-align: stretch;
				-ms-grid-row-align: stretch;
				align-self: stretch;
			}

			<?php echo $suffix; ?> p.link + .catlink {
				padding: 10px 0 0 0;
			}

			<?php echo $suffix; ?> .innernews > .catlink {
				display: none;
			}

<?php if ($leading_image || $image) : ?>

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

	<?php if (intval($pic_shadow_width) > 0) : ?>
		<?php echo $suffix; ?> .newshead.picturetype {
			padding: <?php echo (intval($pic_shadow_width) + 2) ?>px;

			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}

		<?php echo $suffix; ?> .newshead .picture,
		<?php echo $suffix; ?> .newshead .nopicture {
			-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		}
	<?php endif; ?>

<?php endif; ?>
