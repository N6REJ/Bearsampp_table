<?php
    $fontawesome_sw = $this->params->get('fontawesome_sw', 1);
    $fontawesome_solid = $this->params->get('fontawesome_solid', 1);
    $fontawesome_reg = $this->params->get('fontawesome_reg', 1);
    $fontawesome_brands = $this->params->get('fontawesome_brands', 1);
    $animatecss_sw = $this->params->get('animatecss_sw', 1);
?>

<noscript id="deferred-styles">
	<?php if ($animatecss_sw) { ?>
    <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/animate.min.css" rel="stylesheet">
	<?php } ?>
	
    <?php if ($fontawesome_sw) { ?>
    	<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome/fontawesome.min.css" rel="stylesheet">
        <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome/v4-shims.min.css" rel="stylesheet">
        <?php if ($fontawesome_brands) { ?>
            <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome/brands.min.css" rel="stylesheet">
        <?php } ?>
        <?php if ($fontawesome_reg) { ?>
            <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome/regular.min.css" rel="stylesheet">
        <?php } ?>
        <?php if ($fontawesome_solid) { ?>
            <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontawesome/solid.min.css" rel="stylesheet">
        <?php } ?>
    <?php } ?>
</noscript>
<script>
	var loadDeferredStyles = function() {
	var addStylesNode = document.getElementById("deferred-styles");
	var replacement = document.createElement("div");
	replacement.innerHTML = addStylesNode.textContent;
	document.body.appendChild(replacement)
	addStylesNode.parentElement.removeChild(addStylesNode);
	};
	var raf = requestAnimationFrame || mozRequestAnimationFrame ||
		webkitRequestAnimationFrame || msRequestAnimationFrame;
	if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
	else window.addEventListener('load', loadDeferredStyles);
</script>