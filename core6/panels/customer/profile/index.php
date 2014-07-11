<div class="page-header">
	<H2><?php echo M('My Profile'); ?></H2>
</div>


<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>

<h3><?php echo M('Change Password'); ?></h3>
<?php
$passwordForm =& $ff->makeForm( dirname(__FILE__) . '/passwordForm' );
$passwordForm->display();
?>