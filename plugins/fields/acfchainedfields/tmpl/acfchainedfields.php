<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$value = $field->value)
{
	return;
}

$value = array_values($value);

$layout = $fieldParams->get('layout', 'default');

// Default layout
if ($layout === 'default')
{
	?><div class="acf-chained-fields"><?php
	foreach ($value as $key => $_value)
	{
		if (!$_value)
		{
			continue;
		}
		
		$label = isset($field->choices['inputs'][$key]) ? $field->choices['inputs'][$key]['label'] : '';
		?>
		<div class="item">
			<strong><?php echo $label; ?></strong>: <?php echo $_value; ?>
		</div>
		<?php
	}
	?>
	</div>
	<?php
}
// Custom Layout
else
{
	if (!$layout = $fieldParams->get('custom_layout', null))
	{
		return;
	}

	// Make index start from 1
	array_unshift($value, '');
	unset($value[0]);
	
	// Create Smart Tags instance
	$st = new \NRFramework\SmartTags();

	$values = [];
	$labels = [];

	foreach ($value as $key => $_value)
	{
		$label_value = isset($field->choices['inputs'][$key - 1]) ? $field->choices['inputs'][$key - 1]['label'] : '';

		$labels[$key . '.label'] = $label_value;
		$values[$key . '.value'] = $_value;
	}
	$st->add($labels, 'field.');

	// Add values to Smart Tags
	$st->add($values, 'field.');

	// Replace Smart Tags
	echo $st->replace($layout);
}