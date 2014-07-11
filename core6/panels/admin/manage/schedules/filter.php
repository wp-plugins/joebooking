<?php
$schView = ntsLib::getVar( 'admin/manage:schView' );
$ress = ntsLib::getVar( 'admin::ress' );

if( ! ( $schView && array_intersect($ress, $schView) ) )
{
	$msg = M('Schedules') . ': ' . M('View') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );

	/* continue */
	$forwardTo = ntsLink::makeLink( '-current-/..' );
	ntsView::redirect( $forwardTo );
	exit;
}
?>