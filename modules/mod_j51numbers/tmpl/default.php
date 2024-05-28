<?php
/**
* J51_Numbers
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$baseurl  = JURI::base();
$document = JFactory::getDocument();

JHtml::_('behavior.core');
JHtml::_('stylesheet', 'mod_j51numbers/style.css', array('relative' => true, 'version' => 'auto'));

JHtml::_('script', 'mod_j51numbers/script.js', array('relative' => true, 'version' => 'auto'), array('type' => 'module'));
JHtml::_('script', 'mod_j51numbers/countUp.min.js', array('relative' => true, 'version' => 'auto'), array('type' => 'module'));


if (file_exists('media/j51_assets/js/noframework.waypoints.min.js')) { 
    JHtml::_('script', 'j51_assets/noframework.waypoints.min.js', array('relative' => true, 'version' => 'auto'));
} else { 
    JHtml::_('script', 'mod_j51numbers/noframework.waypoints.min.js', array('relative' => true, 'version' => 'auto'));
}

$document->addStyleDeclaration('
.j51_numbers'.$j51_moduleid.' .j51_numbers_item {
	flex: 0 0 '.$j51_columns.';
	max-width: '.$j51_columns.';
	padding: '.($j51_margin_y / 2).'px '.($j51_margin_x / 2).'px;
}
');
if ($module->showtitle) {
	$document->addStyleDeclaration('
		.j51_numbers'.$j51_moduleid.' {
			margin: 0 -'.($j51_margin_x / 2).'px;
		}
	');
} else {
	$document->addStyleDeclaration('
		.j51_numbers'.$j51_moduleid.' {
			margin: -'.($j51_margin_y / 2).'px -'.($j51_margin_x / 2).'px;
		}
	');
}
if (!empty($j51_number_color)) {
	$document->addStyleDeclaration('
		.j51_numbers'.$j51_moduleid.' .j51_numbers_number {
			color: '.$j51_number_color.' !important;
		}
	');
}
if (!empty($j51_title_color)) {
	$document->addStyleDeclaration('
		.j51_numbers'.$j51_moduleid.' .j51_number_title {
			color: '.$j51_title_color.';
		}
	');
}
if (!empty($j51_caption_color)) {
	$document->addStyleDeclaration('
		.j51_numbers'.$j51_moduleid.' .j51_number_caption {
			color: '.$j51_caption_color.';
		}
	');
}

// Pass data from PHP to JS
foreach ($j51_items as $item) {
    $dataObj = new stdClass();
    $dataObj->id = 'counter' . $j51_moduleid . 'i' . $id;
    $dataObj->number = 'number' . $j51_moduleid . 'i' . $id;
    $dataObj->counts = htmlspecialchars($item->j51_number, ENT_COMPAT, 'UTF-8');
    $dataObj->decimal = htmlspecialchars($item->j51_number_decimal, ENT_COMPAT, 'UTF-8');
    $dataObj->delay = $delay;
    $dataObj->group = filter_var($item->j51_group, FILTER_VALIDATE_BOOLEAN);
    $dataObj->group_sep = $item->j51_group_sep;
    $dataObj->decimal_sep = $item->j51_decimal_sep;
    $dataObj->animation_length = $j51_animation_length;
    $dataArray[] = $dataObj;

    $id++;
    $delay = $delay + $j51_interval_length;
}

if (!empty($document->getScriptOptions('j51_module_numbers'))) {
    $dataArray = array_merge($document->getScriptOptions('j51_module_numbers'), $dataArray);
}
$document->addScriptOptions('j51_module_numbers', $dataArray);

// reset the id/delay
$id = 1;
$delay = 0;
?>

<div class="j51_numbers j51_numbers<?php echo $j51_moduleid; ?>" >
<?php foreach ($j51_items as $item) : ?>
	<?php $numberid = $j51_moduleid.'i'.$id ?>
	<div id="number<?php echo $numberid ?>" class="j51_numbers_item j51_number_layout_<?php echo $j51_layout; ?> j51_numbers_align_<?php echo $j51_align; ?> animate" transition-delay="<?php echo $delay; ?>">
		<div class="j51_numbers_number text-primary">
			<?php if($item->j51_number_prefix  != "") : ?>
			<span class="j51_number_value">
				<?php echo htmlspecialchars($item->j51_number_prefix, ENT_COMPAT, 'UTF-8'); ?>
			</span>
			<?php endif; ?>
			<span id="counter<?php echo $numberid ?>" class="j51_number_value">
				<?php echo htmlspecialchars($item->j51_number, ENT_COMPAT, 'UTF-8'); ?>
			</span>
			<?php if($item->j51_number_suffix  != "") : ?>
			<span class="j51_number_value">
				<?php echo htmlspecialchars($item->j51_number_suffix, ENT_COMPAT, 'UTF-8'); ?>
			</span>
			<?php endif; ?>
		</div>
		<div class="j51_number_text">
			<?php if($item->j51_number_title  != "") : ?>
				<<?php echo $j51_title_tag; ?> class="j51_number_title">
					<?php echo htmlspecialchars($item->j51_number_title, ENT_COMPAT, 'UTF-8'); ?>
				</<?php echo $j51_title_tag; ?>>
			<?php endif; ?>
			<?php if($item->j51_number_caption  != "") : ?>
				<p class="j51_number_caption">
					<?php echo htmlspecialchars($item->j51_number_caption, ENT_COMPAT, 'UTF-8'); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php 
		$id++;
		$delay = $delay + $j51_interval_length; 
	?>
<?php endforeach ?>
</div>
