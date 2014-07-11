<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
?>

<div class="row">
	<div class="col-md-4 col-xs-12 pull-right">
		<ul class="list-inline pull-right">
			<li>
				ID: <?php echo $object->getId(); ?>
			</li>
			<li>
				<?php echo $object->statusLabel(); ?>
			</li>
		</ul>
	</div>

	<div class="col-md-8 col-xs-12">
		<h2><?php echo ntsView::objectTitle($object, TRUE); ?></h2>
	</div>
</div>