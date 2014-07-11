<?php
require( dirname(__FILE__) . '/_action.php' );

$cm->runCommand( $object, 'reject' );
$msg = M('Appointment') . ': ' . M('Reject') . ': ' . M('OK');
ntsView::addAnnounce( $msg, 'ok' );
ntsView::redirect( $forwardTo );
exit;
?>