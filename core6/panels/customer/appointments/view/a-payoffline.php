<?php
require( dirname(__FILE__) . '/_a_init_objects.php' );

$pgm =& ntsPaymentGatewaysManager::getInstance();
$has_offline = $pgm->hasOffline();

$msg = array();
if( count($objects) > 1 )
	$msg[] = M('Appointments');
else
	$msg[] = M('Appointment');
$msg[] = $has_offline;
$msg[] = M('OK');
$msg = join( ': ', $msg);
ntsView::addAnnounce( $msg, 'ok' );

$forwardTo = ntsLink::makeLink( '' );
ntsView::redirect( $forwardTo );
exit;
?>