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

<?php if ($center_image) : ?>
	@media only screen and (max-width: <?php echo $breakpoint; ?>px) {
		.item-page .item-image {
			margin-left: auto !important;
		    margin-right: auto !important;
		    float: none !important;
		    text-align: center;
		}
	}
<?php endif; ?>

<?php if ($lists_center_image) : ?>
	@media only screen and (max-width: <?php echo $breakpoint; ?>px) {
		.items-leading .item-image,
		.items-row .item-image {
			margin-left: auto !important;
		    margin-right: auto !important;
		    float: none !important;
		    text-align: center;
		}
	}
<?php endif; ?>

.articledetails {
	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;

	-webkit-flex-direction: row;
	-ms-flex-direction: row;
	flex-direction: row;

	-webkit-flex-wrap: wrap;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;

	min-width: 100px; /* arbitrary - works better when working with surrounding image floats */
}

.articledetails-header {
	margin-bottom: 5px;

	<?php if ($vertical_align == 'center') : ?>
		-webkit-box-align: center;
		-webkit-align-items: center;
		-ms-flex-align: center;
		align-items: center;
	<?php elseif ($vertical_align == 'bottom'): ?>
		-webkit-box-align: end;
		-webkit-align-items: flex-end;
		-moz-box-align: end;
		-ms-flex-align: end;
		align-items: flex-end;
	<?php endif; ?>

	<?php if ($align_details == 'right') : ?>
		-webkit-box-orient: horizontal;
		-webkit-box-direction: reverse;
		-webkit-flex-direction: row-reverse;
		-ms-flex-direction: row-reverse;
		flex-direction: row-reverse;
	<?php endif; ?>
}

.articledetails-footer {
	margin-top: 15px;
	padding-top: 15px;
	border-top: 1px solid #eee;
	clear: both;

	<?php if ($footer_vertical_align == 'center') : ?>
		-webkit-box-align: center;
		-webkit-align-items: center;
		-ms-flex-align: center;
		align-items: center;
	<?php elseif ($footer_vertical_align == 'bottom'): ?>
		-webkit-box-align: end;
		-webkit-align-items: flex-end;
		-moz-box-align: end;
		-ms-flex-align: end;
		align-items: flex-end;
	<?php endif; ?>

	<?php if ($footer_align_details == 'right') : ?>
		-webkit-box-orient: horizontal;
		-webkit-box-direction: reverse;
		-webkit-flex-direction: row-reverse;
		-ms-flex-direction: row-reverse;
		flex-direction: row-reverse;
	<?php endif; ?>
}

	.articledetails .publishing_status {
		margin-bottom: 5px;
	}

	.articledetails .head {
		-webkit-box-flex: none;
  		-moz-box-flex: none;
		-webkit-flex: none;
		-ms-flex: none;
		flex: none;
	}

	.articledetails-header .head {
		<?php if ($align_details == 'left') : ?>
		   	margin: 0 8px 0 0;
		<?php endif; ?>
		<?php if ($align_details == 'right') : ?>
		   	margin: 0 0 0 8px;
		<?php endif; ?>
	}

	html[dir="rtl"] .articledetails-header .head {
		<?php if ($align_details == 'left') : ?>
		   	margin: 0 0 0 8px;
		<?php endif; ?>
		<?php if ($align_details == 'right') : ?>
		   	margin: 0 8px 0 0;
		<?php endif; ?>
	}

	.articledetails-footer .head {
		<?php if ($footer_align_details == 'left') : ?>
		   	margin: 0 8px 0 0;
		<?php endif; ?>
		<?php if ($footer_align_details == 'right') : ?>
		   	margin: 0 0 0 8px;
		<?php endif; ?>
	}

	html[dir="rtl"] .articledetails-footer .head {
		<?php if ($footer_align_details == 'left') : ?>
		   	margin: 0 0 0 8px;
		<?php endif; ?>
		<?php if ($footer_align_details == 'right') : ?>
		   	margin: 0 8px 0 0;
		<?php endif; ?>
	}

	<?php if ($image_header || $image_footer) : ?>

		.articledetails .head.imagetype {
			position: relative;
			max-width: 100%;

			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
	<?php endif; ?>

	<?php if ($image_header) : ?>

		<?php if ($pic_shadow_width > 0) : ?>
			.articledetails-header .head.imagetype {
				padding: <?php echo ($pic_shadow_width + 2) ?>px;
			}
		<?php endif; ?>

			.articledetails-header .head .picture,
			.articledetails-header .head .nopicture {
				width: <?php echo $head_width; ?>px;
				max-width: <?php echo $head_width; ?>px;
				height: <?php echo $head_height; ?>px;
				max-height: <?php echo $head_height; ?>px;
				background-color: <?php echo $pic_bgcolor; ?>;
				<?php if ($pic_border_width > 0) : ?>
					border: <?php echo $pic_border_width ?>px solid <?php echo $pic_border_color ?>;
				<?php endif; ?>
				<?php if ($pic_shadow_width > 0) : ?>
					box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-moz-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php endif; ?>
				<?php if ($pic_border_radius > 0) : ?>
					border-radius: <?php echo $pic_border_radius; ?>px;
					-moz-border-radius: <?php echo $pic_border_radius; ?>px;
					-webkit-border-radius: <?php echo $pic_border_radius; ?>px;
				<?php endif; ?>
			}

			<?php if ($pic_border_radius_img > 0) : ?>
				.articledetails-header .head .picture img {
					border-radius: <?php echo $pic_border_radius_img; ?>px;
					-moz-border-radius: <?php echo $pic_border_radius_img; ?>px;
					-webkit-border-radius: <?php echo $pic_border_radius_img; ?>px;
				}
			<?php endif; ?>

			.articledetails-header .head .nopicture span {
				width: <?php echo $head_width; ?>px;
				height: <?php echo $head_height; ?>px;
			}
	<?php endif; ?>

	<?php if ($image_footer) : ?>

		<?php if ($footer_pic_shadow_width > 0) : ?>
			.articledetails-footer .head.imagetype {
				padding: <?php echo ($footer_pic_shadow_width + 2) ?>px;
			}
		<?php endif; ?>

			.articledetails-footer .head .picture,
			.articledetails-footer .head .nopicture {
				width: <?php echo $footer_head_width; ?>px;
				max-width: <?php echo $footer_head_width; ?>px;
				height: <?php echo $footer_head_height; ?>px;
				max-height: <?php echo $footer_head_height; ?>px;
				background-color: <?php echo $footer_pic_bgcolor; ?>;
				<?php if ($footer_pic_border_width > 0) : ?>
					border: <?php echo $footer_pic_border_width ?>px solid <?php echo $footer_pic_border_color ?>;
				<?php endif; ?>
				<?php if ($footer_pic_shadow_width > 0) : ?>
					box-shadow: 0 0 <?php echo $footer_pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-moz-box-shadow: 0 0 <?php echo $footer_pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $footer_pic_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php endif; ?>
				<?php if ($footer_pic_border_radius > 0) : ?>
					border-radius: <?php echo $footer_pic_border_radius; ?>px;
					-moz-border-radius: <?php echo $footer_pic_border_radius; ?>px;
					-webkit-border-radius: <?php echo $footer_pic_border_radius; ?>px;
				<?php endif; ?>
			}

			<?php if ($footer_pic_border_radius_img > 0) : ?>
				.articledetails-footer .head .picture img {
					border-radius: <?php echo $footer_pic_border_radius_img; ?>px;
					-moz-border-radius: <?php echo $footer_pic_border_radius_img; ?>px;
					-webkit-border-radius: <?php echo $footer_pic_border_radius_img; ?>px;
				}
			<?php endif; ?>

			.articledetails-footer .head .nopicture span {
				width: <?php echo $footer_head_width; ?>px;
				height: <?php echo $footer_head_height; ?>px;
			}
	<?php endif; ?>

	<?php if ($calendar) : ?>

		/*.articledetails .head.calendartype {*/
			/*font-size: <?php echo $font_ref_cal; ?>px;*/ /* the base size for the calendar */
		/*}*/

			.articledetails .head .nocalendar {
				width: <?php echo $head_width; ?>px;
				max-width: <?php echo $head_width; ?>px;
				height: <?php echo $head_height; ?>px;
				min-height: <?php echo $head_height; ?>px;
			}

			.articledetails .head .calendar {
				/* set height in the element stylesheet */
				width: <?php echo $head_width; ?>px;
				max-width: <?php echo $head_width; ?>px;
			}

	<?php endif; ?>

	<?php if ($icon) : ?>

		.articledetails .head.icontype {
			display: -webkit-box;
			display: -moz-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;

			-webkit-box-align: stretch;
    		-webkit-align-items: stretch;
       		-moz-box-align: stretch;
       	 	-ms-flex-align: stretch;
			align-items: stretch;

			<?php if ($head_width > 0) : ?>
				width: <?php echo $head_width; ?>px;
			<?php endif; ?>
			<?php if ($head_height > 0) : ?>
				height: <?php echo $head_height; ?>px;
			<?php endif; ?>

			<?php if (intval($icon_shadow_width) > 0) : ?>
				padding: <?php echo (intval($icon_shadow_width) + 2) ?>px;
			<?php endif; ?>
		}

			.articledetails .head .icon,
			.articledetails .head .noicon {
				background-color: <?php echo $icon_bgcolor; ?>;
				padding: <?php echo $icon_padding; ?>px;
				width: 100%;
				height: 100%;

				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;

				display: -webkit-box;
				display: -moz-box;
				display: -ms-flexbox;
				display: -webkit-flex;
				display: flex;

				-webkit-box-pack:center;
				-webkit-justify-content:center;
				-ms-flex-pack:center;
				justify-content: center;

				-webkit-box-align: center;
				-webkit-align-items: center;
				-ms-flex-align: center;
				align-items: center;

				<?php if ($icon_border_width > 0) : ?>
					border: <?php echo $icon_border_width ?>px solid <?php echo $icon_border_color ?>;
				<?php endif; ?>

				<?php if ($icon_border_radius > 0) : ?>
					border-radius: <?php echo $icon_border_radius; ?>px;
					-moz-border-radius: <?php echo $icon_border_radius; ?>px;
					-webkit-border-radius: <?php echo $icon_border_radius; ?>px;
				<?php endif; ?>

				<?php if (intval($icon_shadow_width) > 0) : ?>
					box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-moz-box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
					-webkit-box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php endif; ?>
			}

			.articledetails .head .icon i {
				color: <?php echo $icon_color; ?>;

				<?php if ($icon_text_shadow_width > 0) : ?>
					text-shadow: 0 0 <?php echo $icon_text_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				<?php endif; ?>
			}

			.articledetails .head .noicon i {
				color: transparent;
			}

				.articledetails .head .icon i,
				.articledetails .head .noicon i {
					<?php if ($head_width >= $head_height) : ?>
						font-size: <?php echo ($head_height - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php else : ?>
						font-size: <?php echo ($head_width - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php endif; ?>
				}

		<?php endif; ?>

	/* 0px for flexbox to work in IE11 */
	.articledetails .info {
		-webkit-box-flex: 1 1 0px;
		-moz-box-flex: 1 1 0px;
		-webkit-flex: 1 1 0px;
		-ms-flex: 1 1 0px;
		flex: 1 1 0px;

		display: -webkit-box;
		display: -moz-box;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;

		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
	}

		.articledetails-header .info .head_details {
			display: -webkit-box;
			display: -moz-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;

			<?php if ($vertical_align == 'center') : ?>
				-webkit-box-align: center;
				-webkit-align-items: center;
				-ms-flex-align: center;
				align-items: center;
			<?php elseif ($vertical_align == 'bottom'): ?>
				-webkit-box-align: end;
    			-webkit-align-items: flex-end;
       			-moz-box-align: end;
        		-ms-flex-align: end;
				align-items: flex-end;
			<?php endif; ?>

			<?php if ($align_details == 'right') : ?>
				-webkit-box-orient: horizontal;
				-webkit-box-direction: reverse;
				-webkit-flex-direction: row-reverse;
				-ms-flex-direction: row-reverse;
				flex-direction: row-reverse;
			<?php else : ?>
				-webkit-flex-direction: row;
				-ms-flex-direction: row;
				flex-direction: row;
			<?php endif; ?>

			-webkit-flex-wrap: wrap;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
		}

	<?php if ($image_header && $pic_shadow_width > 0) : ?>
		.articledetails-header .info,
		.articledetails-header .info .head_details .item_details {
			padding-top: <?php echo $pic_shadow_width ?>px;
		}
	<?php endif; ?>

	<?php if ($calendar && $cal_shadow_width > 0) : ?>
		.articledetails-header .info,
		.articledetails-header .info .head_details .item_details {
			padding-top: <?php echo $cal_shadow_width ?>px;
		}
	<?php endif; ?>

	<?php if ($icon && $icon_shadow_width > 0) : ?>
		.articledetails-header .info,
		.articledetails-header .info .head_details .item_details {
			padding-top: <?php echo $icon_shadow_width ?>px;
		}
	<?php endif; ?>

	<?php if (($image_footer && $footer_pic_shadow_width) > 0) : ?>
		.articledetails-footer .info {
			padding-top: <?php echo ($footer_pic_shadow_width) ?>px;
		}
	<?php endif; ?>

		.articledetails .info .article_title {

			-webkit-box-flex: 1 1 auto;
			-moz-box-flex: 1 1 auto;
			-webkit-flex: 1 1 auto;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;

		    display: block;
			margin: 0 0 3px 0;
		    padding: 0;
		    text-align: <?php echo $align_details; ?>;
		}

		html[dir="rtl"] .articledetails .info .article_title {
			<?php if ($align_details == 'left') : ?>
			   	text-align: right;
			<?php endif; ?>
			<?php if ($align_details == 'right') : ?>
			   	text-align: left;
			<?php endif; ?>
		}

		.articledetails .info .article_edit,
		.articledetails .info .article_checked_out {
			margin-left: 10px;
			font-size: 0.8em;
			display: inline-block;
		}

		html[dir="rtl"] .articledetails .info .article_edit,
		html[dir="rtl"] .articledetails .info .article_checked_out {
			margin-left: 0;
			margin-right: 10px;
		}

		.articledetails .info p {
			margin: 0;
		    padding: 0;
		}

		.articledetails .info p + p {
			text-indent: 0;
		}

		.articledetails .info .form-inline {
			margin: 0;
			padding: 0;
			display: block;
		}

		.articledetails .info .form-inline select {
			width: auto;
			display: inline-block;
		}

		.articledetails-header .info .form-inline,
		.articledetails-header .info .details {
			text-align: <?php echo $align_details; ?>;
		}

		html[dir="rtl"] .articledetails-header .info .form-inline,
		html[dir="rtl"] .articledetails-header .info .details {
			<?php if ($align_details == 'left') : ?>
			   	text-align: right;
			<?php endif; ?>
			<?php if ($align_details == 'right') : ?>
			   	text-align: left;
			<?php endif; ?>
		}

		.articledetails-footer .info .form-inline,
		.articledetails-footer .info .details {
			text-align: <?php echo $footer_align_details; ?>;
		}

		html[dir="rtl"] .articledetails-footer .info .form-inline,
		html[dir="rtl"] .articledetails-footer .info .details {
			<?php if ($footer_align_details == 'left') : ?>
			   	text-align: right;
			<?php endif; ?>
			<?php if ($footer_align_details == 'right') : ?>
			   	text-align: left;
			<?php endif; ?>
		}

		.articledetails .info dl.item_details,
		.articledetails .info dd.details {
			margin: 0;
			padding: 0;
		}

		.articledetails .info dl.item_details > dt {
			position: absolute;
			top: -9999px;
			left: -9999px;
		}

		.articledetails .info .item_details {

			-webkit-box-flex: 1 1 auto;
			-moz-box-flex: 1 1 auto;
			-webkit-flex: 1 1 auto;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;

			font-size: <?php echo ($font_details / 100); ?>em;
			margin-bottom: 3px;
		}

		.articledetails .info .item_details.before_title {

			-webkit-box-flex: none;
			-moz-box-flex: none;
			-webkit-flex: none;
			-ms-flex: none;
			flex: none;
		}

		.articledetails .info .item_details.after_title {

			-webkit-box-flex: 2 2 auto;
			-moz-box-flex: 2 2 auto;
			-webkit-flex: 2 2 auto;
			-ms-flex: 2 2 auto;
			flex: 2 2 auto;
		}

			.articledetails .info .item_details .delimiter {
				white-space: pre-wrap;
			}

			.articledetails .info .details {
				<?php if ($details_line_spacing[0]) : ?>
					line-height: <?php echo $details_line_spacing[0]; ?><?php echo $details_line_spacing[1]; ?>;
				<?php endif; ?>
				color: <?php echo $details_font_color; ?>;
			}


			.articledetails .info .details [class^="SYWicon-"],
			.articledetails .info .details [class*=" SYWicon-"],
			.articledetails .info .details [class^="icon-"],
			.articledetails .info .details [class*=" icon-"],
			.articledetails .info .details [class^="fa-"],
			.articledetails .info .details [class*=" fa-"] {
				font-size: 1em;
			    color: <?php echo $iconfont_color; ?>;
			    padding-right: 3px;
			}

			html[dir="rtl"] .articledetails .info .details [class^="SYWicon-"],
			html[dir="rtl"] .articledetails .info .details [class*=" SYWicon-"],
			html[dir="rtl"] .articledetails .info .details [class^="icon-"],
			html[dir="rtl"] .articledetails .info .details [class*=" icon-"],
			html[dir="rtl"] .articledetails .info .details [class^="fa-"],
			html[dir="rtl"] .articledetails .info .details [class*=" fa-"] {
				padding-left: 3px;
			    padding-right: 0;
			}

			.articledetails .info .details a [class^="SYWicon-"],
			.articledetails .info .details a [class*=" SYWicon-"],
			.articledetails .info .details a [class^="icon-"],
			.articledetails .info .details a [class*=" icon-"],
			.articledetails .info .details a [class^="fa-"],
			.articledetails .info .details a [class*=" fa-"] {
				color: inherit;
			}

			.articledetails .info .details .detail {
				vertical-align: middle;
			}

			.articledetails .info .details .detail_email .detail_data i,
			.articledetails .info .details .detail_print .detail_data i {
				vertical-align: middle;
				font-size: 1.2em;
			}

			.articledetails .info .details .detail_rating .detail_data i {
				vertical-align: middle;
				font-size: 1.2em;
				color: <?php echo $star_color; ?>;
			}

			<?php if ($share_bgcolor) : ?>
				.articledetails .info .details .detail_social {
					line-height: 30px;
				}
			<?php endif; ?>

			.articledetails .info .details .detail_social a {
				text-align: center;
				margin: 0 3px;
				font-family: initial;
				line-height: 1em;
			}

			.articledetails .info .details .detail_social a:hover {
				text-decoration: none;
			}

			.articledetails .info .details .detail_social .detail_data a > i {
				vertical-align: middle;
				font-size: 1.2em;
			}

			.articledetails .info .details .detail_social .detail_data a svg {
				vertical-align: middle;
				width: 1.2em;
				height: 1.2em;
    			display: inline-block;
			}

			<?php if ($share_bgcolor) : ?>
    			.articledetails .info .details .detail_social .detail_data a > i,
    			.articledetails .info .details .detail_social .detail_data a.inline_svg .svg_container {
    				display: inline-block;
    				color: #fff;
    				padding: 6px;
    				<?php if ($share_radius > 0) : ?>
    					-webkit-border-radius: <?php echo $share_radius; ?>px;
    					-moz-border-radius: <?php echo $share_radius; ?>px;
    					border-radius: <?php echo $share_radius; ?>px;
    				<?php endif; ?>
    			}
			<?php endif; ?>

<?php if ($fixed_share) : ?>
	.articledetails-footer.mobile .info .details .detail_social {
		position: fixed;
    	left: 0;
    	bottom: 0;
    	width: 100%;
    	display: table;
    	table-layout: fixed;
    	text-align: center;
    	z-index: 1000;
	}

	.articledetails-footer.mobile .info .details .detail_social > i,
	.articledetails-footer.mobile .info .details .detail_social .detail_label,
	.articledetails-footer.mobile .info .details .detail_social .detail_post {
		display: none;
	}

	.articledetails-footer.mobile .info .details .detail_social .detail_data {
		width: 100%;
		display: table-row;
	}

	.articledetails-footer.mobile .info .details .detail_social .detail_data a {
		display: table-cell;
		margin: 0;
		vertical-align: middle;
    	<?php if (!$share_bgcolor) : ?>
			background-color: #fff;
			border: 1px solid #efefef;
		<?php endif; ?>
	}

	.articledetails-footer.mobile .info .details .detail_social .detail_data a > i {
		display: block;
		padding: 10px 0;
		font-size: 1.7em;
		border-radius: 0;
	}

	.articledetails-footer.mobile .info .details .detail_social .detail_data a svg {
		display: block;
		margin: 0 auto;
		width: 1.7em;
		height: 1.7em;
		border-radius: 0;
	}
<?php endif; ?>
