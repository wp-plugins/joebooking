<?php
global $NTS_CURRENT_USER, $_NTS;

if( 
	preg_match('/manage$/', $_NTS['WAS_REQUESTED_PANEL']) OR
	preg_match('/admin$/', $_NTS['WAS_REQUESTED_PANEL']) OR
	(! $_NTS['WAS_REQUESTED_PANEL'])
	)
{
	$redirectTo = 'admin/manage/calendar';
	$forwardTo = ntsLink::makeLink( $redirectTo );
	ntsView::redirect( $forwardTo );
	exit;
}
?>