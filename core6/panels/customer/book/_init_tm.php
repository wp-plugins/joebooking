<?php
global $current_user;
$current_user = ntsLib::getCurrentUser();

$t = $NTS_VIEW['t'];
$tm2 = new haTimeManager2();
$tm2->customerT = $t;

if( isset($current_user) && $current_user->hasRole('admin') )
	$tm2->customerSide = false;
else
{
	$tm2->customerSide = true;
}

if( isset($current_user) && (! $current_user->hasRole('admin')) )
{
	$tm2->customerId = $current_user->getId();
}

/* archived resources */
$ress_archive = ntsObjectFactory::getIds( 
	'resource', 
	array(
		'archive'	=> array( '=', 1 ),
		)
	);
if( $ress_archive )
{
	$ress = ntsObjectFactory::getAllIds( 'resource' );

	$ress = array_diff( $ress, $ress_archive );
	$ress = array_values( $ress );
	$tm2->addFilter( 'resource', $ress );
}

if( isset($GLOBALS['NTS_FIX_RESOURCE']) )
{
	$tm2->addFilter( 'resource', $GLOBALS['NTS_FIX_RESOURCE'] );
}

if( isset($GLOBALS['NTS_FIX_SERVICE']) )
{
	$tm2->addFilter( 'service', $GLOBALS['NTS_FIX_SERVICE'] );
}
if( isset($GLOBALS['NTS_FIX_LOCATION']) )
{
	$tm2->addFilter( 'location', $GLOBALS['NTS_FIX_LOCATION'] );
}

/* check if the customer has assigned resource and service */
$assign = $current_user->getProp( '_assign_location');
$assign_only = $current_user->getProp( '_assign_location_only');
if( $assign_only )
{
	$tm2->addFilter( 'location', $assign );
}
elseif( $assign )
{
	$requested = $_NTS['REQ']->getParam('location');
	if( ! strlen($requested) )
	{
		$_NTS['REQ']->setParam('location', $assign[0]);
	}
}

$assign = $current_user->getProp( '_assign_resource');
$assign_only = $current_user->getProp( '_assign_resource_only');
if( $assign_only )
{
	$tm2->addFilter( 'resource', $assign );
}
elseif( $assign )
{
	$requested = $_NTS['REQ']->getParam('resource');
	if( ! strlen($requested) )
	{
		$_NTS['REQ']->setParam('resource', $assign[0]);
	}
}

$assign = $current_user->getProp( '_assign_service');
$assign_only = $current_user->getProp( '_assign_service_only');
if( $assign_only )
{
	$tm2->addFilter( 'service', $assign );
}
elseif( $assign )
{
	$requested = $_NTS['REQ']->getParam('service');
	if( ! strlen($requested) )
	{
		$_NTS['REQ']->setParam('service', $assign[0]);
	}
}
/* END OF ASSIGN */

$order = NULL;
if( $order )
{
/* set filters for time manager */
	/* services */
	$filter = $order->getFilter( 'service' );
	if( $filter )
	{
		$tm2->addFilter( 'service', $filter );
	}

	/* resources */
	$filter = $order->getFilter( 'resource' );
	if( $filter )
	{
		$tm2->addFilter( 'resource', $filter );
	}

	/* weekdays */
	$filter = $order->getFilter( 'weekday' );
	if( $filter )
	{
		$tm2->addFilter( 'weekday', $filter );
	}

	/* time */
	$filter = $order->getFilter( 'time' );
	if( $filter )
	{
		$tm2->addFilter( 'time', $filter );
	}

	/* date */
	$filter = $order->getFilter( 'date' );
	if( $filter )
	{
		$tm2->addFilter( 'date', $filter );
	}
}

$lid = $_NTS['REQ']->getParam('location');
if( $lid )
	$tm2->setLocation( $lid );

$rid = $_NTS['REQ']->getParam('resource');
if( $rid )
	$tm2->setResource( $rid );

$sid = $_NTS['REQ']->getParam('service');
if( $sid )
	$tm2->setService( $sid );

$NTS_VIEW['tm2'] = $tm2;
?>