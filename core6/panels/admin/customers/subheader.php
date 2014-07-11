<?php
$returnTo = ntsLib::getVar('admin::returnTo');
?>
<h2>
	<?php if( $returnTo ) : ?>
		<?php echo M('Customer'); ?>: <?php echo M('Select'); ?>
	<?php else : ?>
		<i class="fa fa-user"></i> <?php echo M('Customers'); ?>
	<?php endif; ?>
</h2>