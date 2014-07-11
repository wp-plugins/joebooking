<?php
$current_filter = ntsLib::getVar( 'admin/manage:current_filter' );
$btn_width = '10em';
?>

<!-- SELECTORS -->
<?php if( count($locs_all) > 1 ) : ?>
	<?php if( isset($current_filter['l']) && $current_filter['l'] ) : ?>
		<?php
		$obj = ntsObjectFactory::get( 'location' );
		$obj->setId( $current_filter['l'] );
		?>
		<li class="active">
			<div class="btn-group">
				<span class="btn btn-default btn-info" style="width: <?php echo $btn_width; ?>; overflow: hidden;" title="<?php echo ntsView::objectTitle($obj); ?>">
					<?php echo ntsView::objectTitle($obj, TRUE); ?>
				</span>
				<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#ntsFilterLocation">
					<span class="caret"></span>
				</a>
			</div>
		</li>
	<?php else : ?>
		<li>
			<div class="btn-group">
				<span class="btn btn-default" style="width: <?php echo $btn_width; ?>; overflow: hidden;" title="<?php echo M('Location'); ?>">
					<?php echo ' - ' . M('Location') . ' - '; ?>
				</span>
				<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#ntsFilterLocation">
					<span class="caret"></span>
				</a>
			</div>
		</li>
	<?php endif; ?>
<?php endif; ?>

<?php if( count($ress_all) > 1 ) : ?>
	<?php if( isset($current_filter['r']) && $current_filter['r'] ) : ?>
		<?php
		$obj = ntsObjectFactory::get( 'resource' );
		$obj->setId( $current_filter['r'] );
		?>
		<li class="active">
			<div class="btn-group">
				<span class="btn btn-default btn-info" style="width: <?php echo $btn_width; ?>; overflow: hidden;" title="<?php echo ntsView::objectTitle($obj); ?>">
					<?php echo ntsView::objectTitle($obj, TRUE); ?>
				</span>
				<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#ntsFilterResource">
					<span class="caret"></span>
				</a>
			</div>
		</li>
	<?php else : ?>
		<li>
			<div class="btn-group">
				<span class="btn btn-default" style="width: <?php echo $btn_width; ?>; overflow: hidden;" title="<?php echo M('Bookable Resource'); ?>">
					<?php echo ' - ' . M('Bookable Resource') . ' - '; ?>
				</span>
				<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#ntsFilterResource">
					<span class="caret"></span>
				</a>
			</div>
		</li>
	<?php endif; ?>
<?php endif; ?>
<!-- END OF SELECTORS -->