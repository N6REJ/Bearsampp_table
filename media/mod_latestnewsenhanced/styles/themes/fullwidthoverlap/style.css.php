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

// limitations:

// category 'over head' or 'first' or 'last' are not visible
// needs an image
// downgrade limitation: if border needed and showing image, need to add it manually

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

				<?php if ($shadow_body == 's') : ?>
					max-width: <?php echo ((int)$head_width - 6 * 2) ?>px;
				<?php elseif ($shadow_body == 'm') : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2) ?>px;
				<?php elseif ($shadow_body == 'l') : ?>
					max-width: <?php echo ((int)$head_width - 27 * 2) ?>px;
				<?php elseif ($shadow_body == 'ss') : ?>
					max-width: <?php echo ((int)$head_width - 5 * 2) ?>px;
				<?php elseif ($shadow_body == 'sm') : ?>
					max-width: <?php echo ((int)$head_width - 11 * 2) ?>px;
				<?php elseif ($shadow_body == 'sl') : ?>
					max-width: <?php echo ((int)$head_width - 16 * 2) ?>px;
				<?php elseif (intval($pic_shadow_width) > 0) : ?>
					margin: <?php echo (intval($pic_shadow_width) + 2) ?>px;
					max-width: <?php echo ((int)$head_width - ((int)$pic_shadow_width + 2) * 2) ?>px;
					-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php else : ?>
					max-width: <?php echo $head_width ?>px;
				<?php endif; ?>

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
					position: relative;
					top: 0;
					left: 0;
					overflow: hidden;
					width: 100%;
					<?php if ($maintain_height) : ?>
						height: <?php echo $head_height ?>px;
					<?php endif; ?>
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
					overflow: hidden;
				}

				<?php if ($maintain_height) : ?>
					<?php echo $suffix; ?> .newshead .picture .innerpicture {
						-ms-flex-negative: 0;
						flex-shrink: 0;
					}

					<?php echo $suffix; ?> .newshead .picture img {
						max-width: none !important;
					}
				<?php endif; ?>

				<?php echo $suffix; ?> .newsinfooverhead {
					display: none;
				}

				<?php echo $suffix; ?> .newsinfo {
					padding: 8px;
				}

				<?php echo $suffix; ?> .newshead + .newsinfo {
					position: absolute;

					background-color: rgb(0, 0, 0); /* fallback color */
					background-color: rgba(0, 0, 0, 0.2);

					color: #fff;
				}

				<?php echo $suffix; ?> .newshead + .newsinfo {
					overflow: hidden;
				}

				<?php echo $suffix; ?> .head_left .newshead + .newsinfo {
					top: 0;
					right: 0;
					width: 50%;
					height: 100%;
					overflow-y: auto;
				}

				<?php echo $suffix; ?> .head_right .newshead + .newsinfo {
					top: 0;
					left: 0;
					width: 50%;
					height: 100%;
					overflow-y: auto;
				}

				<?php echo $suffix; ?> .text_top .newshead + .newsinfo {
					top: 0;
					left: 0;
					width: 100%;
					max-height: 100%;
					overflow-y: auto;
				}

				<?php echo $suffix; ?> .text_bottom .newshead + .newsinfo {
					bottom: 0;
					left: 0;
					width: 100%;
					max-height: 100%;
					overflow-y: auto;
				}

				<?php echo $suffix; ?> .downgraded .head_right .newstitle,
				<?php echo $suffix; ?> .downgraded .head_right .newsextra,
				<?php echo $suffix; ?> .downgraded .head_right .newsintro {
					text-align: left;
				}
