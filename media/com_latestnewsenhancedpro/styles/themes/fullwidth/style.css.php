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

	<?php if ($leading_image || $leading_video) : ?>
		<?php echo $suffix; ?> .leading .innernews > .catlink {
			max-width: <?php echo $leading_head_width ?>px;
		}
	<?php elseif ($image || $video) : ?>
		<?php echo $suffix; ?> .leading .innernews > .catlink {
			max-width: inherit;
		}
	<?php endif; ?>

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
					margin: 0 auto;
				<?php endif; ?>
			}

		<?php echo $suffix; ?> .newsinfo {
			margin: 0 auto;
		    position: relative;
		    width: 100%;
		    overflow: hidden;
		    <?php if ($image || $video) : ?>
		    	max-width: <?php echo $head_width ?>px;
		    <?php endif; ?>
		}

		<?php if ($leading_image || $leading_video) : ?>
			<?php echo $suffix; ?> .leading .newsinfo {
				max-width: <?php echo $leading_head_width ?>px;
			}
		<?php elseif ($image || $video) : ?>
			<?php echo $suffix; ?> .leading .newsinfo {
				max-width: inherit;
			}
		<?php endif; ?>

<?php if ($leading_image || $image) : ?>

	<?php echo $suffix; ?> .newshead.picturetype,
	<?php echo $suffix; ?> .leading .newshead.picturetype {
		margin-bottom: 5px;
	    position: relative;
		width: 100%;
	}

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

		<?php echo $suffix; ?> .newshead .picture .innerpicture {
			-ms-flex-negative: 0;
			flex-shrink: 0;
		}

	<?php if (intval($pic_shadow_width) > 0) : ?>
		<?php echo $suffix; ?> .picturetype {
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

<?php if ($leading_calendar || $calendar) : ?>

	<?php echo $suffix; ?> .newshead.calendartype {
	    margin-bottom: 5px;
		position: relative;
		width: 100%;
	}

		<?php echo $suffix; ?> .newshead .calendar,
		<?php echo $suffix; ?> .newshead .nocalendar,
		<?php echo $suffix; ?> .leading .newshead .calendar,
		<?php echo $suffix; ?> .leading .newshead .nocalendar {
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

<?php if ($leading_video || $video) : ?>

	<?php echo $suffix; ?> .newshead.videotype {
		margin-bottom: 5px;
		position: relative;
		width: 100%;
	}

<?php endif; ?>
