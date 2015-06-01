<?php
ntsView::setTitle( M('Admin Area') );

/* this file is here to indicate that the menu hierarchy starts here */

/* check permissions if admin */
if( ! ($NTS_CURRENT_USER->hasRole('admin')) ){
	$requestParams = $_NTS['REQ']->getGetParams();
	$returnPage = array(
		NTS_PARAM_PANEL		=> $_NTS['CURRENT_PANEL'],
		NTS_PARAM_ACTION	=> $requestParams,
		'params'	=> $requestParams,
		);
	$_SESSION['return_after_login'] = $returnPage;

	/* redirect to login page */
	$forwardTo = ntsLink::makeLink( 'anon/login', '', array('user' => 'admin') );
	ntsView::redirect( $forwardTo );
	exit;
	}

if( ! isset($_NTS['CURRENT_PANEL']) )
	$_NTS['CURRENT_PANEL'] = 'admin';

if( $_NTS['CURRENT_PANEL'] == 'admin/conf/upgrade' )
{
	return;
}

/* check if should run backup */
$conf =& ntsConf::getInstance();
$remindOfBackup = $conf->get('remindOfBackup');
$backupLastRun = $conf->get('backupLastRun'); 
$now = time();

if( $remindOfBackup ){
	if( (! $backupLastRun) || ( ($now - $backupLastRun) > $remindOfBackup ) ){
		if( $_NTS['CURRENT_PANEL'] != 'admin/conf/backup' ){
			$announceText = M("It seems that you have not made a backup for some time, it's highly recommended to do it now");
			$announceText .= '<br><a href="' . ntsLink::makeLink('admin/conf/backup') . '">' . M('Download Backup') . '</a>';
			ntsView::setAdminAnnounce( $announceText, 'alert' );
			}
		}
	}

if( $remindOfBackup ){
	if( (! $backupLastRun) || ( ($now - $backupLastRun) > $remindOfBackup ) ){
		if( $_NTS['CURRENT_PANEL'] != 'admin/conf/backup' ){
			$announceText = M("It seems that you have not made a backup for some time, it's highly recommended to do it now");
			$announceText .= '<br><a href="' . ntsLink::makeLink('admin/conf/backup') . '">' . M('Download Backup') . '</a>';
			ntsView::setAdminAnnounce( $announceText, 'alert' );
			}
		}
	}

/* CUSTOMER FIELDS */
$om2 =& objectMapper::getInstance();
$fields = $om2->getFields( 'customer', 'external' );
$customerFields = array();
$skip = array( 'first_name', 'last_name' );
foreach( $fields as $f ){
	if( ! in_array($f[0], $skip) )
		$customerFields[] = $f;
	}
$NTS_VIEW['CUSTOMER_FIELDS'] = $customerFields;

$t = new ntsTime();
global $NTS_CURRENT_USER;
$t->setTimezone( $NTS_CURRENT_USER->getTimezone() );
$NTS_VIEW['t'] = $t;

/* CURRENT USER PERMISSIONS */
$NTS_VIEW['APP_EDIT'] = array();
$NTS_VIEW['APP_VIEW'] = array();
$NTS_VIEW['SCH_EDIT'] = array();
$NTS_VIEW['SCH_VIEW'] = array();
$NTS_VIEW['ALL_RESS'] = array();

$appPermissions = $NTS_CURRENT_USER->getAppointmentPermissions();
reset( $appPermissions );
foreach( $appPermissions as $rid => $pa )
{
	if( $pa['view'] )
		$NTS_VIEW['APP_VIEW'][] = $rid;
	if( $pa['edit'] )
		$NTS_VIEW['APP_EDIT'][] = $rid;
	$NTS_VIEW['ALL_RESS'][] = $rid;
}
$schPermissions = $NTS_CURRENT_USER->getSchedulePermissions();
reset( $schPermissions );
foreach( $schPermissions as $rid => $pa )
{
	if( $pa['view'] )
		$NTS_VIEW['SCH_VIEW'][] = $rid;
	if( $pa['edit'] )
		$NTS_VIEW['SCH_EDIT'][] = $rid;
	$NTS_VIEW['ALL_RESS'][] = $rid;
}

/* ALL LRS */
$loadRes = array_unique( array_merge($NTS_VIEW['APP_VIEW'], $NTS_VIEW['SCH_VIEW']) );
$locs = ntsObjectFactory::getAllIds( 'location' );

$sers = ntsObjectFactory::getAllIds( 'service' );
if( count($sers) < 1 )
{
	$announceText = M('No Services Defined!');
	ntsView::setAdminAnnounce( $announceText, 'alert' );
}

if( count($locs) < 1 )
{
	$announceText = M('No Locations Defined!');
	ntsView::setAdminAnnounce( $announceText, 'alert' );
}

$filterRes = 'ALL_RESS';
if( $_NTS['CURRENT_PANEL'] == 'admin/schedules/create' ){
	$filterRes = 'SCH_EDIT';
	}
elseif( $_NTS['CURRENT_PANEL'] == 'admin/appointments/create' ){
	$filterRes = 'APP_EDIT';
	}
elseif( preg_match('/^admin\/schedules/', $_NTS['CURRENT_PANEL']) ){
	$filterRes = 'SCH_VIEW';
	}
elseif( preg_match('/^admin\/appointments/', $_NTS['CURRENT_PANEL']) ){
	$filterRes = 'APP_VIEW';
	$filterRes2 = 'SCH_VIEW';
	}
$NTS_VIEW['ALL_RESS'] = ( isset($filterRes2) && $filterRes2 ) ? array_merge($NTS_VIEW[$filterRes], $NTS_VIEW[$filterRes2]) : $NTS_VIEW[$filterRes];
$NTS_VIEW['ALL_RESS'] = array_unique( $NTS_VIEW['ALL_RESS'] );

/* INIT TIMEMANAGER */
ntsObjectFactory::preload( 'location', $locs );
ntsObjectFactory::preload( 'resource', $NTS_VIEW['ALL_RESS'] );
ntsObjectFactory::preload( 'service', $sers );

$returnTo = $_NTS['REQ']->getParam( NTS_PARAM_RETURN );
$returnToParams = array();
$saveOn = array();
if( $returnTo )
{
	$saveOn[ NTS_PARAM_RETURN ] = $returnTo;

	switch( $returnTo )
	{
		case 'confirm_app':
			$returnTo = 'admin/manage/appointments/create/confirm';
			break;
		case 'merge':
			$returnTo = 'admin/customers/edit/merge';
			break;
	}
}
ntsLib::setVar( 'admin::returnTo', $returnTo ); 
ntsLib::setVar( 'admin::returnToParams', $returnToParams ); 
ntsView::setPersistentParams( $saveOn, 'admin' );

$ress = array();
$appEdit = array();
$appView = array();
$appPermissions = $NTS_CURRENT_USER->getAppointmentPermissions();
//_print_r( $appPermissions );
//exit;


reset( $appPermissions );
foreach( $appPermissions as $rid => $pa )
{
	if( $pa['view'] || $pa['edit'] )
		$ress[] = $rid; 
	if( $pa['edit'] )
		$appEdit[] = $rid;
	if( $pa['view'] )
		$appView[] = $rid;
}

$schEdit = array();
$schView = array();
$schPermissions = $NTS_CURRENT_USER->getSchedulePermissions();
reset( $schPermissions );
foreach( $schPermissions as $rid => $pa )
{
	if( $pa['view'] || $pa['edit'] )
		$ress[] = $rid; 
	if( $pa['edit'] )
		$schEdit[] = $rid;
	if( $pa['view'] )
		$schView[] = $rid;
}
$ress = array_unique( $ress );

$tm2 = new haTimeManager2();
$tm2->checkNow = 0;
$tm2->setResource( $ress );

/* if this admin is staff only then allow only configured locations and services */
$current_user =& ntsLib::getCurrentUser();
$level = $current_user->getProp( '_admin_level' );
if( $level == 'staff' )
{
	$tm2->setResource( $ress );

	$staff_locs = array();
	$staff_sers = array();
	$lrss = $tm2->getLrs( TRUE );
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

$tm2->setLocation( $locs );
$tm2->setService( $sers );

$locs_all = $locs;
$ress_all = $ress;
$sers_all = $sers;

if( $schView || $appView )
{
	$lrss = $tm2->getLrs();
	reset( $lrss );
	foreach( $lrss as $lrs )
	{
		if( ! in_array($lrs[0], $locs_all) )
			$locs_all[] = $lrs[0];
		if( ! in_array($lrs[1], $ress_all) )
			$ress_all[] = $lrs[1];
		if( ! in_array($lrs[2], $sers_all) )
			$sers_all[] = $lrs[2];
	}
}


/* check filter */
$filterParam = $_NTS['REQ']->getParam( 'nts-filter' );
$allowedFilter = array('l', 'r', 's', 'c');
$filterParam = explode( '-', $filterParam );
$filter = array();
foreach( $filterParam as $fp ){
	$fclass = trim(substr( $fp, 0, 1 ));
	$fid = trim(substr( $fp, 1 ));
	if( ! in_array($fclass, $allowedFilter) )
		continue;
	if( ! preg_match('/^[\d]*$/', $fid) )
		continue;

	switch( $fclass ){
		case 'l':
			if( ! in_array($fid, $locs) )
				$fp = '';
			else
				$locs = array( $fid );
			break;
		case 'r':
			/* not allowed */
			if( ! in_array($fid, $ress) )
				$fp = '';
			else
				$ress = array( $fid );
			break;
		case 's':
			if( ! in_array($fid, $sers) )
				$fp = '';
			else
				$sers = array( $fid );
			break;
		}

	if( $fp )
		$filter[] = $fp;
	}
$filterParam = join( '-', $filter );
ntsLib::setVar( 'admin/manage:filter', $filter );

$saveOn = array();
$saveOn['nts-filter'] = $filterParam;

ntsLib::setVar( 'admin/manage:appEdit', $appEdit );
ntsLib::setVar( 'admin/manage:schEdit', $schEdit );
ntsLib::setVar( 'admin/manage:appView', $appView );
ntsLib::setVar( 'admin/manage:schView', $schView );

global $NTS_CURRENT_USER;
if( (! $schView) && (! $schEdit) )
{
	$NTS_CURRENT_USER->setDisabledPanel( 'admin/manage/schedules' );
}

if( (! $appView) && (! $appEdit) )
{
	$NTS_CURRENT_USER->setDisabledPanel( 'admin/customers/edit/appointments' );
	$NTS_CURRENT_USER->setDisabledPanel( 'admin/manage/calendar' );
}

$tm2->setResource( $ress );
$tm2->setLocation( $locs );
$tm2->setService( $sers );

/* sort ress2 */
require( dirname(__FILE__) . '/filter_lrs.php' );

$current_filter = array();
for( $fi = 0; $fi < count($filter); $fi++ )
{
	$fp = $filter[$fi];
	$fclass = substr( $fp, 0, 1 );
	$fid = substr( $fp, 1 );
	$current_filter[ $fclass ] = $fid;
}

if( isset($current_filter['r']) && $current_filter['r'] )
{
	if( count($locs2) == 1 )
	{
		$current_filter['l'] = $locs2[0];
	}
}

if( 
	( isset($current_filter['l']) && $current_filter['l'] )
	)
{
	if( count($ress2) == 1 )
		$current_filter['r'] = $ress2[0];
}
ntsLib::setVar( 'admin/manage:current_filter', $current_filter );

/* all that i can view */
ntsLib::setVar( 'admin::ress_all', $ress_all );
ntsLib::setVar( 'admin::locs_all', $locs_all );
ntsLib::setVar( 'admin::sers_all', $sers_all );

/* archived resources */
$ress_archive = ntsObjectFactory::getIds( 
	'resource', 
	array(
		'archive'	=> array( '=', 1 ),
		)
	);
ntsLib::setVar( 'admin::ress_archive', $ress_archive );

/* archived locations */
$locs_archive = ntsObjectFactory::getIds( 
	'location', 
	array(
		'archive'	=> array( '=', 1 ),
		)
	);
ntsLib::setVar( 'admin::locs_archive', $locs_archive );

ntsLib::setVar( 'admin::tm2', $tm2 );

ntsView::setPersistentParams( $saveOn, 'admin/manage' );

$appointment = ntsObjectFactory::get( 'appointment' );
?>