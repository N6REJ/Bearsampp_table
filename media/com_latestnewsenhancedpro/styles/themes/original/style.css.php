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
	<?php echo $suffix; ?> .text_top .innernews,
	<?php echo $suffix; ?> .text_bottom .innernews {
		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;

		-webkit-box-align: start;
		-webkit-align-items: flex-start;
		-moz-box-align: start;
		-ms-flex-align: start;
		align-items: flex-start;
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

	<?php echo $suffix; ?> .text_top .newsinfo,
	<?php echo $suffix; ?> .text_bottom .newsinfo {
		width: 100%;
	}

		<?php if ($wrap) : ?>

			<?php echo $suffix; ?> .head_left .newshead {
				float: left;
			}

			<?php echo $suffix; ?> .head_right .newshead {
				float: right;
			}

			<?php if (!$leading_wrap) : ?>

				<?php echo $suffix; ?> .leading.head_left .innernews,
				<?php echo $suffix; ?> .leading.head_right .innernews {
					display: -webkit-box;
					display: -moz-box;
					display: -webkit-flex;
					display: -ms-flexbox;
					display: flex;

					-webkit-box-align: start;
					-webkit-align-items: flex-start;
					-moz-box-align: start;
					-ms-flex-align: start;
					align-items: flex-start;

					-webkit-flex-wrap: wrap;
					-ms-flex-wrap: wrap;
					flex-wrap: wrap;
				}

				<?php echo $suffix; ?> .leading.head_left .innernews {
					-webkit-flex-direction: row;
					-ms-flex-direction: row;
					flex-direction: row;
				}

				<?php echo $suffix; ?> .leading.head_right .innernews {
					-webkit-box-orient: horizontal;
					-webkit-box-direction: reverse;
					-webkit-flex-direction: row-reverse;
					-ms-flex-direction: row-reverse;
					flex-direction: row-reverse;
				}

				<?php echo $suffix; ?> .leading.head_left .newshead,
				<?php echo $suffix; ?> .leading.head_right .newshead {
					float: none;
				}

				<?php echo $suffix; ?> .leading .newshead {
					-webkit-box-flex: none;
					-webkit-flex: none;
					-ms-flex: none;
					flex: none;
				}

				<?php echo $suffix; ?> .leading .newsinfo {
					-webkit-box-flex: 1;
					-webkit-flex: 1;
					-ms-flex: 1;
					flex: 1;
				}

			<?php endif; ?>

		<?php else : ?>

			<?php echo $suffix; ?> .head_left .innernews,
			<?php echo $suffix; ?> .head_right .innernews {
				display: -webkit-box;
				display: -moz-box;
				display: -webkit-flex;
				display: -ms-flexbox;
				display: flex;

				-webkit-box-align: start;
				-webkit-align-items: flex-start;
				-moz-box-align: start;
				-ms-flex-align: start;
				align-items: flex-start;

				-webkit-flex-wrap: wrap;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
			}

			<?php echo $suffix; ?> .head_left .innernews {
				-webkit-flex-direction: row;
				-ms-flex-direction: row;
				flex-direction: row;
			}

			<?php echo $suffix; ?> .head_right .innernews {
				-webkit-box-orient: horizontal;
				-webkit-box-direction: reverse;
				-webkit-flex-direction: row-reverse;
				-ms-flex-direction: row-reverse;
				flex-direction: row-reverse;
			}

			<?php echo $suffix; ?> .newshead {
				-webkit-box-flex: none;
				-webkit-flex: none;
				-ms-flex: none;
				flex: none;
			}

			<?php echo $suffix; ?> .newsinfo {
				-webkit-box-flex: 1;
				-webkit-flex: 1;
				-ms-flex: 1;
				flex: 1;
			}

			<?php if ($leading_wrap) : ?>

				<?php echo $suffix; ?> .leading.head_left .innernews,
				<?php echo $suffix; ?> .leading.head_right .innernews {
					display: inherit;
				}

				<?php echo $suffix; ?> .leading.head_left .newshead {
					float: left;
				}

				<?php echo $suffix; ?> .leading.head_right .newshead {
					float: right;
				}

			<?php endif; ?>

		<?php endif; ?>

		<?php echo $suffix; ?> .head_left .newshead {
			margin-right: 8px;
		}

		<?php echo $suffix; ?> .head_right .newshead {
			margin-left: 8px;
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

<?php if ($leading_image || $image) : ?>

	<?php echo $suffix; ?> .newshead.picturetype {
		position: relative;
		max-width: 100%;
	}

		<?php if ($image) : ?>
			<?php echo $suffix; ?> .newshead .picture {
				<?php if ($head_width > 0) : ?>
					width: 100%;
				<?php else : ?>
					width: max-content;
				<?php endif; ?>
				max-width: 100%;
				height: 100%;
			}
		<?php endif; ?>

		<?php if ($leading_image) : ?>
			<?php echo $suffix; ?> .leading .newshead .picture {
				<?php if ($leading_head_width > 0) : ?>
					width: 100%;
				<?php else : ?>
					width: max-content;
				<?php endif; ?>
				max-width: 100%;
				height: 100%;
			}
		<?php endif; ?>

		<?php echo $suffix; ?> .newshead .nopicture {
			width: 100%;
			height: 100%;
		}

			<?php echo $suffix; ?> .newshead .nopicture > a span,
			<?php echo $suffix; ?> .newshead .nopicture > span {
				display: inline-block;
				width: 100%;
				height: 100%;
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
