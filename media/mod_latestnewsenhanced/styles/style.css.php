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

<?php if ($animation) : ?>
	<?php echo $suffix; ?> + <?php echo $suffix; ?>_loader {
		text-align: center;
	}
<?php endif; ?>

	<?php echo $suffix; ?> ul.latestnews-items {
	    <?php if ($items_height) : ?>
	    	height: <?php echo $items_height; ?>px;
	    	overflow-y: auto;
	    <?php endif; ?>
	    <?php if ($items_width) : ?>
	    	<?php if ($item_width_unit == 'px') : ?>
	    		min-width: <?php echo $item_width; ?>px;
	    	<?php endif; ?>
	    	max-width: <?php echo $items_width; ?>px;
	    	margin-left: auto;
	    	margin-right: auto;
	    <?php endif; ?>

	    <?php if ($animation && $animation !== 'justpagination') : ?>

	    <?php else : ?>
	    	<?php if (!$use_leading) : ?>
		    	display: -webkit-box;
				display: -ms-flexbox;
				display: flex;

				-webkit-flex-wrap: wrap;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;

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

				<?php if (!$horizontal) : ?>
					-webkit-flex-direction: column;
					-ms-flex-direction: column;
					flex-direction: column;

					<?php if ($items_valign_col == 'fs') : ?>
				    	-webkit-box-align: start;
				    	-ms-flex-align: start;
				    	align-items: flex-start;
				    <?php elseif ($items_valign_col == 'fe') : ?>
				    	-webkit-box-align: end;
				    	-ms-flex-align: end;
				    	align-items: flex-end;
				    <?php elseif ($items_valign_col == 'c') : ?>
				    	-webkit-box-align: center;
				    	-ms-flex-align: center;
				    	align-items: center;
					<?php else : ?>
				    	-webkit-box-align: stretch;
				    	-ms-flex-align: stretch;
				    	align-items: stretch;
					<?php endif; ?>
		   		<?php else: ?>
			   		<?php if ($items_valign_row == 'fs') : ?>
				    	-webkit-box-align: start;
				    	-ms-flex-align: start;
				    	align-items: flex-start;
				    <?php elseif ($items_valign_row == 'fe') : ?>
				    	-webkit-box-align: end;
				    	-ms-flex-align: end;
				    	align-items: flex-end;
		    		<?php elseif ($items_valign_row == 'c') : ?>
				    	-webkit-box-align: center;
				    	-ms-flex-align: center;
				    	align-items: center;
		    		<?php else : ?>
		    			-webkit-box-align: stretch;
		    			-ms-flex-align: stretch;
		    			align-items: stretch;
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
	    <?php endif; ?>
	}

	<?php echo $suffix; ?> ul.latestnews-items li.latestnews-item {
		<?php if ($font_ref_body > 0) : ?>
			font-size: <?php echo $font_ref_body; ?>px;
		<?php else : ?>
			font-size: medium;
		<?php endif; ?>

		<?php if ($animation && $animation !== 'justpagination') : ?>

			/* margin: 0; needed ? */
			/* specifics for width in animation's stylesheet (some animations require width, other not) */

	    <?php else : ?>
	    	<?php if (!$use_leading) : ?>
		    	-webkit-box-flex: 1;
				-ms-flex: 1 1 auto;
				flex: 1 1 auto;
			<?php endif; ?>

	    	width: <?php echo $item_width; ?><?php echo $item_width_unit; ?>;

	    	<?php if ($item_min_width) : ?>
				min-width: <?php echo $item_min_width; ?>px;
			<?php endif; ?>
			<?php if ($item_max_width) : ?>
				max-width: <?php echo $item_max_width; ?>px;
			<?php endif; ?>

	    	<?php if ($item_width_unit == '%') : ?>
	    		margin: <?php echo intval($space_between_items / 2); ?>px <?php echo $margin_in_perc; ?>%;
	    	<?php else : ?>
	    		margin: <?php echo intval($space_between_items / 2); ?>px;
	    	<?php endif; ?>
	    <?php endif; ?>
	}

		<?php if ($animation && $animation !== 'justpagination') : ?>
			<?php if ($item_max_width) : ?>
				<?php echo $suffix; ?> .news {
					max-width: <?php echo $item_max_width; ?>px;
					margin: 0 auto;
				}
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($horizontal && $items_valign_row == 's') : ?>
			<?php echo $suffix; ?> .news {
				height: 100%;
			}
		<?php endif; ?>

			<?php if (($bgcolor_body && $bgcolor_body != 'transparent') || $border_width_body > 0 || $border_radius_body > 0 || $shadow_body != 'none' || $padding_body > 0 || $font_color_body) : ?>
				<?php echo $suffix; ?> .innernews {
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

						<?php if ($horizontal && $items_valign_row == 's') : ?>
							height: calc(100% - 12px);
						<?php endif; ?>

					<?php endif; ?>
					<?php if ($shadow_body == 'm') : ?>
						-webkit-box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14),0 1px 10px 0 rgba(0,0,0,0.12),0 2px 4px -1px rgba(0,0,0,0.3);
						box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14),0 1px 10px 0 rgba(0,0,0,0.12),0 2px 4px -1px rgba(0,0,0,0.3);
						margin: 11px;

						<?php if ($horizontal && $items_valign_row == 's') : ?>
							height: calc(100% - 22px);
						<?php endif; ?>

					<?php endif; ?>
					<?php if ($shadow_body == 'l') : ?>
						-webkit-box-shadow: 0 8px 17px 2px rgba(0,0,0,0.14),0 3px 14px 2px rgba(0,0,0,0.12),0 5px 5px -3px rgba(0,0,0,0.2);
						box-shadow: 0 8px 17px 2px rgba(0,0,0,0.14),0 3px 14px 2px rgba(0,0,0,0.12),0 5px 5px -3px rgba(0,0,0,0.2);
						margin: 27px;

						<?php if ($horizontal && $items_valign_row == 's') : ?>
							height: calc(100% - 54px);
						<?php endif; ?>

					<?php endif; ?>
					<?php if ($shadow_body == 'ss') : ?>
						-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
						box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
						margin: 5px;

						<?php if ($horizontal && $items_valign_row == 's') : ?>
							height: calc(100% - 10px);
						<?php endif; ?>

					<?php endif; ?>
					<?php if ($shadow_body == 'sm') : ?>
						-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
						box-shadow: 1px 1px 10px rgba(51, 51, 51, 0.2);
						margin: 11px;

						<?php if ($horizontal && $items_valign_row == 's') : ?>
							height: calc(100% - 22px);
						<?php endif; ?>

					<?php endif; ?>
					<?php if ($shadow_body == 'sl') : ?>
						-webkit-box-shadow: 1px 1px 4px rgba(51, 51, 51, 0.2);
						box-shadow: 1px 1px 15px rgba(51, 51, 51, 0.2);
						margin: 16px;

						<?php if ($horizontal && $items_valign_row == 's') : ?>
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
			<?php endif; ?>

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

			<?php if ($image || $video) : ?>

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

				<?php if (!$maintain_height) : ?>
					<?php echo $suffix; ?> .newshead .nopicture .over_head,
					<?php echo $suffix; ?> .newshead .novideo .over_head {
						position: relative;
					}
				<?php endif; ?>

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

				<?php echo $suffix; ?> .newshead .catlink.linkright {
					left: auto;
					right: 10px;
				}

				<?php echo $suffix; ?> .newshead .catlink.linkcenter,
				<?php echo $suffix; ?> .newshead .catlink.linkjustify {
					width: 100%;
					left: auto;
					right: auto;
					border-right: 10px solid transparent;
					border-left: 10px solid transparent;

					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
				}

				<?php echo $suffix; ?> .newshead .catlink.nostyle > div,
				<?php echo $suffix; ?> .newshead .catlink.nostyle > a {
					background-color: #fff;
					padding: 2px 4px;
				}

				<?php echo $suffix; ?> .newshead .catlink.nostyle.linkcenter > div {
					display: inline-block;
				}

				<?php echo $suffix; ?> .newshead .catlink.nostyle.linkjustify > a {
					display: block;
				}

				<?php echo $suffix; ?> .newshead .catlink .btn-link {
					background-color: #fff;
				}

			<?php endif; ?>

			<?php if ($image) : ?>

				<?php if ($head_width <= 0 || $head_height <= 0) : ?>
					<?php echo $suffix; ?> .newshead .picture {
						overflow: hidden;
						text-align: center;
						position: relative;
						width: max-content;
						max-width: 100%;
						<?php if ($bgcolor && $bgcolor != 'transparent') : ?>
							background-color: <?php echo $bgcolor; ?>;
						<?php endif; ?>
					}

					<?php echo $suffix; ?> .newshead .nopicture {
						display: none;
					}
				<?php else : ?>
					<?php echo $suffix; ?> .newshead .picture,
					<?php echo $suffix; ?> .newshead .nopicture {
						overflow: hidden;
						text-align: center;
						position: relative;
						max-width: <?php echo $head_width; ?>px;
						max-height: <?php echo $head_height; ?>px;
						<?php if ($maintain_height) : ?>
							height: <?php echo $head_height; ?>px;
							min-height: <?php echo $head_height; ?>px;
						<?php endif; ?>
						<?php if ($bgcolor && $bgcolor != 'transparent') : ?>
							background-color: <?php echo $bgcolor; ?>;
						<?php endif; ?>
					}

					<?php if ($maintain_height) : ?>
						<?php echo $suffix; ?> .newshead .nopicture > a span,
						<?php echo $suffix; ?> .newshead .nopicture > span {
							display: inline-block;
							width: <?php echo $head_width; ?>px;
							height: <?php echo $head_height; ?>px;
						}
					<?php endif; ?>
				<?php endif; ?>

				<?php echo $suffix; ?> .newshead .picture .innerpicture a,
				<?php echo $suffix; ?> .newshead .nopicture > a {
					text-decoration: none;
					display: inline-block;
					height: 100%;
    				width: 100%;
    				cursor: pointer;
				}

				<?php echo $suffix; ?> .newshead .picture .innerpicture a:hover,
				<?php echo $suffix; ?> .newshead .nopicture > a:hover {
					text-decoration: none;
				}

				<?php echo $suffix; ?> .newshead .picture img {
					max-width: 100%;
					max-height: 100%;
				}

			<?php endif; ?>

			<?php if ($calendar) : ?>

				<?php echo $suffix; ?> .newshead.calendartype {
					font-size: <?php echo $font_ref_cal; ?>px; /* the base size for the calendar */
				}

					<?php echo $suffix; ?> .newshead .nocalendar {
						width: <?php echo $head_width; ?>px;
						max-width: <?php echo $head_width; ?>px;
						height: <?php echo $head_height; ?>px;
						min-height: <?php echo $head_height; ?>px;
					}

					<?php echo $suffix; ?> .newshead .calendar {
						width: <?php echo $head_width; ?>px;
						max-width: <?php echo $head_width; ?>px;
					}

					<?php echo $suffix; ?> .newshead .calendar.image {
						height: <?php echo $head_height; ?>px;
					}

					<?php echo $suffix; ?> .newshead .calendar .position1,
					<?php echo $suffix; ?> .newshead .calendar .position2,
					<?php echo $suffix; ?> .newshead .calendar .position3,
					<?php echo $suffix; ?> .newshead .calendar .position4,
					<?php echo $suffix; ?> .newshead .calendar .position5 {
						display: block;
					}

			<?php endif; ?>

			<?php if ($video) : ?>

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

					<?php if ($maintain_height) : ?>
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

			<?php if ($icon) : ?>

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

					<?php echo $suffix; ?> .newshead .icon,
					<?php echo $suffix; ?> .newshead .noicon {
						<?php if ($icon_bgcolor && $icon_bgcolor != 'transparent') : ?>
							background-color: <?php echo $icon_bgcolor; ?>;
						<?php endif; ?>

						padding: <?php echo $icon_padding; ?>px;

						<?php if ($head_width > 0) : ?>
							width: <?php echo $head_width; ?>px;
						<?php endif; ?>
						<?php if ($head_height > 0) : ?>
							height: <?php echo $head_height; ?>px;
						<?php endif; ?>

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

						<?php if ($icon_text_shadow_width > 0) : ?>
							text-shadow: 0 0 <?php echo $icon_text_shadow_width; ?>px rgba(0, 0, 0, 0.8);
						<?php endif; ?>
					}

					<?php echo $suffix; ?> .newshead .noicon i {
						color: transparent;
					}

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

			<?php if ($content_align) : ?>
				<?php echo $suffix; ?> .newsinfooverhead .item_details .newsextra,
				<?php echo $suffix; ?> .newsinfooverhead .newstitle,
				<?php echo $suffix; ?> .newsinfo .item_details .newsextra,
				<?php echo $suffix; ?> .newsinfo .newstitle,
				<?php echo $suffix; ?> .newsinfo .newsintro {
					text-align: <?php echo $content_align; ?> !important;
				}
			<?php endif; ?>

					<?php if ($force_title_one_line) : ?>
						<?php echo $suffix; ?> .newstitle span {
			    			display: block;
			    			white-space: nowrap;
			    			text-overflow: ellipsis;
			    			overflow: hidden;
			    			line-height: initial;
						}
					<?php endif; ?>

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

						<?php if ($iconfont_color) : ?>
							<?php echo $suffix; ?> .newsextra .detail_icon {
							    color: <?php echo $iconfont_color; ?>;
							}
						<?php endif; ?>

						<?php if ($iconfont_color_overhead) : ?>
							<?php echo $suffix; ?> .over_head .newsextra .detail_icon {
							    color: <?php echo $iconfont_color_overhead; ?>;
							}
						<?php endif; ?>

						<?php echo $suffix; ?> .newsextra .detail_rating .detail_data .detail_icon {
							color: <?php echo $star_color; ?>;
						}

						<?php echo $suffix; ?> .detail_social .detail_data a svg {
							<?php if ($share_size[0]) : ?>
								width: <?php echo $share_size[0]; ?><?php echo $share_size[1]; ?>;
								height: <?php echo $share_size[0]; ?><?php echo $share_size[1]; ?>;
							<?php else : ?>
								width: 1.2em;
								height: 1.2em;
							<?php endif; ?>
						}

						<?php if ($share_bgcolor) : ?>
							<?php echo $suffix; ?> .newsextra .detail_social .detail_data a > i,
							<?php echo $suffix; ?> .newsextra .detail_social .detail_data a.inline_svg .svg_container {
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
