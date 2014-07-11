<?php
$save_on = array();
$capture = array( 
	'location_id',
	'resource_id',
	'service_id',
	'cal',
	'starts_at',
	);
reset( $capture );
foreach( $capture as $c )
{
	$value = $_NTS['REQ']->getParam( $c );
	if( $value )
	{
		$save_on[$c] = $value;
	}
}
ntsView::setPersistentParams( $save_on, 'admin/manage/appointments/edit/change' );
?>