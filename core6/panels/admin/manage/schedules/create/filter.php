<?php
$schEdit = ntsLib::getVar( 'admin/manage:schEdit' );
$ress = ntsLib::getVar( 'admin::ress' );

if( ! ( $schEdit && array_intersect($ress, $schEdit) ) ){
	$msg = M('Schedules') . ': ' . M('Edit') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );
	require( NTS_APP_DIR . '/views/error.php' );
	exit;
	}
?>