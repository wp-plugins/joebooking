<?php
$obj_class = $obj->getClassName();
$select_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		$obj_class . '_id' => $obj->getId(),
		)
	);
$reset_link = ntsLink::makeLink( 
	'-current-',
	'', 
	array(
		$obj_class . '_id' => '0',
		)
	);
require( dirname(__FILE__) . '/_object_dropdown.php' );
?>
<?php if( (! $link) && (! isset($no_reset)) ) : ?>
	<a class="close text-danger" href="<?php echo $reset_link; ?>" title="<?php echo M('Reset'); ?>">
		<i class="fa fa-times text-danger"></i>
	</a>
<?php endif; ?>
<ul class="list-unstyled list-separated">
	<li>
		<?php if( $link ) : ?>
			<?php if( $dropdown ) : ?>
				<div class="dropdown">
					<div class="squeeze-in">
						<span class="label label-<?php echo $alert_class; ?>">&nbsp;</span> 
						<a href="#" data-toggle="dropdown" class="dropdown-toggle" title="<?php echo ntsView::objectTitle($obj); ?>">
							<?php echo ntsView::objectTitle($obj); ?>
						</a>
						<?php echo Hc_html::dropdown_menu($dropdown); ?>
					</div>
				</div>
			<?php else : ?>
				<div class="squeeze-in">
					<span class="label label-<?php echo $alert_class; ?>">&nbsp;</span> 
					<a href="<?php echo $select_link; ?>" title="<?php echo ntsView::objectTitle($obj); ?>">
						<?php echo ntsView::objectTitle($obj); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<?php echo ntsView::objectTitle($obj, TRUE); ?>
		<?php endif; ?>
	</li>
	<li>
		<?php require( dirname(__FILE__) . '/_service_details.php' ); ?>
	</li>
</ul>