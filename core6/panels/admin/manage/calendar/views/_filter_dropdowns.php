<?php
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive ){
	$ress2 = array_diff( $ress2, $ress_archive );
	$ress2 = array_values( $ress2 );
}
?>
<?php if( count($locs_all) > 1 ) : ?>
	<?php
	$thisFilter_NoLocation = array();
	foreach( $current_filter as $fk => $fv )
	{
		if( $fk == 'l' )
			continue;
		$thisFilter_NoLocation[] = $fk . $fv;
	}
	?>
	<div class="dropdown" id="ntsFilterLocation">
		<ul class="dropdown-menu dropdown-menu-hori">

			<?php if( isset($current_filter['r']) && $current_filter['r'] ) : ?>
				<?php if( (count($locs2) > 1) && (count($locs2) < count($locs_all)) ) : ?>
					<li class="dropdown-header">
						<?php
						$obj = ntsObjectFactory::get( 'resource' );
						$obj->setId( $current_filter['r'] );
						?>
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</li>

					<?php if( isset($current_filter['l']) && $current_filter['l'] ) : ?>
						<?php
						$myFilter = $thisFilter_NoLocation;
						?>
						<li>
							<a title="<?php echo ' - ' . M('All') . ' - '; ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
								<i class="fa fa-home"></i> <?php echo ' - ' . M('All') . ' - '; ?>
							</a>
						</li>
					<?php endif; ?>

					<?php foreach( $locs2 as $obj_id ) : ?>
						<?php
						if( isset($current_filter['l']) && $current_filter['l'] == $obj_id )
							continue;
						$myFilter = array_merge( $thisFilter_NoLocation, array('l' . $obj_id) );
						$myFilter = array_unique($myFilter);
						$obj = ntsObjectFactory::get( 'location' );
						$obj->setId( $obj_id );
						?>
						<li>
							<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
								<?php echo ntsView::objectTitle($obj, TRUE); ?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if( isset($current_filter['l']) && $current_filter['l'] && (count($ress_all) > 1) ) : ?>
				<li class="dropdown-header">
					<i class="fa fa-hand-o-up"></i> <?php echo ' - ' . M('All') . ' - '; ?>
				</li>
			<?php endif; ?>

			<?php if( isset($current_filter['l']) && $current_filter['l'] ) : ?>
				<?php
				$myFilter = array();
				?>
				<li>
					<a title="<?php echo ' - ' . M('All') . ' - '; ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
						<i class="fa fa-home"></i> <?php echo ' - ' . M('All') . ' - '; ?>
					</a>
				</li>
			<?php endif; ?>

			<?php foreach( $locs_all as $obj_id ) : ?>
				<?php
				if( isset($current_filter['l']) && $current_filter['l'] == $obj_id )
					continue;
				if( isset($current_filter['r']) && $current_filter['r'] && (count($locs2) < count($locs_all)) )
				{
					if( in_array($obj_id, $locs2) )
						continue;
				}
				$myFilter = array('l' . $obj_id);
				$myFilter = array_unique($myFilter);
				$obj = ntsObjectFactory::get( 'location' );
				$obj->setId( $obj_id );
				?>
				<li>
					<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if( count($ress_all) > 1 ) : ?>
	<?php
	$thisFilter_NoResource = array();
	foreach( $current_filter as $fk => $fv )
	{
		if( $fk == 'r' )
			continue;
		$thisFilter_NoResource[] = $fk . $fv;
	}
	?>
	<div class="dropdown" id="ntsFilterResource">
		<ul class="dropdown-menu dropdown-menu-hori">
			<?php if( isset($current_filter['l']) && $current_filter['l'] ) : ?>
				<?php if( (count($ress2) > 1) && (count($ress2) < count($ress_all)) ) : ?>
					<li class="dropdown-header">
						<?php
						$obj = ntsObjectFactory::get( 'location' );
						$obj->setId( $current_filter['l'] );
						?>
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</li>

					<?php if( isset($current_filter['r']) && $current_filter['r'] ) : ?>
						<?php
						$myFilter = $thisFilter_NoResource;
						?>
						<li>
							<a title="<?php echo ' - ' . M('All') . ' - '; ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
								<i class="fa fa-hand-o-up"></i> <?php echo ' - ' . M('All') . ' - '; ?>
							</a>
						</li>
					<?php endif; ?>

					<?php foreach( $ress2 as $obj_id ) : ?>
						<?php
						if( isset($current_filter['r']) && $current_filter['r'] == $obj_id )
							continue;
						$myFilter = array_merge( $thisFilter_NoResource, array('r' . $obj_id) );
						$myFilter = array_unique($myFilter);
						$obj = ntsObjectFactory::get( 'resource' );
						$obj->setId( $obj_id );
						?>
						<li>
							<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
								<?php echo ntsView::objectTitle($obj, TRUE); ?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if( isset($current_filter['l']) && $current_filter['l'] && (count($locs_all) > 1) ) : ?>
				<li class="dropdown-header">
					<i class="fa fa-home"></i> <?php echo ' - ' . M('All') . ' - '; ?>
				</li>
			<?php endif; ?>

			<?php if( isset($current_filter['r']) && $current_filter['r'] ) : ?>
				<?php
				$myFilter = array();
				?>
				<li>
					<a title="<?php echo ' - ' . M('All') . ' - '; ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
						<i class="fa fa-hand-o-up"></i> <?php echo ' - ' . M('All') . ' - '; ?>
					</a>
				</li>
			<?php endif; ?>

			<?php foreach( $ress_all as $obj_id ) : ?>
				<?php
				if( isset($current_filter['r']) && $current_filter['r'] == $obj_id )
					continue;
				if( isset($current_filter['l']) && $current_filter['l'] && (count($ress2) < count($ress_all)) )
				{
					if( in_array($obj_id, $ress2) )
						continue;
				}
				$myFilter = array('r' . $obj_id);
				$myFilter = array_unique($myFilter);
				$obj = ntsObjectFactory::get( 'resource' );
				$obj->setId( $obj_id );
				?>
				<li>
					<a title="<?php echo ntsView::objectTitle($obj); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('nts-filter' => join('-', $myFilter) )); ?>">
						<?php echo ntsView::objectTitle($obj, TRUE); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>