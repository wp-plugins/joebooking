<?php
$conf =& ntsConf::getInstance();

$key = $_NTS['REQ']->getParam( 'key' );
$currentlyDisabled = $conf->get( 'disabledNotifications' );
$currentlyDisabled[] = $key;
$conf->set( 'disabledNotifications', $currentlyDisabled );

ntsView::setAnnounce( M('Notification') . ': ' . M('Disable') . ': ' . M('OK'), 'ok' );

/* continue  */
$forwardTo = ntsLink::makeLink( '-current-/..' );
ntsView::redirect( $forwardTo );
exit;
?>