<?php
global $NTS_CURRENT_USER, $_NTS;

if( 
	preg_match('/manage$/', $_NTS['WAS_REQUESTED_PANEL']) OR
	preg_match('/admin$/', $_NTS['WAS_REQUESTED_PANEL']) OR
	(! $_NTS['WAS_REQUESTED_PANEL'])
	)
{
	$appView = ntsLib::getVar( 'admin/manage:appView' );
	if( $appView )
	{
		$redirectTo = 'admin/manage/calendar';
	}
	else
	{
		$redirectTo = 'admin/customers/browse';
	}
	$forwardTo = ntsLink::makeLink( $redirectTo );
	ntsView::redirect( $forwardTo );
	exit;
}
?>