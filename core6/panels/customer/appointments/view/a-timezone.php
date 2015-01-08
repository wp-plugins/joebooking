<?php
$current_user = ntsLib::getCurrentUser();
$timezoneSelected = $_NTS['REQ']->getParam( 'tz' );

$current_user->setProp('_timezone', $timezoneSelected );
$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $current_user, 'update' );

/* get back to me */
/* redirect back to the referrer */
if( isset($_SERVER['HTTP_REFERER']) )
{
	$forwardTo = $_SERVER['HTTP_REFERER'];
}
else
{
	$forwardTo = ntsLink::makeLink( '-current-' );
}
ntsView::redirect( $forwardTo );
exit;
?>