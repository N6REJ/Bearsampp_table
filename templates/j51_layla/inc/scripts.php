<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

// jQUery
$wa->useScript('template.jquery');

// MMenu
$wa->useScript('template.mmenu');

// Animate on Scroll
$wa->useScript('template.waypoints');

if($sidecol_responsive_pos == 'after') {
    // Responsive stacking order
    $document->addStyleDeclaration('
    @media only screen and (max-width: 767px) {
        .sidecol_a, .sidecol_b {
            order: 1 !important;
        }
    }
    ');
}

$wa->useScript('template.masonry');

if ($this->params->get('sticky_sw')) {
    // Sticky Div
    $wa->useScript('template.sticky');
    $document->addScriptDeclaration('
        jQuery(window).on("load", function(){
            jQuery("#container_header").sticky({ 
                topSpacing: 0
            });
        });
    ');
}

// Load scripts.js
$document->addScriptOptions('j51_template', array(
    'scrolltoOffset' => $this->params->get('scrollto_offset', -55),
    'mobileMenuPosition' => $this->params->get('mobile_menu_position', 'left'),
    'mobileMenuTitle' => $this->params->get('mobile_menu_title', 'Menu'),
));
$wa->useScript('template.script');
