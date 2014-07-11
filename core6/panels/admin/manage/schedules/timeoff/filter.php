<?php
$schView = ntsLib::getVar( 'admin/manage:schView' );
$ress = ntsLib::getVar( 'admin::ress' );

if( ! ( $schView && array_intersect($ress, $schView) ) ){
	$msg = M('Timeoff') . ': ' . M('View') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );

	require( NTS_APP_DIR . '/views/error.php' );
	exit;
	}
?>