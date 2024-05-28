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

<?php echo $suffix; ?> .latestnews-items {
	clear: both;
	width: 100%;
}

<?php echo $suffix; ?> .latestnews-items .heading_group,
<?php echo $suffix; ?> .latestnews-items .heading,
<?php echo $suffix; ?> .latestnews-items .heading_description {
	width: 100%;
}

/* pagination */

<?php echo $suffix; ?> .pagination_wrapper {
	clear: both;
	width: 100%;
	padding: 10px 0;
}

	<?php echo $suffix; ?> .pagination_wrapper .pull-right,
	<?php echo $suffix; ?> .pagination_wrapper .float-right,
	<?php echo $suffix; ?> .pagination_wrapper .float-end {
		margin-left: 5px;
	}

	html[dir="rtl"] <?php echo $suffix; ?> .pagination_wrapper .pull-right,
	html[dir="rtl"] <?php echo $suffix; ?> .pagination_wrapper .float-right,
	html[dir="rtl"] <?php echo $suffix; ?> .pagination_wrapper .float-end {
		margin-left: 0;
		margin-right: 5px;
	}

	<?php echo $suffix; ?> .pagination_wrapper .counterpagination,
	<?php echo $suffix; ?> .pagination_wrapper .countertotal {
		text-align: center;
	}

	<?php echo $suffix; ?> .searchblock,
	<?php echo $suffix; ?> .clearblock {
		display: inline-block;
		margin-bottom: 5px;
	}

	<?php echo $suffix; ?> .searchblock .options label {
		font-size: 0.85em;
	}

	<?php echo $suffix; ?> .index_filter.listing {
		clear: both;
	}

	<?php echo $suffix; ?> .pagination_limit,
	<?php echo $suffix; ?> .index_filter.selection {
		float: right;
		margin: 0 0 5px 5px;
	}

	html[dir="rtl"] <?php echo $suffix; ?> .pagination_limit,
	html[dir="rtl"] <?php echo $suffix; ?> .index_filter.selection {
		float: left;
		margin: 0 5px 5px 0;
	}

	<?php echo $suffix; ?> .index_filter.selection > label {
		vertical-align: inherit;
		padding: 0 5px 0 0;
		margin: 0;
		display: inline-block;
	}

	html[dir="rtl"] <?php echo $suffix; ?> .index_filter.selection > label {
		padding: 0 0 0 5px;
	}

	<?php echo $suffix; ?> .pagination_limit select,
	<?php echo $suffix; ?> .index_filter.selection select {
		display: inline-block;
		margin: 0;
		width: auto;
	}

	<?php echo $suffix; ?> .index_filter.listing.flat ul {
		text-align: center;
		list-style: none;
		margin: 0;
		padding: 0;
	}

	<?php echo $suffix; ?> .index_filter.listing ul[data-label]::before {
		content: attr(data-label);
		padding: 0 5px 0 0;
	}

	html[dir="rtl"] <?php echo $suffix; ?> .index_filter.listing ul[data-label]::before {
		padding: 0 0 0 5px;
	}

		<?php echo $suffix; ?> .index_filter.listing.flat ul li {
		    list-style-type: none;
			display: inline-block;
			margin: 2px;
			padding: 0;
		}
		
		<?php echo $suffix; ?> .index_filter.listing.tree ul li {
		    list-style-type: none;
			display: list-item;
			padding: 0;
		}

			<?php echo $suffix; ?> .index_filter.listing.flat li a {
				display: inline-block;
				text-decoration: none;
			}
			
			<?php echo $suffix; ?> .index_filter.listing li a:focus span,
			<?php echo $suffix; ?> .index_filter.listing li a:hover span {
				text-decoration: none;
			}
			
			<?php echo $suffix; ?> .index_filter.listing li a:focus span:not(.nopicture),
			<?php echo $suffix; ?> .index_filter.listing li a:hover span:not(.nopicture) {
				text-decoration: underline;
			}

			<?php echo $suffix; ?> .index_filter.listing li.selected > a span:not(.nopicture) {
				font-weight: bolder;
				text-decoration: underline;
			}
			
			<?php echo $suffix; ?> .index_filter.listing img + span,
			<?php echo $suffix; ?> .index_filter.listing .nopicture + span {
				padding-left: 10px;
			}
			
			<?php echo $suffix; ?> .index_filter.listing.flat li > a > img,
			<?php echo $suffix; ?> .index_filter.listing.flat li > span > img {
				margin-left: 10px;
			}

			<?php echo $suffix; ?> .index_filter.listing li img {
				vertical-align: middle;
				max-width: 32px;
				max-height: 32px;
			}

			<?php echo $suffix; ?> .index_filter.listing li .nopicture {
				display: inline-block;
				background-color: transparent;
				vertical-align: middle;
				width: 32px;
				height: 32px;
			}

/* for lack of specific theme */

<?php echo $suffix; ?> .newshead {
	margin: 0 auto;
}

<?php echo $suffix; ?> .newshead > div,
<?php echo $suffix; ?> .newshead > a {
	margin: 0 auto;
}

<?php if ($leading_image || $image) : ?>

	<?php echo $suffix; ?> .newshead.picturetype {
		position: relative;
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