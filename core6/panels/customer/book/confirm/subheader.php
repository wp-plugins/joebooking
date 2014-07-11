<?php
$session = new ntsSession;
$apps = $session->userdata( 'apps' );
?>
<h2>
<?php if( $apps && (count($apps) > 1) ) : ?>
	<?php echo M('Confirm Appointments'); ?>
<?php else : ?>
	<?php echo M('Confirm Appointment'); ?>
<?php endif; ?>
</h2>