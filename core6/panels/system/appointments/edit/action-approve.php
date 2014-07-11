<?php
require( dirname(__FILE__) . '/_action.php' );

$cm->runCommand( $object, 'approve' );
$msg = M('Appointment') . ': ' . M('Approved');
ntsView::addAnnounce( $msg, 'ok' );
ntsView::redirect( $forwardTo );
exit;
?>