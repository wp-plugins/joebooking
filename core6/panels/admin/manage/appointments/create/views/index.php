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

<?php foreach( $to_display as $d ) : ?>
	<?php 
	require( dirname(__FILE__) . '/_index_' . $d . '.php' );
	?>
<?php endforeach; ?>

<?php foreach( $to_select as $d ) : ?>
	<?php if( $d == 'time' ) : ?>
		<div class="nts-ajax-container" style="display: block;">
	<?php endif; ?>
	<?php
	require( dirname(__FILE__) . '/_index_' . $d . '.php' );
	?>
	<?php if( $d == 'time' ) : ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

