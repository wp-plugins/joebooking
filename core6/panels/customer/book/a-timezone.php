<?php
$current_user = ntsLib::getCurrentUser();
$timezoneSelected = $_NTS['REQ']->getParam( 'tz' );

$current_user->setProp('_timezone', $timezoneSelected );

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $current_user, 'update' );

/* get back to me */
$forwardTo = ntsLink::makeLink( '-current-' );
ntsView::redirect( $forwardTo );
exit;
?>