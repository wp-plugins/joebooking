<?php
$groupId = $_NTS['REQ']->getParam( 'gid' );
$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$tm2 = ntsLib::getVar('admin::tm2');

$blocks = $tm2->getBlocksByGroupId( $groupId );

reset( $blocks );
$resIds = array();
foreach( $blocks as $thisBlock ){
	$resId = $thisBlock['resource_id'];
	break;
	}

$iCanEdit = in_array($resId, $schEdit );
if( ! $iCanEdit ){
	$msg = M('Schedules') . ': ' . M('Edit') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );
	require( NTS_APP_DIR . '/views/error.php' );
	exit;
	}
?>