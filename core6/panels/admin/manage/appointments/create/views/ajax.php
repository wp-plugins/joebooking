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
<?php
require( dirname(__FILE__) . '/_index_time.php' );
?>