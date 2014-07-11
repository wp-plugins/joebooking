<H2><?php echo M('Resend Confirmation Code'); ?></H2>

<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );
?>
<?php $form->display(); ?>
