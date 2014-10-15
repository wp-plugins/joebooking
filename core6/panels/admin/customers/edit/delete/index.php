<?php
$id = $NTS_VIEW['id'];
$customerAppointmentsCount = $NTS_VIEW['customerAppointmentsCount'];
$customerOrderCount = $NTS_VIEW['customerOrderCount'];

$ff =& ntsFormFactory::getInstance();
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm' );
?>
<?php if( is_array($id) && (count($id) > 1) ) : ?>
	<H2>Do you really want to delete these <?php echo count($id); ?> users?</H2>
	<p>
	<?php if ( $customerAppointmentsCount OR $customerOrderCount ) : ?>
	<p>
	Please note that there are also <?php echo $customerAppointmentsCount; ?> appointment(s) associated with this customers. They <b>will also be deleted</b> if your delete the customer accounts.
	<?php endif; ?>
<?php else : ?>
	<span class="text-danger"><?php echo M('Are you sure?'); ?></span>
	<p>
	<?php if ( $customerAppointmentsCount OR $customerOrderCount ) : ?>
	<p>
<?php 		if( $customerAppointmentsCount ) : ?>
				<?php echo M('Appointments'); ?>: <a href="<?php echo ntsLink::makeLink('-current-/../appointments'); ?>"><?php echo $customerAppointmentsCount; ?></a><br>
<?php 		endif; ?>
<?php 		if( $customerOrderCount ) : ?>
				<?php echo M('Package Orders'); ?>: <a href="<?php echo ntsLink::makeLink('-current-/../orders'); ?>"><?php echo $customerOrderCount; ?></a><br>
<?php 		endif; ?>
			<p>They <b>will also be deleted</b> if your delete the customer account. No notifications will be sent.
	<?php endif; ?>
<?php endif; ?>

<ul class="list-inline list-hori-separated">
	<li>
		<?php
		$confirmForm->display();
		?>
	</li>
	<li>
		<a class="btn btn-default btn-sm" href="javascript:history.go(-1);"><?php echo M('Cancel'); ?></A>
	</li>
</ul>