<?php
$fields = array(
	array( 'status',	M('Status') ),
	array( 'date',		M('Date') ),
	array( 'time',		M('Time') ),
	array( 'service',	M('Service') ),
	array( 'seats',		M('Seats') ),
	array( 'location',	M('Location') ),
	array( 'resource',	M('Bookable Resource') ),
	);

$headers = array();
reset( $fields );
foreach( $fields as $f )
	$headers[] = $f[1];
echo ntsLib::buildCsv( array_values($headers) );
echo "\n";

$t = $NTS_VIEW['t'];

reset( $objects );
foreach( $objects as $a ){
	$output = array();
	$output['status'] = $a->getProp('approved') ? M('Approved') : M('Pending');

	$t->setTimestamp( $a->getProp('starts_at') );
	$startsAt = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
	$output['starts_at'] = $startsAt;
	
	$serviceView = ntsView::appServiceView( $a );
	$serviceView = str_replace( "\n", " ", $serviceView );
	$output['service'] = $serviceView;

	$location = new ntsObject('location');
	$location->setId( $a->getProp('location_id') );
	$output['location'] = ntsView::objectTitle($location);

	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $a->getProp('resource_id') );
	$output['resource'] = ntsView::objectTitle($resource);

	$output = $a->dump( FALSE );

	$outLines = array();
	reset( $fields );
	foreach( $fields as $f ){
		$outLines[] = $output[ $f[0] ];
		}
	echo ntsLib::buildCsv( $outLines );
	echo "\n";
	}
?>