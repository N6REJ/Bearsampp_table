<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );
 ?>

<?php if($this->params->get('hornavPosition') == '1') : ?>
    <nav id="hornav-nav" class="hornav">
        <jdoc:include type="modules" name="hornav" />
    </nav>
<?php else : ?>
    <nav id="hornav-nav" class="hornav">
        <?php echo $hornav; ?>
    </nav>
<?php endif; ?>
