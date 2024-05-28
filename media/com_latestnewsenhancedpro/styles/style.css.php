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

/* page heading */

<?php echo $true_suffix; ?> h1 {
	display: inline-block;
}

/* article before */

<?php echo $true_suffix; ?> .pretext {}

/* article after */

<?php echo $true_suffix; ?> .posttext {
	margin-top: 20px;
	clear: both;
}

/* icons */

<?php echo $true_suffix; ?> .icons ul.list {
	list-style: none;
	padding: 0;
	margin: 0;
	float: right;
}

html[dir="rtl"] <?php echo $true_suffix; ?> .icons ul.list {
	float: left;
}

<?php echo $true_suffix; ?> .icons ul.list > li {
	display: inline-block;
	padding: 0 5px;
}

<?php echo $true_suffix; ?> .icons ul.list > li:last-child {
	padding: 0 0 0 5px;
}

html[dir="rtl"] <?php echo $true_suffix; ?> .icons ul.list > li:last-child {
	padding: 0 5px 0 0;
}

/* k2 style overrides */

<?php echo $suffix; ?> .even {
	border-bottom: none;
	background: none;
	padding: 0;
}

<?php echo $suffix; ?> .odd {
	border-bottom: none;
	background: none;
	padding: 0;
}

/* line break */

<?php echo $suffix; ?> .linebreak {
	width: 100%;
	border: none;
	margin: 0;
	padding: 0;
	background-color: unset;
	display: block;
}

<?php if ($layout == 'list') : ?>

    <?php echo $suffix; ?> .table {
    	table-layout: <?php echo $table_layout; ?>;
    	width: 100%;
    	max-width: 100%;
    }

    <?php echo $suffix; ?> .table th,
    <?php echo $suffix; ?> .table td {
    	text-align: inherit;
    	vertical-align: middle;
    }

    <?php echo $suffix; ?> .table th {
    	<?php if ($heading_color && $heading_color != 'transparent') : ?>
    		color: <?php echo $heading_color; ?>;
    	<?php endif; ?>
    	<?php if ($heading_bgcolor) : ?>
    		background-color: <?php echo $heading_bgcolor; ?>;
    	<?php endif; ?>
    }

    <?php echo $suffix; ?> .table th .heading-sort,
    <?php echo $suffix; ?> .table th .heading-sort button {
    	margin: 0;
    	padding: 0;
    }

    <?php echo $suffix; ?> .col_edit {
    	width: 24px;
    	text-align: center;
    }

    <?php if ($min_row_width) : ?>
    	@media only screen and (max-width: <?php echo $min_row_width; ?>px) {

    		<?php echo $suffix; ?> .table {
    			border: none;
    		}

    		/* Force table to not be like tables anymore */
    		<?php echo $suffix; ?> table,
    		<?php echo $suffix; ?> tbody,
    		<?php echo $suffix; ?> thead,
    		<?php echo $suffix; ?> th,
    		<?php echo $suffix; ?> td,
    		<?php echo $suffix; ?> tr {
    			display: block;
    		}

    		/* Hide table headers (but not display: none;, for accessibility) */
    		<?php echo $suffix; ?> .table thead tr {
    			position: absolute;
    			top: -9999px;
    			left: -9999px;
    		}

    		<?php echo $suffix; ?> .table tr {
    			border: 1px solid #ccc;
    			margin-top: 20px;
    			-webkit-border-radius: 5px;
    			-moz-border-radius: 5px;
    			border-radius: 5px;
    		}

    		<?php echo $suffix; ?> .table tr:first-child {
    			margin-top: 0;
    		}

    		<?php echo $suffix; ?> .table td {
    			/* Behave like a "row" */
    			border: none;
    			border-bottom: 1px solid #eee;
    			position: relative;

    			padding-left: <?php echo (intVal($header_width) + 26); ?>px;

    			min-height: 20px;
    			white-space: normal;
    		}

    		html[dir="rtl"] <?php echo $suffix; ?> .table td {
    			padding-left: 0;
    			padding-right: <?php echo (intVal($header_width) + 26); ?>px;
    		}

    		<?php echo $suffix; ?> .table td:before {
    			/* behave like a table header */
    			position: absolute;
    			left: 8px;

    			width: <?php echo $header_width; ?>px;

    			margin-right: 8px;
    			white-space: nowrap;
    			overflow: hidden;
    			text-overflow: ellipsis;
    			font-weight: bold;
    			top: 50%;
    	    	transform: translateY(-50%);

    	    	line-height: 25px;
    	    	padding: 0 5px;
    	    	<?php if ($heading_color && $heading_color != 'transparent') : ?>
    				color: <?php echo $heading_color; ?>;
    			<?php endif; ?>
    			<?php if ($heading_bgcolor) : ?>
    				background-color: <?php echo $heading_bgcolor; ?>;
    			<?php endif; ?>
    		}

    		html[dir="rtl"] <?php echo $suffix; ?> .table td:before {
    			left: 0;
    			right: 8px;
    			margin-right: 0;
    			margin-left: 8px;
    		}

    		<?php echo $suffix; ?> .table td:before {
    			content: attr(data-title);
    		}

    		<?php echo $suffix; ?> .col_edit {
    			width: auto;
    			text-align: inherit;
    		}

    		<?php echo $suffix; ?> .table td.col_head .newshead {
    			margin: 0;
    		}
    	}
    <?php endif; ?>

    <?php echo $suffix; ?> tr.latestnews-item td {
    	font-size: <?php echo ($font_size_reference / 100); ?>em;
    }

    <?php echo $suffix; ?> tr.latestnews-item.leading td {
    	font-size: <?php echo ($leading_font_size_reference / 100); ?>em;
    }

<?php else : ?>

    <?php echo $suffix; ?> .news,
    <?php echo $suffix; ?> .innernews {
    	overflow: hidden;

    	-webkit-box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	box-sizing: border-box;
    }

    <?php if ($items_valign == 's') : ?>
    	<?php echo $suffix; ?> .news {
    		height: 100%;
    	}
    <?php endif; ?>

    <?php echo $suffix; ?> .innernews {
    	font-size: <?php echo ($font_size_reference / 100); ?>em;
    	<?php if ($bgcolor_body && $bgcolor_body != 'transparent') : ?>
    		background-color: <?php echo $bgcolor_body; ?>;
    	<?php endif; ?>
    	<?php if ($border_width_body > 0 && ($border_color_body || $colortheme)) : ?>
    		<?php if ($border_color_body) : ?>
    			border: <?php echo $border_width_body; ?>px solid <?php echo $border_color_body; ?>;
    		<?php else : ?>
    			border-width: <?php echo $border_width_body; ?>px;
    			border-style: solid;
    		<?php endif; ?>
    	<?php endif; ?>
    	<?php if ($border_radius_body > 0) : ?>
        	-moz-border-radius: <?php echo $border_radius_body; ?>px;
        	-webkit-border-radius: <?php echo $border_radius_body; ?>px;
    		border-radius: <?php echo $border_radius_body; ?>px;
    	<?php endif; ?>
    	<?php if ($shadow_body == 's') : ?>
    		-webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 3px 1px -2px rgba(0,0,0,0.12),0 1px 5px 0 rgba(0,0,0,0.2);
    		box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 3px 1px -2px rgba(0,0,0,0.12),0 1px 5px 0 rgba(0,0,0,0.2);
    		margin: 6px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 12px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($shadow_body == 'm') : ?>
    		-webkit-box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14),0 1px 10px 0 rgba(0,0,0,0.12),0 2px 4px -1px rgba(0,0,0,0.3);
    		box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14),0 1px 10px 0 rgba(0,0,0,0.12),0 2px 4px -1px rgba(0,0,0,0.3);
    		margin: 11px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 22px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($shadow_body == 'l') : ?>
    		-webkit-box-shadow: 0 8px 17px 2px rgba(0,0,0,0.14),0 3px 14px 2px rgba(0,0,0,0.12),0 5px 5px -3px rgba(0,0,0,0.2);
    		box-shadow: 0 8px 17px 2px rgba(0,0,0,0.14),0 3px 14px 2px rgba(0,0,0,0.12),0 5px 5px -3px rgba(0,0,0,0.2);
    		margin: 27px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 54px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($shadow_body == 'ss') : ?>
    		-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
    		box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
    		margin: 5px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 10px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($shadow_body == 'sm') : ?>
    		-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
    		box-shadow: 1px 1px 10px rgba(51, 51, 51, 0.2);
    		margin: 11px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 22px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($shadow_body == 'sl') : ?>
    		-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
    		box-shadow: 1px 1px 15px rgba(51, 51, 51, 0.2);
    		margin: 16px;

    		<?php if ($items_valign == 's') : ?>
    			height: calc(100% - 32px);
    		<?php endif; ?>

    	<?php endif; ?>
    	<?php if ($padding_body > 0) : ?>
    		padding: <?php echo $padding_body; ?>px;
    	<?php endif; ?>
    	<?php if ($font_color_body) : ?>
    		color: <?php echo $font_color_body; ?>;
    	<?php endif; ?>
    }

    <?php echo $suffix; ?> .leading .innernews {
    	font-size: <?php echo ($leading_font_size_reference / 100); ?>em;
    }

    <?php if ($link_color_body) : ?>
    	<?php echo $suffix; ?> .innernews a:not(.btn) {
    		color: <?php echo $link_color_body; ?>;
    	}
    <?php endif; ?>

    <?php if ($link_color_hover_body) : ?>
    	<?php echo $suffix; ?> .innernews a:not(.btn):hover,
    	<?php echo $suffix; ?> .innernews a:not(.btn):focus {
    		color: <?php echo $link_color_hover_body; ?>;
    		text-decoration: underline;
    	}
    <?php endif; ?>

    <?php if (is_int($padding_head) && $padding_head >= 0) : ?>
    	<?php echo $suffix; ?> .newshead {
    		padding: <?php echo $padding_head; ?>px !important;
    	}
    <?php endif; ?>

    <?php if (is_int($padding_info) && $padding_info >= 0) : ?>
    	<?php echo $suffix; ?> .newsinfo,
    	<?php echo $suffix; ?> .newsinfooverhead {
    		padding: <?php echo $padding_info; ?>px !important;
    	}
    <?php endif; ?>

    <?php if ($leading_image || $image || $leading_video || $video) : ?>

    	<?php echo $suffix; ?> .newshead .over_head {
    		position: absolute;
    		bottom: 0;
    		left: 0;
    		width: 100%;
    		padding: 10px;
    		-webkit-box-sizing: border-box;
    		-moz-box-sizing: border-box;
    		box-sizing: border-box;
        	pointer-events: none;
    	}

    	<?php if ($over_head_contrast) : ?>
    		<?php echo $suffix; ?> .newshead .picture .over_head,
    		<?php echo $suffix; ?> .newshead .video .over_head {
    			padding: 60px 10px 10px 10px;
    			background: -webkit-gradient(linear, left bottom, left top, from(rgba(0, 0, 0, 0.85)), to(transparent));
        		background: -o-linear-gradient(bottom, rgba(0, 0, 0, 0.85), transparent);
        		background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
    		}
    	<?php endif; ?>

    	<?php echo $suffix; ?> .newshead .catlink {
    		position: absolute;
    		top: 10px;
    		left: 10px;
    		z-index: 1;
    	}

    	<?php echo $suffix; ?> .newshead .catlink.right {
    		left: auto;
    		right: 10px;
    	}

    	<?php echo $suffix; ?> .newshead .catlink.center {
    		width: 100%;
    		left: auto;
    		right: auto;
    	}

    	<?php echo $suffix; ?> .newshead .catlink.nostyle span {
    		background-color: #fff;
    		padding: 2px 4px;
    	}

    <?php endif; ?>

    <?php echo $suffix; ?> .newshead,
    <?php echo $suffix; ?> .newsinfo {
    	-webkit-box-sizing: border-box;
    	-moz-box-sizing: border-box;
    	box-sizing: border-box;
    }

    <?php if ($content_align) : ?>
    	<?php echo $suffix; ?> .newsinfo .item_details .newsextra,
    	<?php echo $suffix; ?> .newsinfo .newstitle,
    	<?php echo $suffix; ?> .newsinfo .newsintro {
    		text-align: <?php echo $content_align; ?> !important;
    	}
    <?php endif; ?>

    <?php if ($leading_content_align && $leading_content_align !== $content_align) : ?>
    	<?php echo $suffix; ?> .leading .newsinfo .item_details .newsextra,
    	<?php echo $suffix; ?> .leading .newsinfo .newstitle,
    	<?php echo $suffix; ?> .leading .newsinfo .newsintro {
    		text-align: <?php echo $leading_content_align; ?> !important;
    	}
    <?php endif; ?>

    /* link and category */

    <?php echo $suffix; ?> .link {
    	margin: 5px 0 0 0;
    }

    <?php echo $suffix; ?> .link.left,
    <?php echo $suffix; ?> .catlink.left {
    	text-align: left;
    }

    <?php echo $suffix; ?> .link.center,
    <?php echo $suffix; ?> .catlink.center {
    	text-align: center;
    }

    <?php echo $suffix; ?> .link.right,
    <?php echo $suffix; ?> .catlink.right {
    	text-align: right;
    }

    <?php echo $suffix; ?> .catlink.first {
    	margin-bottom: 5px;
    	width: 100%;
    }

    <?php echo $suffix; ?> .catlink.last {
    	margin-top: 5px;
    	width: 100%;
    }

    /* title */

    <?php echo $suffix; ?> .newstitle {
    	margin: 6px 0;
    	line-height: initial;
    }

    <?php if ($force_title_one_line) : ?>
    	<?php echo $suffix; ?> .newstitle,
    	<?php echo $suffix; ?> .newstitle a {
    		display: block;
        	white-space: nowrap;
        	text-overflow: ellipsis;
        	overflow: hidden;
    	}
    <?php endif; ?>

    <?php if ($leading_force_title_one_line) : ?>
    	<?php echo $suffix; ?> .leading .newstitle,
    	<?php echo $suffix; ?> .leading .newstitle a {
    		display: block;
        	white-space: nowrap;
        	text-overflow: ellipsis;
        	overflow: hidden;
    	}
    <?php else: ?>
    	<?php if ($force_title_one_line) : ?>
    		<?php echo $suffix; ?> .leading .newstitle,
    		<?php echo $suffix; ?> .leading .newstitle a {
    	    	white-space: normal;
    	    	overflow: initial;
    		}
    	<?php endif; ?>
    <?php endif; ?>

    /* news extra */

    <?php echo $suffix; ?> .delimiter {
    	white-space: pre-wrap;
    }

    <?php echo $suffix; ?> dl.item_details,
    <?php echo $suffix; ?> dd.newsextra {
    	margin: 0;
    	padding: 0;
    }

    <?php echo $suffix; ?> dl.item_details > dt {
    	position: absolute;
    	top: -9999px;
    	left: -9999px;
    }

    <?php echo $suffix; ?> .newsextra {
    	font-size: <?php echo ($font_size_details / 100); ?>em;
    	<?php if ($details_line_spacing[0]) : ?>
    		line-height: <?php echo $details_line_spacing[0]; ?><?php echo $details_line_spacing[1]; ?>;
    	<?php endif; ?>
    	<?php if ($details_font_color) : ?>
    		color: <?php echo $details_font_color; ?>;
    	<?php endif; ?>
    }

    <?php echo $suffix; ?> .over_head .newsextra {
    	pointer-events: auto;
    	<?php if ($details_font_color_overhead) : ?>
    		color: <?php echo $details_font_color_overhead; ?>;
    	<?php endif; ?>
    	<?php if ($over_head_contrast) : ?>
    		text-shadow: 0 2px 3px rgba(0, 0, 0, 0.3);
    	<?php endif; ?>
    }

    <?php echo $suffix; ?> .newsextra .detail_icon {
        font-size: 1em;
        padding-right: 3px;
        <?php if ($iconfont_color) : ?>
        	color: <?php echo $iconfont_color; ?>;
    	<?php endif; ?>
    }

    <?php if ($iconfont_color_overhead) : ?>
    	<?php echo $suffix; ?> .over_head .newsextra .detail_icon {
    	    color: <?php echo $iconfont_color_overhead; ?>;
    	}
    <?php endif; ?>

    html[dir="rtl"] <?php echo $suffix; ?> .newsextra .detail_icon {
    	padding-left: 3px;
    	padding-right: 0;
    }

    <?php echo $suffix; ?> .newsextra a .detail_icon {
        color: inherit;
    }

<?php endif; ?>

<?php if ($leading_image || $image) : ?>

	<?php if ($image) : ?>
		<?php if ($head_width <= 0 || $head_height <= 0) : ?>
			<?php echo $suffix; ?> .newshead .picture {
				width: max-content;
				max-width: 100%;
			}

			<?php echo $suffix; ?> .newshead .nopicture {
				display: none;
			}
		<?php else : ?>
			<?php echo $suffix; ?> .newshead .picture {
				max-width: <?php echo $head_width; ?>px;
				max-height: <?php echo $head_height; ?>px;
			}
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($leading_image) : ?>
		<?php if ($leading_head_width <= 0 || $leading_head_height <= 0) : ?>
			<?php echo $suffix; ?> .leading .newshead .picture {
				width: max-content;
				max-width: 100%;
				max-height: inherit;
			}

			<?php echo $suffix; ?> .leading .newshead .nopicture {
				display: none;
			}
		<?php else : ?>
			<?php echo $suffix; ?> .leading .newshead .picture {
				width: auto;
				max-width: <?php echo ($leading_head_width); ?>px;
				max-height: <?php echo $leading_head_height; ?>px;
			}

			<?php echo $suffix; ?> .leading .newshead .nopicture {
				display: block;
			}
		<?php endif; ?>
	<?php endif; ?>

	<?php echo $suffix; ?> .newshead .picture,
	<?php echo $suffix; ?> .newshead .nopicture {
    	overflow: hidden;
		text-align: center;
		position: relative;
		<?php if ($bgcolor && $bgcolor != 'transparent') : ?>
			background-color: <?php echo $bgcolor; ?>;
		<?php endif; ?>
	}

<?php endif; ?>

<?php if ($leading_calendar || $calendar) : ?>

	<?php echo $suffix; ?> .newshead.calendartype {
    	font-size: <?php echo $font_ref_cal; ?>px; /* the base size for the calendar */
    }

    <?php if ($calendar) : ?>
		<?php echo $suffix; ?> .newshead.calendartype .calendar {
			width: <?php echo $head_width; ?>px;
			max-width: <?php echo $head_width; ?>px;
			height: auto;
		}

			<?php echo $suffix; ?> .newshead .calendar.image {
				height: <?php echo $head_height; ?>px;
			}
	<?php endif; ?>

	<?php if ($leading_calendar) : ?>
		<?php echo $suffix; ?> .leading .newshead.calendartype .calendar {
			width: <?php echo $leading_head_width; ?>px;
			max-width: <?php echo $leading_head_width; ?>px;
			height: auto;
		}

			<?php echo $suffix; ?> .leading .newshead .calendar.image {
				height: <?php echo $leading_head_height; ?>px;
			}
	<?php endif; ?>

	<?php echo $suffix; ?> .newshead .calendar .position1,
	<?php echo $suffix; ?> .newshead .calendar .position2,
	<?php echo $suffix; ?> .newshead .calendar .position3,
	<?php echo $suffix; ?> .newshead .calendar .position4,
	<?php echo $suffix; ?> .newshead .calendar .position5 {
		display: block;
	}

<?php endif; ?>

<?php if ($leading_video || $video) : ?>

	<?php echo $suffix; ?> .newshead.videotype {
		position: relative;
		max-width: 100%;

		<?php if ($video_shadow_width > 0) : ?>
   			padding: <?php echo (intval($video_shadow_width) + 2); ?>px;

   			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
   		<?php endif; ?>
	}

    <?php if ($video) : ?>
   		<?php echo $suffix; ?> .newshead.videotype .video,
   		<?php echo $suffix; ?> .newshead.videotype .novideo {
   			<?php if ($head_width > 0) : ?>
   				width: <?php echo $head_width; ?>px;
   			<?php endif; ?>
   			max-width: 100%;
			<?php if ($head_height > 0) : ?>
   				max-height: <?php echo $head_height; ?>px;
   			<?php endif; ?>
   		}
    <?php endif; ?>

	<?php if ($leading_video) : ?>
    	<?php echo $suffix; ?> .leading .newshead.videotype .video,
    	<?php echo $suffix; ?> .leading .newshead.videotype .novideo {
    		<?php if ($leading_head_width > 0) : ?>
    			width: <?php echo $leading_head_width; ?>px;
    		<?php endif; ?>
   			max-width: 100%;
    		<?php if ($leading_head_height > 0) : ?>
    			max-height: <?php echo $leading_head_height; ?>px;
    		<?php endif; ?>
    	}
    <?php endif; ?>

			<?php echo $suffix; ?> .newshead .novideo > span {
				width: auto;
				display: inline-block;
				vertical-align: top; /* to shave a couple pixels from the height */
				<?php if ($video_ratio) : ?>
    				padding-bottom: <?php echo $video_ratio; ?>%;
    				height: 0;
    			<?php else : ?>
    				height: <?php echo $head_height; ?>px;
    			<?php endif; ?>
			}

			<?php if ($leading_video && !$video_ratio) : ?>
				<?php echo $suffix; ?> .leading .newshead .novideo > span {
					height: <?php echo $leading_head_height; ?>px;
				}
			<?php endif; ?>

		<?php echo $suffix; ?> .newshead .video,
		<?php echo $suffix; ?> .newshead .novideo {
			position: relative;
			overflow: hidden;
    		<?php if ($video_border_width > 0) : ?>
    			border: <?php echo $video_border_width ?>px solid <?php echo $video_border_color ?>;

    			-webkit-box-sizing: border-box;
			    -moz-box-sizing: border-box;
			    box-sizing: border-box;
    		<?php endif; ?>

    		<?php if ($video_border_radius > 0) : ?>
    			border-radius: <?php echo $video_border_radius; ?>px;
    			-moz-border-radius: <?php echo $video_border_radius; ?>px;
    			-webkit-border-radius: <?php echo $video_border_radius; ?>px;
    		<?php endif; ?>

    		<?php if ($video_shadow_width > 0) : ?>
    			-moz-box-shadow: 0 0 <?php echo $video_shadow_width; ?>px rgba(0, 0, 0, 0.8);
    			-webkit-box-shadow: 0 0 <?php echo $video_shadow_width; ?>px rgba(0, 0, 0, 0.8);
    			box-shadow: 0 0 <?php echo $video_shadow_width; ?>px rgba(0, 0, 0, 0.8);
    		<?php endif; ?>
		}

        <?php echo $suffix; ?> .newshead .video {
            cursor: pointer;
        }

        <?php echo $suffix; ?> .newshead .video.image {
            cursor: default;
        }

			<?php echo $suffix; ?> .newshead .video.image .innerpicture {
            	<?php if ($video_ratio) : ?>
            		padding-bottom: <?php echo $video_ratio; ?>%;
            		height: auto;
	            <?php else : ?>
	            	height: <?php echo $head_height; ?>px;
				<?php endif; ?>
			}

			<?php if ($leading_video && !$video_ratio) : ?>
            	<?php echo $suffix; ?> .leading .newshead .video.image .innerpicture {
            		height: <?php echo $leading_head_height; ?>px;
            	}
            <?php endif; ?>

            	<?php echo $suffix; ?> .newshead .video.image .innerpicture img {

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

    		<?php echo $suffix; ?> .newshead .innervideo {
    			position: relative;
    			overflow: hidden;
    			<?php if ($video_ratio) : ?>
    				padding-bottom: <?php echo $video_ratio; ?>%;
    				height: 0;
    			<?php endif; ?>
    			<?php if ($video_bgcolor) : ?>
    				background-color: <?php echo $video_bgcolor; ?>;
    			<?php endif; ?>
    			<?php if ($video_bgimage) : ?>
    				background-image: url("../../<?php echo $video_bgimage; ?>");
    				background-position: center;
    				background-repeat: no-repeat;

    				-webkit-background-size:cover;
    				-moz-background-size:cover;
    				-o-background-size:cover;
    				background-size: cover;
    			<?php endif; ?>
    		}

    		<?php if (!$video_ratio) : ?>
    			<?php echo $suffix; ?> .newshead .innervideo {
    				height: <?php echo $head_height; ?>px;
    			}
    		<?php endif; ?>

            <?php if ($leading_video && !$video_ratio) : ?>
            	<?php echo $suffix; ?> .leading .newshead .innervideo {
            		height: <?php echo $leading_head_height; ?>px;
            	}
            <?php endif; ?>

			<?php echo $suffix; ?> .newshead .innervideo iframe {
				z-index: 20;
				<?php if ($video_ratio) : ?>
					left: 0;
					top: 0;
					height: 100%;
					width: 100%;
					position: absolute;
				<?php else :  ?>
					position: relative;
				<?php endif; ?>
			}

    		<?php echo $suffix; ?> .newshead .innervideo .playbutton {
    			max-width: 64px;
                max-height: 64px;

                -webkit-transform: translate(-50%, -50%);
    			-ms-transform: translate(-50%, -50%);
    			transform: translate(-50%, -50%);

                top: 50%;
                left: 50%;
                position: absolute;
                width: 20%;

				z-index: 10;
    		}

    		<?php echo $suffix; ?> .newshead .innervideo .playbutton .back {
    			fill: #000;
    			opacity: .7;

    			-webkit-transition: fill .3s ease-out;
    			-o-transition: fill .3s ease-out;
    			transition: fill .3s ease-out;
    		}

    		<?php echo $suffix; ?> .newshead .innervideo:hover .playbutton .back {
    			opacity: .5;
    		}

    		<?php echo $suffix; ?> .newshead .innervideo .playbutton .arrow {
    			fill: #fff;
    			opacity: .7;
    		}

    		<?php echo $suffix; ?> .newshead .innervideo:hover .playbutton .arrow {
    			opacity: .5;
    		}

    		<?php echo $suffix; ?> .newshead .innervideo video {
    			display: block;
    		}
<?php endif; ?>

<?php if ($leading_icon || $icon) : ?>

	<?php echo $suffix; ?> .newshead.icontype {
		position: relative;
		max-width: 100%;

		display: -webkit-box;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;

		-webkit-box-align: stretch;
		-webkit-align-items: stretch;
		-ms-flex-align: stretch;
		align-items: stretch;

		<?php if (intval($icon_shadow_width) > 0) : ?>
			padding: <?php echo (intval($icon_shadow_width) + 2) ?>px;

			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		<?php endif; ?>
	}

		<?php echo $suffix; ?> .newshead.icontype a {
			text-decoration: none;
		}

		<?php if ($icon) : ?>
			<?php echo $suffix; ?> .newshead.icontype .icon,
			<?php echo $suffix; ?> .newshead.icontype .noicon {
				width: <?php echo $head_width; ?>px;
				height: <?php echo $head_height; ?>px;
			}
		<?php endif; ?>

		<?php if ($leading_icon) : ?>
			<?php echo $suffix; ?> .leading .newshead.icontype .icon,
			<?php echo $suffix; ?> .leading .newshead.icontype .noicon {
				width: <?php echo $leading_head_width; ?>px;
				height: <?php echo $leading_head_height; ?>px;
			}
		<?php endif; ?>

		<?php echo $suffix; ?> .newshead .icon,
		<?php echo $suffix; ?> .newshead .noicon {
			<?php if ($icon_bgcolor && $icon_bgcolor != 'transparent') : ?>
				background-color: <?php echo $icon_bgcolor; ?>;
			<?php endif; ?>

			padding: <?php echo $icon_padding; ?>px;

			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;

			display: -webkit-box;
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
				-moz-box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				-webkit-box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
				box-shadow: 0 0 <?php echo $icon_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			<?php endif; ?>
		}

		<?php echo $suffix; ?> .newshead .icon i {
			color: <?php echo $icon_color; ?>;

			<?php if ($icon_text_shadow_width> 0) : ?>
				text-shadow: 0 0 <?php echo $icon_text_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			<?php endif; ?>
		}

		<?php echo $suffix; ?> .newshead .noicon i {
			color: transparent;
		}

			<?php if ($icon) : ?>
				<?php echo $suffix; ?> .newshead .icon i,
				<?php echo $suffix; ?> .newshead .noicon i {
					width: 100%;
					height: 100%;
					display: inline-block;
					margin: 0;
					<?php if ($head_width >= $head_height) : ?>
						font-size: <?php echo ($head_height - $icon_border_width * 2 - $icon_padding * 2); ?>px;
						line-height: <?php echo ($head_height - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php else : ?>
						font-size: <?php echo ($head_width - $icon_border_width * 2 - $icon_padding * 2); ?>px;
						line-height: <?php echo ($head_width - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php endif; ?>
				}
			<?php endif; ?>

			<?php if ($leading_icon) : ?>
				<?php echo $suffix; ?> .leading .newshead .icon i,
				<?php echo $suffix; ?> .leading .newshead .noicon i {
					width: 100%;
					height: 100%;
					display: inline-block;
					margin: 0;
					<?php if ($leading_head_width >= $leading_head_height) : ?>
						font-size: <?php echo ($leading_head_height - $icon_border_width * 2 - $icon_padding * 2); ?>px;
						line-height: <?php echo ($leading_head_height - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php else : ?>
						font-size: <?php echo ($leading_head_width - $icon_border_width * 2 - $icon_padding * 2); ?>px;
						line-height: <?php echo ($leading_head_width - $icon_border_width * 2 - $icon_padding * 2); ?>px;
					<?php endif; ?>
				}
			<?php endif; ?>
<?php endif; ?>

<?php echo $suffix; ?> .detail_rating .detail_data .detail_icon {
	color: <?php echo $star_color; ?>;
}

<?php echo $suffix; ?> .detail_social {
	vertical-align: middle;
}

<?php echo $suffix; ?> .detail_social a {
	text-align: center;
	margin: 0 2px;
	font-family: initial;
	line-height: 1em;
}

<?php echo $suffix; ?> .detail_social .detail_data a > i {
	padding-right: 0;
	vertical-align: middle;
	font-size: 1.2em;
}

html[dir="rtl"] <?php echo $suffix; ?> .detail_social .detail_data a > i {
	padding-left: 0;
}

<?php echo $suffix; ?> .detail_social .detail_data a svg {
	vertical-align: middle;
	<?php if ($share_size[0]) : ?>
		width: <?php echo $share_size[0]; ?><?php echo $share_size[1]; ?>;
		height: <?php echo $share_size[0]; ?><?php echo $share_size[1]; ?>;
	<?php else : ?>
		width: 1.2em;
		height: 1.2em;
	<?php endif; ?>
    display: inline-block;
}

<?php if ($share_bgcolor) : ?>
	<?php echo $suffix; ?> .detail_social .detail_data a > i,
	<?php echo $suffix; ?> .detail_social .detail_data a.inline_svg .svg_container {
		display: inline-block;
    	color: #fff;
    	padding: 6px;
    	<?php if ($share_radius > 0) : ?>
    		-webkit-border-radius: <?php echo $share_radius; ?>px;
    		-moz-border-radius: <?php echo $share_radius; ?>px;
    		border-radius: <?php echo $share_radius; ?>px;
    	<?php endif; ?>
		line-height: 0;
	}
<?php endif; ?>
