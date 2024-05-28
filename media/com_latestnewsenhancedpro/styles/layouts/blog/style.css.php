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

	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;

	-webkit-box-orient: horizontal;
	-webkit-box-direction: normal;
	-webkit-flex-flow: row wrap;
	-moz-box-orient: horizontal;
	-moz-box-direction: normal;
	-ms-flex-flow: row wrap;
	flex-flow: row wrap;

	<?php if ($items_align == 'fs') : ?>
    	-webkit-box-pack: start;
		-webkit-justify-content: flex-start;
    	-ms-flex-pack: start;
        justify-content: flex-start;
    <?php elseif ($items_align == 'fe') : ?>
    	-webkit-box-pack: end;
		-webkit-justify-content: flex-end;
    	-ms-flex-pack: end;
        justify-content: flex-end;
    <?php elseif ($items_align == 'c') : ?>
    	-webkit-box-pack: center;
		-webkit-justify-content: center;
    	-ms-flex-pack: center;
        justify-content: center;
    <?php elseif ($items_align == 'sb') : ?>
    	-webkit-box-pack: justify;
    	-webkit-justify-content: space-between;
        -ms-flex-pack: justify;
        justify-content: space-between;
    <?php elseif ($items_align == 'se') : ?>
    	-webkit-box-pack: space-evenly;
		-webkit-justify-content: space-evenly;
		-ms-flex-pack: space-evenly;
        justify-content: space-evenly;
    <?php else : ?>
    	-webkit-justify-content: space-around;
		-ms-flex-pack: distribute;
        justify-content: space-around;
    <?php endif; ?>

    <?php if ($items_valign == 'fs') : ?>
    	-webkit-box-align: start;
    	-ms-flex-align: start;
    	align-items: flex-start;
    <?php elseif ($items_valign == 'fe') : ?>
    	-webkit-box-align: end;
    	-ms-flex-align: end;
    	align-items: flex-end;
    <?php elseif ($items_valign == 'c') : ?>
    	-webkit-box-align: center;
    	-ms-flex-align: center;
    	align-items: center;
    <?php else : ?>
    	-webkit-box-align: stretch;
		-ms-flex-align: stretch;
		align-items: stretch;
	<?php endif; ?>

	width: 100%;
}

	<?php echo $suffix; ?> .latestnews-items .latestnews-item {

		display: inline-block;

		-webkit-box-flex: 1;
		-webkit-flex: 1 1 auto;
    	-ms-flex: 1 1 auto;
		flex: 1 1 auto;

		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;

	    width: <?php echo $item_width; ?>%;
	    min-width: <?php echo $item_minwidth; ?>px;

	    <?php if ($item_maxwidth) : ?>
	    	max-width: <?php echo $item_maxwidth; ?>px;
	    <?php endif; ?>

	    <?php if ($item_space_after > 0) : ?>
	    	margin-bottom: <?php echo $item_space_after; ?>px;
	    <?php endif; ?>

		<?php if ($item_space_between > 0) : ?>
			margin-left: <?php echo ($item_space_between / 2); ?>px;
	    	margin-right: <?php echo ($item_space_between / 2); ?>px;
		<?php endif; ?>
	}

	<?php echo $suffix; ?> .latestnews-items .latestnews-item.leading {
		width: <?php echo $leading_item_width; ?>%;
		min-width: <?php echo $leading_item_minwidth; ?>px;

	    <?php if ($leading_item_maxwidth) : ?>
	    	max-width: <?php echo $leading_item_maxwidth; ?>px;
	    <?php else : ?>
	    	<?php if ($item_maxwidth) : ?>
	    		max-width: inherit;
	    	 <?php endif; ?>
	    <?php endif; ?>

	    <?php if ($leading_item_space_after > 0) : ?>
	    	margin-bottom: <?php echo $leading_item_space_after; ?>px;
	    <?php else : ?>
	    	<?php if ($item_space_after > 0) : ?>
	    		margin-bottom: 0;
	    	<?php endif; ?>
	    <?php endif; ?>

	    <?php if ($leading_item_space_between > 0) : ?>
	    	margin-left: <?php echo ($leading_item_space_between / 2); ?>px;
	    	margin-right: <?php echo ($leading_item_space_between / 2); ?>px;
		<?php else : ?>
			<?php if ($item_space_between > 0) : ?>
    	    	margin-left: 0;
    	    	margin-right: 0;
			<?php endif; ?>
		<?php endif; ?>
	}

	<?php echo $suffix; ?> .latestnews-items .heading_group,
	<?php echo $suffix; ?> .latestnews-items .heading,
	<?php echo $suffix; ?> .latestnews-items .heading_description {
		width: 100%;
	}

/* load more */

<?php echo $suffix; ?> .loader {
	text-align: center;
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
