<?php
$to_select = array();
$to_display = array();

if( $cid )
{
	$to_display[] = 'customer';
}
else
{
//	$to_select[] = 'customer';
}

if( count($locs) > 1 )
	$lid ? $to_display[] = 'location' : $to_select[] = 'location';
if( count($ress) > 1 )
	$rid ? $to_display[] = 'resource' : $to_select[] = 'resource';
if( count($sers) > 1 )
	$sid ? $to_display[] = 'service' : $to_select[] = 'service';

$starts_at ? $to_display[] = 'time' : $to_select[] = 'time';

$a = array();
if( $cid )
	$a['customer_id'] = $cid;
if( $lid )
	$a['location_id'] = $lid;
if( $rid )
	$a['resource_id'] = $rid;
if( $sid )
	$a['service_id'] = $sid;
if( $starts_at )
	$a['starts_at'] = $starts_at;
?>

<div class="page-header">
	<?php if( $cart ) : ?>
		<div class="pull-right">
			<a class="btn btn-default" href="<?php echo ntsLink::makeLink('-current-/confirm'); ?>">
				<i class="fa fa-fw fa-shopping-cart"></i><?php echo count($cart); ?>
			</a>
		</div>
	<?php endif; ?>
	<h2><?php echo M('New Appointment'); ?></h2>
</div>

<?php if( $asset_id ) : ?>
	<?php
	$aam =& ntsAccountingAssetManager::getInstance();
	$valid_for_view = $aam->asset_view(
		$asset_id,
		TRUE, // html
		array('location', 'resource', 'service'), //just
		array() // skip
		);
	$when_view = $aam->asset_view(
		$asset_id, 
		TRUE,
		array(),
		array('location', 'resource', 'service', 'expires_in')
		);
	?>

	<div class="alert alert-archive-o">
		<a class="close text-danger" href="<?php echo ntsLink::makeLink('-current-', '', array('asset' => '-reset-')); ?>" title="<?php echo M('Reset'); ?>">
			<i class="fa fa-times text-danger"></i>
		</a>

		<ul class="list-unstyled">
			<li>
				<?php echo M('Use Balance'); ?>
			</li>
			<li class="divider"></li>
			<li>
				<ul class="list-unstyled">
				<?php if( $valid_for_view ) : ?>
					<?php foreach( $valid_for_view as $av ) : ?>
						<li>
							<ul class="list-inline">
								<li>
									<?php echo $av[0]; ?>
								</li>
								<li>
									<ul class="list-unstyled">
										<?php foreach( $av[1] as $av2 ) : ?>
											<li>
												<?php echo $av2; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</li>
							</ul>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if( $when_view ) : ?>
					<?php foreach( $when_view as $av ) : ?>
						<li>
							<ul class="list-inline">
								<li style="vertical-align: top;">
									<?php echo $av[0]; ?>
								</li>
								<li>
									<ul class="list-unstyled">
										<?php foreach( $av[1] as $av2 ) : ?>
											<li>
												<?php echo $av2; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</li>
							</ul>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
				</ul>
			</li>
		</ul>
	</div>
<?php endif; ?>

<?php foreach( $to_display as $d ) : ?>
	<?php 
	require( dirname(__FILE__) . '/_index_' . $d . '.php' );
	?>
<?php endforeach; ?>

<?php foreach( $to_select as $d ) : ?>
	<?php if( $d == 'time' ) : ?>
		<div class="nts-ajax-container-no" style="display: block;">
	<?php endif; ?>
	<?php
	require( dirname(__FILE__) . '/_index_' . $d . '.php' );
	?>
	<?php if( $d == 'time' ) : ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

