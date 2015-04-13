<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
?>
<div class="page-header">
	<h2>
	<?php if( $apps ) : ?>
		<?php echo M('Click Proceed Below To Confirm Appointment'); ?>
	<?php endif; ?>
	</h2>
</div>

<?php require( dirname(__FILE__) . '/../_index_confirm.php' ); ?>

<?php echo $NTS_VIEW['form']->display(); ?>