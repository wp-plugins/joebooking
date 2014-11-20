<?php
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );
$objId = $object->getId();
?>
<H2><?php echo M('Are you sure?'); ?></H2>
<?php if( $NTS_VIEW['appsCount'] ) : ?>
	<p>
	<b><?php echo ntsView::objectTitle($object); ?></b>: 
	<?php echo M('Appointments'); ?>: <?php echo $NTS_VIEW['appsCount']; ?>
	<p>
	<?php echo M('If you proceed, these appointments will be cancelled' ); ?>.
<?php endif; ?>

<p>
<?php echo $this->makePostParams('-current-', 'delete' ); ?>
<input class="btn btn-danger" type="submit" VALUE="<?php echo M('Delete'); ?>">
