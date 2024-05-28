<?php
/**
* J51_Rotate
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* Licensed under the GPL v2&
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

$document = JFactory::getDocument();

// Load CSS/JS
HTMLHelper::_('stylesheet', 'mod_j51rotate/rotate.css', array('relative' => 'auto'));

if (file_exists('media/j51_assets/js/tiny-slider.min.js')) { 
    JHtml::_('script', 'j51_assets/tiny-slider.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
} else { 
    JHtml::_('script', 'mod_j51rotate/tiny-slider.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
}

if (file_exists('media/j51_assets/css/tiny-slider.min.css')) { 
    HTMLHelper::_('stylesheet', 'j51_assets/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto')); 
} else { 
    HTMLHelper::_('stylesheet', 'mod_j51rotate/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto'));
}

$document->addScriptDeclaration('
    document.addEventListener("DOMContentLoaded", () => {
        var slider = tns({
            container: ".j51rotate'.$j51_moduleid.'",
            items: 1,
            slideBy: "page",
            nav: false,
            autoplay: '.$j51_autoplay.',
            autoplayTimeout: '.$j51_autoplay_delay.',
            speed: '.$j51_trans_speed.',
            mouseDrag: true,
            controlsContainer: "#j51rotate'.$j51_moduleid.'-controls",
            autoplayButton: "#j51rotate'.$j51_moduleid.'-play",
            mouseDrag: true,
            slideBy: '.$j51_slideby.',
            axis: "'.$j51_axis.'",
            gutter: "'.$j51_gutter_size.'",
            responsive : {
                0 : {
                    items: '.$j51_columns_mobp.',
                },
                440 : {
                    items: '.$j51_columns_mobl.',
                },
                767 : {
                    items: '.$j51_columns_tabp.',
                },
                960 : {
                    items: '.$j51_columns_tabl.',
                },
                1280 : {
                    items: '.$j51_columns.',
                }
            },
        });
    });
'); 

// Styling from module parameters
$document->addStyleDeclaration('
	.j51rotate' . $j51_moduleid . ',
	.j51rotate' . $j51_moduleid . ' .tns-inner {
		margin: -' . ($j51_image_margin_y / 2) . 'px -' . ($j51_image_margin_x / 2) . 'px;
	}
	.j51rotate' . $j51_moduleid . ' .j51rotate-item {
		padding:' . ($j51_image_margin_y / 2) . 'px ' . ($j51_image_margin_x / 2) . 'px;
	}
');

if ($j51_random == "true") {
    $rotate_items = (array)$rotate_items;
    shuffle($rotate_items);
}

?>
<div class="j51rotate j51rotate<?php echo $j51_moduleid; ?>">
	<?php foreach ($rotate_items as $item) : ?>
        <div class="j51rotate-item">
            <?php echo $item->item; ?>
            <?php if (!empty($item->link)) { ?><a href="<?php echo $item->link; ?>" class="j51rotate-link" target="<?php echo $item->target; ?>" aria-label="Linked Item"></a><?php } ?>
        </div>
	<?php endforeach; ?>
</div>
<div id="j51rotate<?php echo $j51_moduleid; ?>-play" style="display:none;"></div>
<div class="j51rotate-nav" id="j51rotate<?php echo $j51_moduleid; ?>-controls">
    <a type="button" role="presentation" id="j51rotate-prev<?php echo $j51_moduleid; ?>" class="j51rotate-prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg></a><a type="button" role="presentation" id="j51imagehover-next<?php echo $j51_moduleid; ?>" class="j51imagehover-next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg></a>
</div>

