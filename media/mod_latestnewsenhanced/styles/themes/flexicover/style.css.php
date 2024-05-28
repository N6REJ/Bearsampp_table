<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Explicitly declare the type of content
//header("Content-type: text/css; charset=UTF-8");

if (!$image) {
	Factory::getApplication()->enqueueMessage(Text::_('MOD_LATESTNEWSENHANCEDEXTENDED_WARNING_NEEDSIMAGETYPE'), 'warning');
}
?>

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
	width: <?php echo $downgraded_item_width; ?><?php echo $downgraded_item_width_unit; ?>;
}

	<?php echo $suffix; ?> .downgraded .news {
		width: inherit;
	}

		<?php echo $suffix; ?> .innernews {
			position: relative;

			<?php if ($item_width_unit == 'px') : ?>
				<?php if ($shadow_body == 's') : ?>
					max-width: <?php echo ((int)$item_width - 6 * 2) ?>px;
				<?php elseif ($shadow_body == 'm') : ?>
					max-width: <?php echo ((int)$item_width - 11 * 2) ?>px;
				<?php elseif ($shadow_body == 'l') : ?>
					max-width: <?php echo ((int)$item_width - 27 * 2) ?>px;
				<?php elseif ($shadow_body == 'ss') : ?>
					max-width: <?php echo ((int)$item_width - 5 * 2) ?>px;
				<?php elseif ($shadow_body == 'sm') : ?>
					max-width: <?php echo ((int)$item_width - 11 * 2) ?>px;
				<?php elseif ($shadow_body == 'sl') : ?>
					max-width: <?php echo ((int)$item_width - 16 * 2) ?>px;
				<?php elseif (intval($pic_shadow_width) > 0) : ?>
					margin: <?php echo (intval($pic_shadow_width) + 2) ?>px;
					max-width: <?php echo ((int)$item_width - ((int)$pic_shadow_width + 2) * 2) ?>px;
					-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php else : ?>
					max-width: <?php echo $item_width ?>px;
				<?php endif; ?>
			<?php else : ?>
				<?php if (intval($pic_shadow_width) > 0) : ?>
					margin: <?php echo (intval($pic_shadow_width) + 2) ?>px;
					-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php endif; ?>
			<?php endif; ?>
		}

		<?php echo $suffix; ?> .downgraded .innernews {
			border: none;
		}

		<?php echo $suffix; ?> .active .innernews {
			opacity: 0.5;
		}

		<?php echo $suffix; ?> .innernews > .catlink {
			display: none;
		}

			<?php echo $suffix; ?> .newshead.picturetype {
				position: absolute;
				top: 0;
				left: 0;
				overflow: hidden;
				width: 100%;
				height: 100%;
			}

				<?php echo $suffix; ?> .newshead .picture,
				<?php echo $suffix; ?> .newshead .nopicture {
					width: 100%;
					height: 100%;
					max-width: none;
					max-height: none;
					overflow: hidden;

					<?php if ($pic_border_width > 0) : ?>
	    				border: <?php echo $pic_border_width ?>px solid <?php echo $pic_border_color ?>;

	    				-webkit-box-sizing: border-box;
	    				-moz-box-sizing: border-box;
	    				box-sizing: border-box;
					<?php endif; ?>

	           		<?php if ($pic_border_radius > 0) : ?>
	           			-webkit-border-radius: <?php echo $pic_border_radius; ?>px;
	           			-moz-border-radius: <?php echo $pic_border_radius; ?>px;
	           			border-radius: <?php echo $pic_border_radius; ?>px;
	           		<?php endif; ?>
				}

				<?php echo $suffix; ?> .newshead .nopicture {
					background-color: transparent; /* always show the content bg color */
				}

					<?php echo $suffix; ?> .newshead .picture img {

						-o-object-fit: cover;
					    object-fit: cover;

					    -o-object-position: center;
					    object-position: center;

					    position: absolute;
					    top: 0;
					    left: 0;
					    height: 100%;
					    width: 100%;
					}

			<?php echo $suffix; ?> .newsinfooverhead {
				display: none;
			}

			<?php echo $suffix; ?> .newsinfo {
				padding: 20px;
			}

			<?php echo $suffix; ?> .newshead + .newsinfo {
				position: relative;
				top: 0;
				right: 0;
				width: 100%;
				height: 100%;

				<?php if ($maintain_height) : ?>
					min-height: <?php echo $head_height ?>px;
				<?php endif; ?>

				color: #fff;

				pointer-events: none;

				display: -webkit-box;
    			display: -ms-flexbox;
				display: flex;

				-webkit-box-orient: vertical;
				-webkit-box-direction: normal;
				-ms-flex-direction: column;
				flex-direction: column;

				-webkit-box-pack: end;
				-ms-flex-pack: end;
				justify-content: flex-end;

				background: -webkit-gradient(linear, left bottom, left top, from(rgba(0, 0, 0, 0.4)), to(transparent));
    			background: -o-linear-gradient(bottom, rgba(0, 0, 0, 0.4), transparent);
				background: linear-gradient(to top, rgba(0, 0, 0, 0.4), transparent);

           		<?php if ($pic_border_radius > 0) : ?>
           			-webkit-border-radius: <?php echo $pic_border_radius; ?>px;
           			-moz-border-radius: <?php echo $pic_border_radius; ?>px;
           			border-radius: <?php echo $pic_border_radius; ?>px;
           		<?php endif; ?>
			}

			<?php echo $suffix; ?> .text_top .newshead + .newsinfo {
				-webkit-box-pack: start;
				-ms-flex-pack: start;
				justify-content: flex-start;

				background: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0.4)), to(transparent));
    			background: -o-linear-gradient(top, rgba(0, 0, 0, 0.4), transparent);
				background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), transparent);
			}

			<?php echo $suffix; ?> .newsinfo .newstitle {
				padding: 10px 0;
			}

			<?php echo $suffix; ?> .newsinfo .newsintro {
				padding-top: 10px;
			}

			<?php echo $suffix; ?> .newsinfo .item_details.after_text {
				padding-top: 10px;
			}

			<?php echo $suffix; ?> .newsinfo p.link {
				padding-top: 10px;
				margin-top: 0;
			}

			<?php echo $suffix; ?> .newsinfo p.link + .catlink {
				padding-top: 10px;
			}

			<?php echo $suffix; ?> .newsinfo a {
				pointer-events: auto;
			}
