<?php
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );
$tm2 = ntsLib::getVar( 'admin::tm2' );
$ntsdb =& dbWrapper::getInstance();

$sers = array();
$locs = array();
$ress = array();
$tm2->setService( $object->getId() );
$lrss = $tm2->getLrs();
reset( $lrss );
foreach( $lrss as $lrs )
{
	if( ! in_array($lrs[0], $locs) )
		$locs[] = $lrs[0];
	if( ! in_array($lrs[1], $ress) )
		$ress[] = $lrs[1];
	if( ! in_array($lrs[2], $sers) )
		$sers[] = $lrs[2];
}
$sers_count = $ntsdb->count( 'services' );
$locs_count = $ntsdb->count( 'locations' );
$ress_count = $ntsdb->count( 'resources' );
?>
<div class="row">
	<div class="col-sm-7">
		<?php $NTS_VIEW['form']->display(); ?>
	</div>

	<div class="col-sm-5">
		<?php if( ! $ress ) : ?>
			<p class="alert alert-danger">
				<?php echo M('No availability configured'); ?>
			</p>
		<?php endif; ?>

		<?php if( $ress && ($ress_count > 1) ) : ?>
			<h4><?php echo M('Bookable Resources'); ?> [<?php echo count($ress); ?>/<?php echo $ress_count; ?>]</h4>
			<ul class="list-unstyled">
				<?php foreach( $ress as $rid ) : ?>
					<li>
						<?php
						$obj = ntsObjectFactory::get('resource');
						$obj->setId( $rid );
						?>
						<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if( $locs && ($locs_count > 1) ) : ?>
			<h4><?php echo M('Locations'); ?> [<?php echo count($locs); ?>/<?php echo $locs_count; ?>]</h4>
			<ul class="list-unstyled">
				<?php foreach( $locs as $lid ) : ?>
					<li>
						<?php
						$obj = ntsObjectFactory::get('location');
						$obj->setId( $lid );
						?>
						<?php echo ntsView::objectTitle( $obj, TRUE ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<p>
			<a href="<?php echo ntsLink::makeLink('admin/manage/schedules'); ?>" class="btn btn-default">
				<i class="fa fa-bar-chart-o fa-fw"></i><?php echo M('Availability'); ?>
			</a>
		</p>
	</div>
</div>