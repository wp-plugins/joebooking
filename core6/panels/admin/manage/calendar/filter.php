<?php
$appView = ntsLib::getVar( 'admin/manage:appView' );
$ress = ntsLib::getVar( 'admin::ress' );

if( ! ( $appView && array_intersect($ress, $appView) ) )
{
	$msg = M('Appointments') . ': ' . M('View') . ': ' . M('Permission Denied');
	ntsView::addAnnounce( $msg, 'error' );

	/* continue */
	$forwardTo = ntsLink::makeLink( '' );
	ntsView::redirect( $forwardTo );
	exit;
}
?>