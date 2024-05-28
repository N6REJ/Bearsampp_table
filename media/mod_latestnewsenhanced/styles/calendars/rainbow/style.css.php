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
<?php if ($cal_shadow_width > 0) : ?>
	<?php echo $suffix; ?> .newshead.calendartype {
		padding: <?php echo (intval($cal_shadow_width) + 2); ?>px;

   		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
	}
<?php endif; ?>

	<?php echo $suffix; ?> .newshead .calendar {
		color: <?php echo $color; ?>;
		font-family: Arial, Helvetica, sans-serif;
		font-size: <?php echo $font_ratio; ?>em;
		text-align: center;

		<?php if ($cal_shadow_width > 0) : ?>
			box-shadow: 0 0 <?php echo $cal_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			-moz-box-shadow: 0 0 <?php echo $cal_shadow_width; ?>px rgba(0, 0, 0, 0.8);
			-webkit-box-shadow: 0 0 <?php echo $cal_shadow_width; ?>px rgba(0, 0, 0, 0.8);
		<?php endif; ?>
	}

	<?php echo $suffix; ?> .newshead .calendar.noimage {
		background-color: <?php echo $bgcolor1; ?>;
	}

		<?php echo $suffix; ?> .newshead .calendar.noimage .position1 {
		    background-color: <?php echo $bgcolor1_top; ?>;
		}

		<?php echo $suffix; ?> .newshead .calendar .empty {
		    line-height: 0.6em;
		}

		<?php echo $suffix; ?> .newshead .calendar .position1 {
		    font-size: 1.2em !important;
			font-weight: bold;
		    line-height: 1.6em !important;
		    text-transform: uppercase !important;
		    letter-spacing: 0.2em;
		    text-indent: 0.2em;
			color: <?php echo $color_top; ?>;
		}

		html[dir="rtl"] <?php echo $suffix; ?> .newshead .calendar .position1 {
			text-indent: -0.2em;
		}

		<?php echo $suffix; ?> .newshead .calendar .weekday {
			font-size: 0.8em;
		    line-height: 1em;
		    text-transform: capitalize;
		}

		<?php echo $suffix; ?> .newshead .calendar .month {
			font-size: 0.7em;
			font-weight: bold;
			line-height: 1.1em;
			text-transform: uppercase;
		}

		<?php echo $suffix; ?> .newshead .calendar .day {
			font-size: 2.4em;
			font-weight: bold;
			line-height: 0.8em;
		}

		<?php echo $suffix; ?> .newshead .calendar .year {
			font-size: 0.7em;
			letter-spacing: 0.35em;
		    text-indent: 0.35em;
			line-height: 1em;
		}

		html[dir="rtl"] <?php echo $suffix; ?> .newshead .calendar .year {
			text-indent: -0.35em;
		}

		<?php echo $suffix; ?> .newshead .calendar .time {
			font-size: 0.7em;
			letter-spacing: 0.1em;
		    text-indent: 0.1em;
			line-height: 1em;
		}

		html[dir="rtl"] <?php echo $suffix; ?> .newshead .calendar .time {
			text-indent: -0.1em;
		}