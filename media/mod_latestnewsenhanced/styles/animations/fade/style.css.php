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

<?php echo $suffix; ?> {
	display: none;
}

<?php echo $suffix; ?> .lnee_carousel_wrapper {
	cursor: default !important;
	margin-left: auto !important;
	margin-right: auto !important;
}

<?php echo $suffix; ?> ul.latestnews-items {
	display: block;
	opacity: 0;
}

<?php echo $suffix; ?> ul.latestnews-items li.latestnews-item {
	display: block !important;
	width: <?php echo $item_width; ?><?php echo $item_width_unit; ?>;
}

<?php echo $suffix; ?>.horizontal ul.latestnews-items li.latestnews-item {
	float: left;
	margin-left: 2px;
	margin-right: 2px;
	padding: 5px 2px;
}

<?php echo $suffix; ?>.vertical ul.latestnews-items li.latestnews-item {
	margin-left: 0;
	margin-right: 0;
}

<?php echo $suffix; ?>.horizontal ul.latestnews-items li.latestnews-item .news {
	width: auto !important;
}

<?php if ($symbols || $arrows || $pages) : ?>
	<?php echo $suffix; ?> .items_pagination {
		<?php if ($pagination_position == 'title') : ?>
			display: inline-block;
			<?php if ($pagination_align == 'left' || $pagination_align == 'right') : ?>
				float: <?php echo $pagination_align; ?>;
			<?php endif; ?>
		<?php else : ?>
			display: block;
			text-align: <?php echo $pagination_align; ?>;
		<?php endif; ?>
		font-size: <?php echo $pagination_specific_size; ?>em;
		opacity: 0;
	}

	<?php echo $suffix; ?>.horizontal .items_pagination.left,
	<?php echo $suffix; ?>.horizontal .items_pagination.right {
		position: absolute;
		top: <?php echo $pagination_offset; ?>px;
		z-index: 100;
		text-align: center;
	}
	
	<?php echo $suffix; ?>.horizontal .items_pagination.left {
		left: 0;
	}

	<?php echo $suffix; ?>.horizontal .items_pagination.right {
		right: 0;
	}

	<?php echo $suffix; ?> .items_pagination.top,
	<?php echo $suffix; ?> .items_pagination.up {
		margin-bottom: <?php echo $pagination_offset; ?>px;
	}

	<?php echo $suffix; ?> .items_pagination.bottom,
	<?php echo $suffix; ?> .items_pagination.down {
		margin-top: <?php echo $pagination_offset; ?>px;
	}

	<?php echo $suffix; ?> .items_pagination ul {
		margin: 0;
		padding: 0;
	}

	<?php echo $suffix; ?> .items_pagination li {
		display: inline-block;
		list-style: none;
		cursor: pointer;
	}

		<?php echo $suffix; ?> .items_pagination a:hover,
		<?php echo $suffix; ?> .items_pagination a:focus {
			text-decoration: none;
		}

	<?php if ($symbols || $pages) : ?>

		<?php echo $suffix; ?> .items_pagination .pagenumbers {
			text-align: center;
			display: inline-block;
		}

		<?php echo $suffix; ?> .items_pagination.pagination .pagenumbers {
			display: inline;
		}

		<?php if ($symbols) : ?>
			<?php echo $suffix; ?> .items_pagination .pagenumbers a:before {
				font-family: 'SYWfont';
				speak: none;
				font-style: normal;
				font-weight: normal;
				font-variant: normal;
				text-transform: none;
				line-height: 1;

				/* Better Font Rendering */
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;

				content: "\e817";
			}

			<?php echo $suffix; ?> .items_pagination .pagenumbers .active a:before {
				content: "\e818";
			}
		<?php endif; ?>

		<?php echo $suffix; ?> .items_pagination .pagenumbers a {
			margin: 0 5px;
		}

		<?php echo $suffix; ?> .items_pagination.pagination .pagenumbers a,
		<?php echo $suffix; ?> .items_pagination .pagination .pagenumbers a,
		<?php echo $suffix; ?> .items_pagination .pagenumbers .pagination a {
			margin: 0;
		}

		<?php if (!$symbols) : ?>
			<?php echo $suffix; ?> .items_pagination .pagenumbers .active a {
				text-decoration: underline;
			}

			<?php echo $suffix; ?> .items_pagination.pagination .pagenumbers .active a {
				text-decoration: none;
			}
		<?php endif; ?>

		<?php if ($symbols) : ?>
			<?php echo $suffix; ?> .items_pagination .pagenumbers a span {
				display: none;
			}
		<?php endif; ?>
	<?php endif; ?>

	/* extra bootstrap 2 styles for 'around' positions */
	<?php if ($pagination_style && $bootstrap_version == 2) : ?>

		<?php if ($symbols || $pages) : ?>
    		<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers li > a,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers li > span {
        		float: left;
        		padding: 4px 12px;
        		line-height: 20px;
        		text-decoration: none;
        		background-color: #fff;
        		border: 1px solid #ddd;
        		border-left-width: 0;
        	}

        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers li > a:hover,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers li > a:focus,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .active > a,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .active > span {
        		background-color: #f5f5f5;
        	}

        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .active > a,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .active > span {
        		color: #999;
        		cursor: default;
        	}

        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .disabled > span,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .disabled > a,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .disabled > a:hover,
        	<?php echo $suffix; ?> .items_pagination.pagination ul .pagenumbers .disabled > a:focus {
        		color: #999;
        		background-color: transparent;
        		cursor: default;
        	}

            <?php echo $suffix; ?> .items_pagination.pagination-small ul .pagenumbers li > a,
        	<?php echo $suffix; ?> .items_pagination.pagination-small ul .pagenumbers li > span {
        		padding: 2px 10px;
        		font-size: 12px;
        	}

        	<?php echo $suffix; ?> .items_pagination.pagination-mini ul .pagenumbers li > a,
        	<?php echo $suffix; ?> .items_pagination.pagination-mini ul .pagenumbers li > span {
        		padding: 0 6px;
        		font-size: 9.75px;
        	}
        <?php endif; ?>

    	<?php echo $suffix; ?> .items_pagination.pagination.left ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination.left ul > li:first-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination.up ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination.up ul > li:first-child > span {
			border-right-width: 1px;
			-webkit-border-top-left-radius: 4px;
			-moz-border-radius-topleft: 4px;
			border-top-left-radius: 4px;
			-webkit-border-bottom-left-radius: 4px;
			-moz-border-radius-bottomleft: 4px;
			border-bottom-left-radius: 4px;
		}

    	<?php echo $suffix; ?> .items_pagination.pagination-mini.left ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.left ul > li:first-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.left ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.left ul > li:first-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.up ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.up ul > li:first-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.up ul > li:first-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.up ul > li:first-child > span {
			-webkit-border-top-left-radius: 3px;
			-moz-border-radius-topleft: 3px;
			border-top-left-radius: 3px;
			-webkit-border-bottom-left-radius: 3px;
			-moz-border-radius-bottomleft: 3px;
			border-bottom-left-radius: 3px;
		}

    	<?php echo $suffix; ?> .items_pagination.pagination.right ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination.right ul > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination.down ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination.down ul > li:last-child > span {
    		border-left-width: 1px;
			-webkit-border-top-right-radius: 4px;
			-moz-border-radius-topright: 4px;
			border-top-right-radius: 4px;
			-webkit-border-bottom-right-radius: 4px;
			-moz-border-radius-bottomright: 4px;
			border-bottom-right-radius: 4px;
		}

    	<?php echo $suffix; ?> .items_pagination.pagination-mini.right ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.right ul > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.right ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.right ul > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.down ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-mini.down ul > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.down ul > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.pagination-small.down ul > li:last-child > span {
			-webkit-border-top-right-radius: 3px;
			-moz-border-radius-topright: 3px;
			border-top-right-radius: 3px;
			-webkit-border-bottom-right-radius: 3px;
			-moz-border-radius-bottomright: 3px;
			border-bottom-right-radius: 3px;
		}
    <?php endif; ?>

    /* extra bootstrap 3 styles for 'around' positions */
    <?php if ($pagination_style && $bootstrap_version == 3) : ?>

    	<?php if ($symbols || $pages) : ?>
            <?php echo $suffix; ?> .pagination .pagenumbers li > a,
            <?php echo $suffix; ?> .pagination .pagenumbers li > span {
                position: relative;
                float: left;
                padding: 6px 12px;
                margin-left: -1px;
                line-height: 1.42857143;
                color: #337ab7;
                text-decoration: none;
                background-color: #fff;
                border: 1px solid #ddd;
            }

            <?php echo $suffix; ?> .pagination-sm .pagenumbers li > a,
            <?php echo $suffix; ?> .pagination-sm .pagenumbers li > span {
                padding: 5px 10px;
                font-size: 12px;
                line-height: 1.5;
            }

            <?php echo $suffix; ?> .pagination .pagenumbers li > a:focus,
            <?php echo $suffix; ?> .pagination .pagenumbers li > a:hover,
            <?php echo $suffix; ?> .pagination .pagenumbers li > span:focus,
            <?php echo $suffix; ?> .pagination .pagenumbers li > span:hover {
                z-index: 2;
                color: #23527c;
                background-color: #eee;
                border-color: #ddd;
            }

            <?php echo $suffix; ?> .pagination .pagenumbers > .active > a,
            <?php echo $suffix; ?> .pagination .pagenumbers > .active > a:focus,
           	<?php echo $suffix; ?> .pagination .pagenumbers > .active > a:hover,
            <?php echo $suffix; ?> .pagination .pagenumbers > .active > span,
            <?php echo $suffix; ?> .pagination .pagenumbers > .active > span:focus,
            <?php echo $suffix; ?> .pagination .pagenumbers > .active > span:hover {
                z-index: 3;
                color: #fff;
                cursor: default;
                background-color: #337ab7;
                border-color: #337ab7;
            }

            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > a,
            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > a:focus,
            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > a:hover,
            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > span,
            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > span:focus,
            <?php echo $suffix; ?> .pagination .pagenumbers > .disabled > span:hover {
                color: #777;
                cursor: not-allowed;
                background-color: #fff;
                border-color: #ddd;
            }
        <?php endif; ?>

    	<?php echo $suffix; ?> .items_pagination.left .pagination > li:first-child > a,
        <?php echo $suffix; ?> .items_pagination.left .pagination > li:first-child > span,
        <?php echo $suffix; ?> .items_pagination.up .pagination > li:first-child > a,
        <?php echo $suffix; ?> .items_pagination.up .pagination > li:first-child > span {
        	-webkit-border-top-right-radius: 4px;
        	-moz-border-radius-topright: 4px;
        	border-top-right-radius: 4px;
        	-webkit-border-bottom-right-radius: 4px;
        	-moz-border-radius-bottomright: 4px;
        	border-bottom-right-radius: 4px;
        }

        <?php echo $suffix; ?> .items_pagination.left .pagination-sm > li:first-child > a,
        <?php echo $suffix; ?> .items_pagination.left .pagination-sm > li:first-child > span,
        <?php echo $suffix; ?> .items_pagination.up .pagination-sm > li:first-child > a,
        <?php echo $suffix; ?> .items_pagination.up .pagination-sm > li:first-child > span {
        	-webkit-border-top-right-radius: 3px;
        	-moz-border-radius-topright: 3px;
        	border-top-right-radius: 3px;
        	-webkit-border-bottom-right-radius: 3px;
        	-moz-border-radius-bottomright: 3px;
        	border-bottom-right-radius: 3px;
        }

    	<?php echo $suffix; ?> .items_pagination.right .pagination > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.right .pagination > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.down .pagination > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.down .pagination > li:last-child > span {
    		-webkit-border-top-left-radius: 4px;
        	-moz-border-radius-topleft: 4px;
        	border-top-left-radius: 4px;
        	-webkit-border-bottom-left-radius: 4px;
        	-moz-border-radius-bottomleft: 4px;
        	border-bottom-left-radius: 4px;
    	}

    	<?php echo $suffix; ?> .items_pagination.right .pagination-sm > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.right .pagination-sm > li:last-child > span,
    	<?php echo $suffix; ?> .items_pagination.down .pagination-sm > li:last-child > a,
    	<?php echo $suffix; ?> .items_pagination.down .pagination-sm > li:last-child > span {
    		-webkit-border-top-left-radius: 3px;
        	-moz-border-radius-topleft: 3px;
        	border-top-left-radius: 3px;
        	-webkit-border-bottom-left-radius: 3px;
        	-moz-border-radius-bottomleft: 3px;
        	border-bottom-left-radius: 3px;
    	}
    <?php endif; ?>

    /* extra bootstrap 4 styles for 'around' positions */
    <?php if ($pagination_style && $bootstrap_version >= 4) : ?>

    	<?php if ($symbols || $pages) : ?>
            <?php echo $suffix; ?> .pagination .pagenumbers .page-item:first-child .page-link,
            <?php echo $suffix; ?> .pagination-sm .pagenumbers .page-item:first-child .page-link {
        	    border-top-left-radius: 0;
            	border-bottom-left-radius: 0;
        	}

            <?php echo $suffix; ?> .pagination .pagenumbers .page-item:last-child .page-link,
            <?php echo $suffix; ?> .pagination-sm .pagenumbers .page-item:last-child .page-link {
            	border-top-right-radius: 0;
            	border-bottom-right-radius: 0;
        	}
        <?php endif; ?>

        <?php echo $suffix; ?> .items_pagination.left .page-item:first-child .page-link,
        <?php echo $suffix; ?> .items_pagination.up .page-item:first-child .page-link {
        	-webkit-border-top-right-radius: .25rem;
        	-moz-border-radius-topright: .25rem;
        	border-top-right-radius: .25rem;
        	-webkit-border-bottom-right-radius: .25rem;
        	-moz-border-radius-bottomright: .25rem;
        	border-bottom-right-radius: .25rem;
        }

        <?php echo $suffix; ?> .items_pagination.left .pagination-sm .page-item:first-child .page-link,
        <?php echo $suffix; ?> .items_pagination.up .pagination-sm .page-item:first-child .page-link {
        	-webkit-border-top-right-radius: .2rem;
        	-moz-border-radius-topright: .2rem;
        	border-top-right-radius: .2rem;
        	-webkit-border-bottom-right-radius: .2rem;
        	-moz-border-radius-bottomright: .2rem;
        	border-bottom-right-radius: .2rem;
        }

        <?php echo $suffix; ?> .items_pagination.right .page-item:last-child .page-link,
        <?php echo $suffix; ?> .items_pagination.down .page-item:last-child .page-link {
        	-webkit-border-top-left-radius: .25rem;
        	-moz-border-radius-topleft: .25rem;
        	border-top-left-radius: .25rem;
        	-webkit-border-bottom-left-radius: .25rem;
        	-moz-border-radius-bottomleft: .25rem;
        	border-bottom-left-radius: .25rem;
        }

        <?php echo $suffix; ?> .items_pagination.right .pagination-sm .page-item:last-child .page-link,
        <?php echo $suffix; ?> .items_pagination.down .pagination-sm .page-item:last-child .page-link {
        	-webkit-border-top-left-radius: .2rem;
        	-moz-border-radius-topleft: .2rem;
        	border-top-left-radius: .2rem;
        	-webkit-border-bottom-left-radius: .2rem;
        	-moz-border-radius-bottomleft: .2rem;
        	border-bottom-left-radius: .2rem;
        }
    <?php endif; ?>

<?php endif; ?>