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

	<?php echo $suffix; ?> .newshead .calendar {
		color: <?php echo $color; ?>;
		font-family: Arial, Helvetica, sans-serif;
		font-size: <?php echo $font_ratio; ?>em;
	}

	<?php echo $suffix; ?> .newshead .calendar.noimage {

		background: <?php echo $bgcolor1; ?>; /* Old browsers */

		<?php if ($bgcolor1 != $bgcolor2) : ?>
			background: -moz-linear-gradient(left, <?php echo $bgcolor1; ?> 0%, <?php echo $bgcolor2; ?> 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, right top, color-stop(0%,<?php echo $bgcolor1; ?>), color-stop(100%,<?php echo $bgcolor2; ?>)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(left, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(left, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* Opera11.10+ */
			background: -ms-linear-gradient(left, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* IE10+ */

			<?php if ($bgcolor1 == 'transparent') : ?>
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $bgcolor2; ?>', endColorstr='<?php echo $bgcolor2; ?>',GradientType=1 ); /* IE6-9 (IE9 cannot use SVG because the colors are dynamic) */
			<?php elseif ($bgcolor2 == 'transparent') : ?>
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $bgcolor1; ?>', endColorstr='<?php echo $bgcolor1; ?>',GradientType=1 ); /* IE6-9 (IE9 cannot use SVG because the colors are dynamic) */
			<?php else : ?>
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $bgcolor1; ?>', endColorstr='<?php echo $bgcolor2; ?>',GradientType=1 ); /* IE6-9 (IE9 cannot use SVG because the colors are dynamic) */
			<?php endif; ?>

			background: linear-gradient(to right, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* W3C */
		<?php endif; ?>
	}

	<?php echo $suffix; ?> .head_left .newshead .calendar.noimage {
		border-right: 1px solid <?php echo $color; ?>;
	}

	<?php echo $suffix; ?> .head_right .newshead .calendar.noimage {
		border-left: 1px solid <?php echo $color; ?>;
	}

	<?php echo $suffix; ?> .head_left .newshead .calendar {
		text-align: right;
		padding-right: 5px;
		margin-right: 5px;
	}

	<?php echo $suffix; ?> .head_right .newshead .calendar {
		text-align: left;
		padding-left: 5px;
		margin-left: 5px;
	}

		<?php echo $suffix; ?> .newshead .calendar .empty {
		    line-height: 0.6em;
		}

		<?php echo $suffix; ?> .newshead .calendar .weekday {
			font-size: 0.8em;
			line-height: 1.1em;
			letter-spacing: 0.2em;
		}

		<?php echo $suffix; ?> .head_left .newshead .calendar .weekday {
			margin-right: -0.2em;
		}

		<?php echo $suffix; ?> .newshead .calendar .month {
			font-size: 0.8em;
			line-height: 1.1em;
			font-weight: bold;
			letter-spacing: 0.45em;
			text-transform: uppercase;
		}

		<?php echo $suffix; ?> .head_left .newshead .calendar .month {
			margin-right: -0.45em;
		}

		<?php echo $suffix; ?> .newshead .calendar .day {
			font-size: 1.8em;
			line-height: 1em;
			font-weight: bold;
			letter-spacing: 0.1em;
		}

		<?php echo $suffix; ?> .head_left .newshead .calendar .day {
			margin-right: -0.1em;
		}

		<?php echo $suffix; ?> .newshead .calendar .year {
			font-size: 0.7em;
			line-height: 1.2em;
			letter-spacing: 0.35em;
		}

		<?php echo $suffix; ?> .head_left .newshead .calendar .year {
			margin-right: -0.35em;
		}

		<?php echo $suffix; ?> .newshead .calendar .time {
			font-size: 0.7em;
			line-height: 1.2em;
			letter-spacing: 0.1em;
		}

		<?php echo $suffix; ?> .head_left .newshead .calendar .time {
			margin-right: -0.1em;
		}
