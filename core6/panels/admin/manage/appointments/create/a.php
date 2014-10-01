<?php
$t = $NTS_VIEW['t'];

/* init my apps in session */
$session = new ntsSession;
$apps = $session->userdata('apps');
if( ! $apps )
{
	$apps = array();
}

$cal = $_NTS['REQ']->getParam( 'cal' );
if( ! $cal )
{
	$t->setNow();
	$cal = $t->formatDate_Db();
}

/* INIT LRS */
$locs = ntsLib::getVar( 'admin::locs' );
/* check out archived locations */
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );
if( $locs_archive )
{
	$locs = array_diff( $locs, $locs_archive );
	$locs = array_values( $locs );
}

$ress = ntsLib::getVar( 'admin::ress' );
/* check out archived resources */
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress = array_diff( $ress, $ress_archive );
	$ress = array_values( $ress );
}

/* check resources that I can edit appointments */
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$ress = array_intersect( $ress, $appEdit );
$ress = array_values( $ress );

$sers = ntsLib::getVar( 'admin::sers' );

/* if this admin is staff only then allow only configured locations and services */
/*
$current_user =& ntsLib::getCurrentUser();
$level = $current_user->getProp( '_admin_level' );
if( $level == 'staff' )
{
	$tm2 = ntsLib::getVar('admin::tm2');
	$tm2->setResource( $ress );

	$staff_locs = array();
	$staff_sers = array();
	$lrss = $tm2->getLrs( TRUE, $cal );
	foreach( $lrss as $lrs )
	{
		$staff_locs[ $lrs[0] ] = 1;
		$staff_sers[ $lrs[2] ] = 1;
	}
	$staff_locs = array_keys( $staff_locs );
	$staff_sers = array_keys( $staff_sers );

	$locs = array_intersect( $locs, $staff_locs );
	$locs = array_values( $locs );
	$sers = array_intersect( $sers, $staff_sers );
	$sers = array_values( $sers );
}
*/

$lid = $_NTS['REQ']->getParam( 'location_id' );
if( (! $lid) && (count($locs) == 1) )
	$lid = $locs[0];

$rid = $_NTS['REQ']->getParam( 'resource_id' );
if( (! $rid) && (count($ress) == 1) )
	$rid = $ress[0];

$sid = $_NTS['REQ']->getParam( 'service_id' );
if( (! $sid) && (count($sers) == 1) )
	$sid = $sers[0];

$cid = $_NTS['REQ']->getParam( 'customer_id' );
$starts_at = $_NTS['REQ']->getParam( 'starts_at' );

/* check if customer has defaults */
if( $cid )
{
	$customer = new ntsUser;
	$customer->setId( $cid );
}

/* check to select */
$all_ready = TRUE;
$to_select = array( 'lid', 'rid', 'sid', 'starts_at' );
foreach( $to_select as $tosel )
{
	if( ! ${$tosel} )
	{
		$all_ready = FALSE;
		break;
	}
}

if( $all_ready )
{
	/* save in session */
	$this_app = array(
		'location_id'	=> $lid,
		'resource_id'	=> $rid,
		'service_id'	=> $sid,
		'starts_at'		=> $starts_at,
		);
	$apps[] = $this_app;

	require( dirname(__FILE__) . '/_parse_apps.php' );
	$session->set_userdata( 'apps', $apps );

	/* choose customer ? */
	if( ! $cid )
	{
		/* redirect to confirm */
		$params = array(
			NTS_PARAM_RETURN	=> 'confirm_app',
			);
		$skip = $_NTS['REQ']->getParam('skip');
		if( $skip )
		{
			$params['skip'] = $skip;
		}
		$forwardTo = ntsLink::makeLink(
			'admin/customers/browse',
			'',
			$params
			);
	}
	else
	{
		/* redirect to confirm */
		$forwardTo = ntsLink::makeLink(
			'-current-/confirm',
			'',
			array(
				'starts_at'	=> '-reset-',
				)
			);
	}
	ntsView::redirect( $forwardTo );
	exit;
}

$t = $NTS_VIEW['t'];
$t->setDateDb( $cal );

/* INIT TM */
$tm2 = ntsLib::getVar('admin::tm2');
$tm2->customerId = $cid;
if( $lid )
	$tm2->setLocation( $lid );
if( $rid )
	$tm2->setResource( $rid );
if( $sid )
	$tm2->setService( $sid );

/* set virtual appointments */
reset( $apps );
foreach( $apps as $a )
{
	$tm2->addVirtualAppointment( $a );
}

$start_month = $t->getStartMonth();
$end_month = $t->getEndMonth();

$tm2->init( $start_month, $end_month );
ntsLib::setVar('admin::tm2', $tm2);


/* ASSET */
$asset_id = $_NTS['REQ']->getParam( 'asset' );

$check_locs = $lid ? array($lid) : $locs;
$check_ress = $rid ? array($rid) : $ress;
$check_sers = $sid ? array($sid) : $sers;

/* CHECK AVAILABILITY */
require( dirname(__FILE__) . '/_build_availability.php' );

/* VIEW */
$view = array();
$view = array(
	'cid'	=> $cid,
	'locs'	=> $locs,
	'lid'	=> $lid,
	'ress'	=> $ress,
	'rid'	=> $rid,
	'sers'	=> $sers,
	'sid'	=> $sid,
	'cal'	=> $cal,
	'starts_at'	=> $starts_at,
	'available'	=> $available,
	'all_times'	=> $all_times,
	'cart'		=> $apps,
	'asset_id'		=> $asset_id,
	);

$is_ajax = ntsLib::isAjax();
$view_file = $is_ajax ? 'ajax.php' : 'index.php';

$this->render(
	dirname(__FILE__) . '/views/' . $view_file,
	$view
	);
?>