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

/* archived locations */
$locs_archive = ntsObjectFactory::getIds( 
	'location',
	array(
		'archive'	=> array( '=', 1 ),
		)
	);
if( $locs_archive )
{
	$locs = ntsObjectFactory::getAllIds( 'location' );
	$locs = array_diff( $locs, $locs_archive );
	$locs = array_values( $locs );
	$tm2->addFilter( 'location', $locs );
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

/* ASSETS */
$asset = array();
if( isset($params['asset']) && $params['asset'] )
{
	$asset_id = $params['asset'];
	$aam =& ntsAccountingAssetManager::getInstance();
	$asset = $aam->get_asset_by_id( $asset_id );
}

if( $asset )
{
	/* check if this customer can access this asset */
	$am =& ntsAccountingManager::getInstance();
	$current_user_id = $current_user->getId();
	$balance = $am->get_balance( 'customer', $current_user_id );

	$now = time();
	$can_use_this = FALSE;
	foreach( $balance as $b_asset_key => $b_asset_value )
	{
		if( $b_asset_value == 0 )
		{
			continue;
		}
		list( $b_asset_id, $b_asset_expires ) = explode( '-', $b_asset_key );
		if( $b_asset_id != $asset_id )
		{
			continue;
		}
		if( $b_asset_expires && ($b_asset_expires < $now) )
		{
			continue;
		}
		$can_use_this = TRUE;
		break;
	}
	if( ! $can_use_this )
	{
		$msg = join( ': ', array(M('Package'), M('Not Available')) );
		ntsView::addAnnounce( $msg, 'error' );
		$asset = array();
	}
}

if( $asset )
{
	/* set filters for time manager */
	$possible_filters = array( 'service', 'resource', 'weekday', 'time', 'date' );
	foreach( $possible_filters as $pf )
	{
		if( isset($asset[$pf]) && $asset[$pf] )
		{
			if( ! is_array($asset[$pf]) )
				$asset[$pf] = array( $asset[$pf] );
			$tm2->addFilter( $pf, $asset[$pf] );
		}
	}
}
/* END OF ASSETS */


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