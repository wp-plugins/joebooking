<?php
$cid = $_NTS['REQ']->getParam( 'customer_id' );
$t = $NTS_VIEW['t'];
$tm2 = ntsLib::getVar('admin::tm2');

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

/* ASSET */
$asset_id = $_NTS['REQ']->getParam( 'asset' );
$asset = array();
if( $asset_id )
{
	$aam =& ntsAccountingAssetManager::getInstance();
	$asset = $aam->get_asset_by_id( $asset_id );
}
if( $asset )
{
	/* set filters for time manager */
	$possible_filters = array( 'service', 'resource', 'weekday', 'time', 'date' );
	$set_gets = array( 'service', 'resource', 'weekday', 'time', 'date' );
	foreach( $possible_filters as $pf )
	{
		if( isset($asset[$pf]) && $asset[$pf] )
		{
			if( ! is_array($asset[$pf]) )
				$asset[$pf] = array( $asset[$pf] );
			$tm2->addFilter( $pf, $asset[$pf] );

			switch( $pf )
			{
				case 'service':
				case 'resource':
					$requested = $_NTS['REQ']->getParam($pf. '_id');
					if( ! strlen($requested) )
					{
						if( count($asset[$pf]) == 1 )
						{
							$_NTS['REQ']->setParam($pf. '_id', $asset[$pf][0]);
						}
					}
					break;
			}
		}
	}
}

/* END OF ASSET */

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
	'asset',
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

ntsLib::setVar('admin::tm2', $tm2);
?>