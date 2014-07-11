<?php
$cid = $_NTS['REQ']->getParam( 'customer_id' );
$t = $NTS_VIEW['t'];

/* ASSIGN CUSTOMER DEFAULTS */
if( $cid )
{
	$customer = new ntsUser();
	$customer->setId( $cid );

	$assign = $customer->getProp( '_assign_location');
	if( $assign )
	{
		$requested = $_NTS['REQ']->getParam('location_id');
		if( ! strlen($requested) )
		{
			$_NTS['REQ']->setParam('location_id', $assign[0]);
		}
	}

	$assign = $customer->getProp( '_assign_resource');
	if( $assign )
	{
		$requested = $_NTS['REQ']->getParam('resource_id');
		if( ! strlen($requested) )
		{
			$_NTS['REQ']->setParam('resource_id', $assign[0]);
		}
	}

	$assign = $customer->getProp( '_assign_service');
	if( $assign )
	{
		$requested = $_NTS['REQ']->getParam('service_id');
		if( ! strlen($requested) )
		{
			$_NTS['REQ']->setParam('service_id', $assign[0]);
		}
	}
}
/* END OF ASSIGN */

/* SAVE ON */
$session = new ntsSession;
$apps = $session->userdata('apps');
if( ! $apps )
{
	$apps = array();
}

$save_on = array();
$capture = array( 
	'location_id',
	'resource_id',
	'service_id',
	'customer_id',
	'cal',
	'starts_at',
//	'coupon',
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
ntsView::setPersistentParams( $save_on, 'admin/manage/appointments/create' );
?>