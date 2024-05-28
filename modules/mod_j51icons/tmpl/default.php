<?php
/**
* J51_Icons
* Version		: 1.0
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

// Load CSS/JS
$document = JFactory::getDocument();
$document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/style.css' );

if ($j51_icon_set == 'ps7'){
  $document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/pe-icon-7-stroke.min.css' ); 
}

if ($j51_icon_set == 'typcn'){
  $document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/typicons.min.css' ); 
}

if ($j51_icon_set == 'mobirise'){
  $document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/mobirise.min.css' ); 
}

if ($j51_icon_set == 'dripicons-v2'){
  $document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/dripIcons-v2.css' ); 
}

if ($j51_icon_set == 'remixicon'){
  $document->addStyleSheet (JURI::base() . 'modules/mod_j51icons/css/remixicon.css' ); 
}


// Styling from module parameters
$document->addStyleDeclaration('
.j51-text-primary {
	color: var(--primary);
	color: var(--primary-color);
}
.j51_icons'.$j51_moduleid.' i,
.j51_icons'.$j51_moduleid.' [class^="fa-"]::before, 
.j51_icons'.$j51_moduleid.' [class*=" fa-"]::before {
	font-size: '.$j51_icon_size.'px;
}
.j51_icons'.$j51_moduleid.' img {
	max-width: '.$j51_icon_size.'px;
}
.j51_icons'.$j51_moduleid.' .j51_icon {
	flex: 0 0 '.$j51_icon_columns.';
	max-width: '.$j51_icon_columns.';
	min-height: '.$j51_circle_size.'px;
}
.j51_icons'.$j51_moduleid.' .j51_icon {
	padding: '.($j51_icon_margin_y / 2).'px '.($j51_icon_margin_x / 2).'px;
}
.j51_icons'.$j51_moduleid.' .boxed figure {
	background-color: '.$j51_bg_color.';
}
@media only screen and (min-width: 960px) and (max-width: 1280px) {
	.j51_icons'.$j51_moduleid.' .j51_icon {flex: 0 0 '.$j51_icon_columns_tabl.'; max-width: '.$j51_icon_columns_tabl.';}
}
@media only screen and (min-width: 768px) and (max-width: 959px) {
	.j51_icons'.$j51_moduleid.' .j51_icon {flex: 0 0 '.$j51_icon_columns_tabp.'; max-width: '.$j51_icon_columns_tabp.';}
}
@media only screen and ( max-width: 767px ) {
	.j51_icons'.$j51_moduleid.' .j51_icon {flex: 0 0 '.$j51_icon_columns_mobl.'; max-width: '.$j51_icon_columns_mobl.';}
}
@media only screen and (max-width: 440px) {
	.j51_icons'.$j51_moduleid.' .j51_icon {flex: 0 0 '.$j51_icon_columns_mobp.'; max-width: '.$j51_icon_columns_mobp.';}
}
');
if (!empty($j51_icon_color)) {
	$document->addStyleDeclaration('
		.j51_icons'.$j51_moduleid.' i {
			color: '.$j51_icon_color.';
		}
	');
} 

if ($module->showtitle) {
	$document->addStyleDeclaration('
		.j51_icons'.$j51_moduleid.' {
			margin: 0 -'.($j51_icon_margin_x / 2).'px;
		}
	');
} else {
	$document->addStyleDeclaration('
		.j51_icons'.$j51_moduleid.' {
			margin: -'.($j51_icon_margin_y / 2).'px -'.($j51_icon_margin_x / 2).'px;
		}
	');
}

if ($j51_icon_style !== 'none') {
	$document->addStyleDeclaration('
		.j51_icons'.$j51_moduleid.' i,
		.j51_icons'.$j51_moduleid.' img {
			background-color: '.$j51_icon_bg_color.';
			line-height: '.($j51_icon_size * 1.8).'px;
			width: '.($j51_icon_size * 1.8).'px;
			border: '.$j51_icon_border_size.'px solid;
		}
	');
	if (!empty($j51_icon_color)) {
		$document->addStyleDeclaration('
			.j51_icons'.$j51_moduleid.' i,
			.j51_icons'.$j51_moduleid.' img {
				background-color: '.$j51_icon_bg_color.';
				border-color: '.$j51_icon_border_color.';
			}
		');
	}
}
if ($j51_icon_align !== 'center' and $j51_icon_style == 'none') {
	$document->addStyleDeclaration('
		.j51_icons' . $j51_moduleid . ' i,
		.j51_icons' . $j51_moduleid . ' img {
			width: ' . $j51_icon_size . 'px;
		}
	');
}
if ($j51_icon_style == 'circle') {
	$document->addStyleDeclaration('
		.j51_icons' . $j51_moduleid . ' figure > i,
		.j51_icons' . $j51_moduleid . ' figure > img {
			border-radius: 50%;
			line-height: '.($j51_icon_size * 2.1).'px;
			width: '.($j51_icon_size * 2.1).'px;
		}
	');
}
if (!empty($j51_icon_max_width)) {
	$document->addStyleDeclaration('
		.j51_icons'.$j51_moduleid.' {
			max-width: '.$j51_icon_max_width.'px;
			margin-left: auto;
			margin-right: auto;
		}
	');
} 

?>

<div class="j51_icons j51_icons<?php echo $j51_moduleid; ?>" >

<?php foreach ($j51_items as $item) : ?>
	<div class="j51_icon j51_icon_layout_<?php echo $j51_icon_layout; ?> j51_icon_align_<?php echo $j51_icon_align; ?><?php if($item->j51_animate_class) { ?> animate <?php echo $item->j51_animate_class;?><?php } ?><?php if ($j51_bg_style == 'boxed') : echo ' boxed'; endif; ?>">
		<?php if($item->j51_iconurl != "") { ?>
		<a href="<?php echo htmlspecialchars($item->j51_iconurl, ENT_COMPAT, 'UTF-8'); ?>" target="<?php echo htmlspecialchars($item->j51_targeturl, ENT_COMPAT, 'UTF-8'); ?>">
		<?php } ?>
		<figure <?php if ($item->j51_override_style && !empty($item->j51_bg_color)) {echo 'style="background:'.$item->j51_bg_color.'"';}?>>
			<?php if($item->j51_type == "typeimage") { ?>
			<img src="<?php echo htmlspecialchars($item->j51_icon_image, ENT_COMPAT, 'UTF-8'); ?>" alt="icon">
			<?php } else { ?>
			<i class="<?php 
				if ($j51_icon_set == 'typcn') {
					echo 'typcn typcn-';
				}
				if ($j51_icon_set == 'bicon') {
					echo 'bi ';
				}
				if ($j51_icon_set == 'dripicons-v2') {
					echo 'dripicons-';
				}
				if ($j51_icon_set == 'remixicon') {
					echo 'ri-';
				}
				echo $item->j51_icon;
				if ($j51_icon_style !== 'none') { 
					echo ' background-primary';
				} 
				?> j51-text-primary j51_icon_style_<?php echo $j51_icon_style; ?>" 
				style="
					<?php if ($item->j51_override_style && !empty($item->j51_icon_color)) {echo 'color:'.$item->j51_icon_color.' !important;';}?>
					<?php if ($item->j51_override_style && !empty($item->j51_icon_bg_color) && $j51_icon_style !== 'none') {echo 'background:'.$item->j51_icon_bg_color.' !important;';}?>
					<?php if ($item->j51_override_style && !empty($item->j51_icon_border_color) && $j51_icon_style !== 'none') {echo 'border-color:'.$item->j51_icon_border_color.' !important;';}?>
				">
			</i>
			<?php } ?>
			<figcaption>
				<?php if($item->j51_icon_title  != "") : ?>
				<<?php echo $j51_title_tag; ?> class="j51_icon_title " <?php if ($item->j51_override_style && !empty($item->j51_icon_title_color)) {echo 'style="color:'.$item->j51_icon_title_color.' !important"';}?>><?php echo $item->j51_icon_title; ?></<?php echo $j51_title_tag; ?>>
				<?php endif; ?>
				<<?php echo $j51_caption_tag; ?> class="j51_icon_caption" <?php if ($item->j51_override_style && !empty($item->j51_icon_desc_color)) {echo 'style="color:'.$item->j51_icon_desc_color.'"';}?>><?php echo $item->j51_icon_desc; ?></<?php echo $j51_caption_tag; ?>>
			</figcaption>
		</figure>
		<?php if($item->j51_iconurl != "") { ?>
		</a>
		<?php } ?> 
	</div>
<?php endforeach ?>

</div>
